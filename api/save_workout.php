<?php
require 'db.php';
header('Content-Type: application/json');

// Obtener payload
$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!$data || !isset($data['date']) || !isset($data['sets']) || !isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos de sesion o estructura incorrecta"]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO workouts (user_id, date) VALUES (?, ?)");
    $stmt->execute([$data['user_id'], $data['date']]);
    $workoutId = $pdo->lastInsertId();

    $setStmt = $pdo->prepare("INSERT INTO exercise_sets (workout_id, exercise_name, weight, reps) VALUES (?, ?, ?, ?)");
    
    foreach ($data['sets'] as $set) {
        $setStmt->execute([
            $workoutId, 
            $set['exercise_name'], 
            $set['weight'], 
            $set['reps']
        ]);
    }

    $pdo->commit();
    echo json_encode(["success" => true, "workout_id" => $workoutId]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => "Error guardando: " . $e->getMessage()]);
}
?>
