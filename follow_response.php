<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$conn = require __DIR__ . "/database.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['acceptBtn'])) {
        $follow_id = $_POST['fid'];
        $currentDateTime = date("d-m-Y H:i:s");
        $stmt = $conn->prepare("UPDATE follow_details SET request_condition = 't', date_time=? WHERE id = ?");
        $stmt->bind_param("si", $currentDateTime,$follow_id);
        if ($stmt->execute() === TRUE) {
            header("Location: notification.php");
            exit;
        }else {
            echo "Error updating user details: " . $stmt->error;
        }
    } elseif (isset($_POST['rejectBtn'])) {
        $follow_id = intval($_POST['fid']);
        $stmt = $conn->prepare("DELETE FROM follow_details WHERE id = ?");
        $stmt->bind_param("i", $follow_id);
        if ($stmt->execute() === TRUE) {
            header("Location: notification.php");
            exit;
        }else {
            echo "Error updating user details: " . $stmt->error;
        }
    }
}
