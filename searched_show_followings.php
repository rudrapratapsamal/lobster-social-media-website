<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
$conn = require __DIR__ . "/database.php";
$sql = "SELECT * FROM follow_details WHERE requested_user = ? ORDER BY following_user ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_GET["username"]);
$stmt->execute();
$result = $stmt->get_result();
echo "Following";
if($result->num_rows!==0){
    while($row = $result->fetch_assoc()){
        $sql1 = "SELECT * FROM user_details WHERE username = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s",$row['following_user']);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $follower  = $result1->fetch_assoc();
        $profileimg = $follower['profile_pic'];
        echo "
        <a href='searched_profile.php?username=$follower[username]'>
            <div class='follower-item'>
                <div class='follower-img'>
                    <img src='data:image;base64,$profileimg'>
                </div>
                <div class='follower-details'>
                    <span class='follower-name'>$follower[fullname]</span>
                    <span class='follower-username'>$follower[username]</span>
                </div>
            </div>
        </a>";
    }
}else{
    echo "0 Following yet";    
}
?>