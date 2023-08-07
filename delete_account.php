<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Delete the user's account from the database
    $conn = require __DIR__ . "/database.php";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_SESSION['username'];

    $sql1 = "DELETE FROM login_users WHERE username = '$username'";
    $sql2 = "DELETE FROM user_details WHERE username = '$username'";
    $sql3 = "DELETE FROM post_details WHERE username = '$username'";
    $sql4 = "DELETE FROM follow_details WHERE requested_user = '$username' OR following_user= '$username'";

    if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE && $conn->query($sql3) === TRUE && $conn->query($sql4) === TRUE ) {
        // Logout the user and destroy the session
        session_unset();
        session_destroy();
        // Redirect to a thank you page after successful deletion
        header("Location: thankyou.php");
        exit;
    } else {
        echo "Error deleting account: " . $conn->error;
    }

    $conn->close();
} else {
    header("Location: index.php"); // Redirect to index page if form is not submitted
    exit;
}
?>
