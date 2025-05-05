<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    exit(0);
}

require_once __DIR__ . '/../db.php';
$db = new Database();
$conn = $db->getConnexion();
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupère toutes les notes utilisateur
        $user_id = $_SESSION['user_id'];
        $req = $conn->prepare('SELECT n.*, GROUP_CONCAT(t.name) as tags
            FROM notes n
            LEFT JOIN note_tags nt ON n.id = nt.note_id
            LEFT JOIN tags t ON nt.tag_id = t.id
            WHERE n.user_id = ?
            GROUP BY n.id
            ORDER BY n.pinned DESC, n.created_at DESC');
        $req->execute([$user_id]);
        $notes = $req->fetchAll(PDO::FETCH_ASSOC);
        // Transforme les tags en tableau
        foreach ($notes as &$note) {
            $note['tags'] = $note['tags'] ? explode(',', $note['tags']) : [];
            $note['pinned'] = (bool) $note['pinned'];
            $note['favorite'] = (bool) $note['favorite'];
        }
        echo json_encode(['notes' => $notes]);
        break;
    case 'POST':
        // Création d'une nouvelle note
        $data = json_decode(file_get_contents('php://input'), true);
        $title = trim($data['title'] ?? '');
        $body = trim($data['body'] ?? '');
        $color = $data['color'] ?? '#ffffff';
        $tags = $data['tags'] ?? [];
        $pinned = !empty($data['pinned']);
        $favorite = !empty($data['favorite']);
        if (!$title) {
            http_response_code(400);
            echo json_encode(['error' => 'Titre obligatoire']);
            exit;
        }
        $req = $conn->prepare('INSERT INTO notes (user_id, title, body, color, pinned, favorite) VALUES (?, ?, ?, ?, ?, ?)');
        $req->execute([$_SESSION['user_id'], $title, $body, $color, $pinned, $favorite]);
        $note_id = $conn->lastInsertId();
        // Gestion des tags
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag === '')
                continue;
            $req = $conn->prepare('INSERT IGNORE INTO tags (name) VALUES (?)');
            $req->execute([$tag]);
            $tag_id = $conn->lastInsertId() ?: $conn->query('SELECT id FROM tags WHERE name = ' . $conn->quote($tag))->fetchColumn();
            $req = $conn->prepare('INSERT INTO note_tags (note_id, tag_id) VALUES (?, ?)');
            $req->execute([$note_id, $tag_id]);
        }
        echo json_encode(['success' => true, 'note_id' => $note_id]);
        break;
    case 'PUT':
        // Maj d'une note
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            parse_str(file_get_contents('php://input'), $data);
        }
        $note_id = $data['id'] ?? null;
        if (!$note_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID manquant']);
            exit;
        }
        $fields = [];
        $params = [];
        foreach (['title', 'body', 'color', 'pinned', 'favorite'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if ($fields) {
            $params[] = $note_id;
            $params[] = $_SESSION['user_id'];
            $sql = 'UPDATE notes SET ' . implode(',', $fields) . ' WHERE id = ? AND user_id = ?';
            $req = $conn->prepare($sql);
            $req->execute($params);
        }
        // Tags
        if (isset($data['tags'])) {
            $tags = is_array($data['tags']) ? $data['tags'] : explode(',', $data['tags']);
            $conn->prepare('DELETE FROM note_tags WHERE note_id = ?')->execute([$note_id]);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if ($tag === '')
                    continue;
                $req = $conn->prepare('INSERT IGNORE INTO tags (name) VALUES (?)');
                $req->execute([$tag]);
                $tag_id = $conn->lastInsertId() ?: $conn->query('SELECT id FROM tags WHERE name = ' . $conn->quote($tag))->fetchColumn();
                $req = $conn->prepare('INSERT INTO note_tags (note_id, tag_id) VALUES (?, ?)');
                $req->execute([$note_id, $tag_id]);
            }
        }
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        // Suppression note
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            parse_str(file_get_contents('php://input'), $data);
        }
        $note_id = $data['id'] ?? null;
        if (!$note_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID manquant']);
            exit;
        }
        $req = $conn->prepare('DELETE FROM notes WHERE id = ? AND user_id = ?');
        $req->execute([$note_id, $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}
