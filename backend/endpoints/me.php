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

$query = 'SELECT id, email, login FROM users WHERE id = ?';
$req = $conn->prepare($query);
$req->execute([$_SESSION['user_id']]);
$user = $req->fetch(PDO::FETCH_ASSOC);
echo json_encode(['user' => $user]);