<?php
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan credenciales"]);
    exit;
}

// BCRYPT Secure Hashing
$hash = password_hash($data['password'], PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->execute([$data['username'], $hash]);
    echo json_encode(["success" => true, "user_id" => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(400); // Seguramente el usuario ya existe (Unique Constraint)
    echo json_encode(["error" => "El usuario ya existe."]);
}
?>
