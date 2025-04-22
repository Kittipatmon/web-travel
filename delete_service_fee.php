<?php
include "connect.php";

if (isset($_GET['s_id'])) {
    $s_id = $_GET['s_id'];

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM service_fee WHERE s_id = ?");
    $stmt->bind_param("i", $s_id);
    $stmt->execute();

    header("Location: dashboard_service_fee.php");
    exit;
}
