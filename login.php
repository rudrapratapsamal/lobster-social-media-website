<?php
$is_valid = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = require __DIR__ . '/database.php';
    $sql = sprintf('SELECT * FROM login_users where username = "%s"',$conn->real_escape_string( $_POST['username']));
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if($user){
        if(password_verify($_POST['password'],$user['password'])){
            header('Location: home.php');
            exit;
        }
    }
    $is_valid = true;
}
?>
