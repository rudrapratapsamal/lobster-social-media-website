<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post'])){
        $user = $_SESSION['username'];
        $media = $_FILES['media']['tmp_name'];
        $name = $_FILES['media']['name'];
        $media = base64_encode(file_get_contents(addslashes($media)));
        
        $caption = $_POST['caption'];
        
        $currentDateTime = date("d-m-Y H:i:s");
        
        $conn = require __DIR__ . "/database.php";

        $stmt = $conn->prepare("INSERT INTO post_details (post_media, post_caption, username, post_date) VALUES (?, ?, ?, ?)");

        $stmt->bind_param("ssss", $media, $caption, $user, $currentDateTime);

        if ($stmt->execute() === TRUE) {
            // Redirect to profile.php
            header("Location: profile.php");
            exit;
        } else {
            echo "Error inserting post: " . $stmt->error;
        }
    }
?>
