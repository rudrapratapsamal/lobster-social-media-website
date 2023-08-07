<?php
session_start();
echo "hello";
if (!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
    die('valid email is required');
}   
if (strlen($_POST['password'])<8){
    die('password must be at least 8 characters');
}
if(!preg_match('/[a-z]/i',$_POST['password'])){
    die('password must contain one lowercase letter');
} 
if(!preg_match('/[0-9]/',$_POST['password'])){
    die('password must contain one number');    
}
if($_POST['password']!==$_POST['confirmpassword']){
    die('password does not match');
}

// Get form data
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$user = $_POST['username'];
$password_hash = password_hash($_POST['password'],PASSWORD_DEFAULT);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lobsterdb";

$conn = require __DIR__ . "/database.php";

// Insert data into login_users table
$sql = "INSERT INTO login_users (fullname, email, dob, gender, username, password)
        VALUES ('$fullname', '$email', '$dob', '$gender', '$user', '$password_hash')";

if ($conn->query($sql) === TRUE) {

    $sql = "INSERT INTO user_details (username, fullname, email)
            VALUES ('$user', '$fullname', '$email')";
    $conn->query($sql);

    $_SESSION['username'] = $user;

    // Redirect to setting.php
    header("Location: setting.php");
    exit;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
