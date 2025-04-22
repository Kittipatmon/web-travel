<?php
include "connect.php";

if (isset($_GET['t_id'])) {
    $t_id = $_GET['t_id'];

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM tourist_spot WHERE t_id = ?");
    $stmt->bind_param("i", $t_id);
    $stmt->execute();

    header("Location: dashboard_detail.php");
    exit;
}
