<?php
require 'db.php';
header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Autenticación requerida. Se requiere el ID de usuario."]);
    exit;
}

$userId = $_GET['user_id'];

try {
    // Solamente cargar los entrenamientos que correspondan al usuario
    $stmt = $pdo->prepare("SELECT id, date FROM workouts WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$userId]);
    $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Iterar para adjuntar sus Series (Sets)
    foreach ($workouts as &$workout) {
        $wId = $workout['id'];
        $setStmt = $pdo->prepare("SELECT exercise_name, weight, reps FROM exercise_sets WHERE workout_id = ?");
        $setStmt->execute([$wId]);
        $workout['sets'] = $setStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($workouts);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error extrayendo entrenamientos: " . $e->getMessage()]);
}
?>
