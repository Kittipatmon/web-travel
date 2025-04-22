<?php
include "connect.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['u_id'])) {
    $u_id = (int)$_POST['u_id'];

    $query = "UPDATE uploads SET view_count = view_count + 1 WHERE u_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $u_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'View counted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating view']);
    }
} else {
    echo json_encode(['message' => 'Invalid request']);
}
