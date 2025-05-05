<?php

require_once __DIR__ . '/../db.php';

header("Access-Control-Allow-Origin: http://localhost:5173");
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    exit(0);
}

$data = json_decode(file_get_contents("php://input"), true);

$login = $data['login'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$db = new Database();
$pdo = $db->getConnexion();

if (empty($login) || empty($email) || empty($password)) 
{
    http_response_code(400);
    echo json_encode(['error' => 'Tous les champs sont obligatoires.']);
    exit;
}
else 
{
    try {
        // Vérification de l'email + nom d'utilisateur si déjà en BDD
        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email OR login = :login';

        $req = $pdo->prepare($sql);

        $req->bindValue(':email', $email, PDO::PARAM_STR);

        $req->bindValue(':login', $login, PDO::PARAM_STR);

        $req->execute();

        $exists = $req->fetchColumn();

        if ($exists > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Email ou nom d\'utilisateur déjà utilisé.']);
            exit;
        }
        else
        {
            // Insertion nouvel user
            $sqlInsertion = 'INSERT INTO users (email, login, password) VALUES (?, ?, MD5(?))';
            $reqInsertion = $pdo->prepare($sqlInsertion);
            $reqInsertion->execute([$email, $login, $password]);
        
            echo json_encode(['success' => true]);
        }
    } 
    catch (Exception $ex) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur serveur : ' . $ex->getMessage()]);
    }
}


