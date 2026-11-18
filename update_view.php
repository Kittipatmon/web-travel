<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connect.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['u_id'])) {
    $u_id = (int)$_POST['u_id'];

    if ($u_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    // ป้องกันการยิงสคริปต์ปั๊มยอดวิวซ้ำๆ (Deduplication ต่อ Session)
    if (!isset($_SESSION['viewed_spots'])) {
        $_SESSION['viewed_spots'] = [];
    }

    if (in_array($u_id, $_SESSION['viewed_spots'])) {
        echo json_encode(['success' => true, 'message' => 'Already counted in session']);
        exit;
    }

    $_SESSION['viewed_spots'][] = $u_id;

    $query = "UPDATE uploads SET view_count = view_count + 1 WHERE u_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $u_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'View counted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating view']);
    }
    $stmt->close();
} else {
    echo json_encode(['message' => 'Invalid request']);
}
