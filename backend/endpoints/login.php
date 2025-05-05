<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    exit(0);
}



require_once __DIR__ . '/../db.php';

session_start();

$donnees = json_decode(file_get_contents("php://input"), true);
$login = $donnees['login'] ?? '';
$password = $donnees['password'] ?? '';
$db = new Database();
$conn = $db->getConnexion();

if (!isset($login) || !isset($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs obligatoires manquants']);
    exit;
}

$query = 'SELECT * FROM users WHERE (email = ? OR login = ?) AND password = MD5(?)';
$req = $conn->prepare($query);
$req->execute([$login, $login, $password]);
$user = $req->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(400);
    echo json_encode(['error' => 'Identifiants invalides']);
} else {
    $_SESSION['user_id'] = $user['id'];
    echo json_encode(['succes' => true]);
}


