<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
    $username = $_SESSION['username'];
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--=============== BOXICONS ===============-->
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

        <!--=============== CSS ===============-->
        <link rel="stylesheet" href="assets/css/notification_styles.css">
        <link rel="icon" type="image/png" href="lobster_logo.png">

        <title>Notifications<?php echo " | @".$username?></title>
    </head>
    <body>
        <!--=============== NAV ===============-->
        <div class="nav" id="nav">
            <nav class="nav__content">
                <div class="nav__toggle" id="nav-toggle">
                    <i class='bx bx-chevron-right' ></i>
                </div>
    
                <a href="#" class="nav__logo">
                    <img src="lobster_logo.png">
                    <span class="nav__logo-name lobster">lobster</span>
                </a>
    
                <div class="nav__list">
                    <a href="home.php" class="nav__link">
                        <i class='bx bx-home-alt'></i>
                        <span class="nav__name">Home</span>
                    </a>

                    <a href="search.php" class="nav__link">
                        <i class='bx bx-search'></i>
                        <span class="nav__name">Search</span>
                    </a>
    
                    <a href="chat.php" class="nav__link">
                        <i class='bx bx-message-square-dots' ></i>
                        <span class="nav__name">Chat</span>
                    </a>
    
                    <a href="notification.php" class="nav__link  active-link">
                        <i class='bx bx-bell' ></i>
                        <span class="nav__name">Notifications</span>
                    </a>
    
                    <a href="setting.php" class="nav__link">
                        <i class='bx bx-cog' ></i>
                        <span class="nav__name">Settings</span>
                    </a>

                    <a href="profile.php" class="nav__link">
                        <i class='bx bx-user' ></i>
                        <span class="nav__name">Profile</span>
                    </a>
                </div>
            </nav>
        </div>

        <!--=============== MAIN ===============-->
        <div class="container section">
            <div class="main">
                <div class="notification-head">
                    <span class="notification-label">Notifications</span>
                </div>
                <div class="notification-body">
                    <?php
                        // Check if notifiedQuery is set in $_POST
                        $conn = require __DIR__ . "/database.php";

                        // Prepare query to fetch requested users
                        $stmt = $conn->prepare("SELECT * FROM follow_details WHERE following_user = ? ORDER BY date_time DESC");
                        $stmt->bind_param("s", $_SESSION['username']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $count = 0;
                        // Fetch requested user details and display in HTML
                        while ($row = $result->fetch_assoc()) {
                            $count+=1;
                            $requested_user = $row['requested_user'];
                            $follow_id = $row['id'];
                            $request_condition=$row['request_condition'];
                            $notify_time = $row['date_time'];
                            $notify_time = strtotime($notify_time);
                            $current_time = time(); 
                            $total_seconds = $current_time - $notify_time;
                            $stmt2 = $conn->prepare("SELECT fullname, username, profile_pic FROM user_details WHERE username = ?");
                            $stmt2->bind_param("s", $requested_user);
                            $stmt2->execute();
                            $result2 = $stmt2->get_result();
                            $row2 = $result2->fetch_assoc();
                            $fullname = $row2['fullname'];
                            $username = $row2['username'];
                            $profile_pic = $row2['profile_pic'];

                            echo "<a href='searched_profile.php?username=$username'>
                                    <div class='notified-item'>
                                        <div class='notified-profile'>
                                            <img src='data:image;base64,$profile_pic' alt='$username'>
                                        </div>
                                        <div class='notified-details'>
                                            <span class='profile-name'>$fullname</span>
                                            <span class='profile-username'>$username";
                                            if($request_condition==='t'){
                                                echo "</span>";
                                            }
                                            elseif($request_condition==='f'){
                                                echo "<br> requested to follow you.</span>";
                                            }
                            echo "      </div>
                                        <div class='response-box'>";
                            if($request_condition==='t'){
                                echo "<span class='notify-time'>Request accepted&nbsp;</span>";
                            }
                            elseif($request_condition==='f'){
                                echo "      <form action='follow_response.php' method='POST'>
                                                <input type='hidden' name='fid' value='$follow_id'>
                                                <button id='accept-btn' name='acceptBtn'><i class='bx bx-check btn-icon'></i>Accept</button>
                                                <button id='reject-btn' name='rejectBtn'><i class='bx bx-x btn-icon'></i>Reject</button>
                                            </form>";
                            }
                            echo "      </div>
                                        <div class='notify-time'>";

                                        if($total_seconds<60){
                                            echo $total_seconds."s ago";
                                        }
                                        else{
                                            $total_seconds = $total_seconds/60;
                                            if($total_seconds<60){
                                                echo intval($total_seconds)."m ago";
                                            }else{
                                                $total_seconds=$total_seconds/60;
                                                echo intval($total_seconds)."h ago";
                                            }
                                        }
                        
                            echo "       </div>
                                    </div>
                                </a>";
                                 
                        }
                        $conn->close();
                    ?>
                </div>
            </div>
        </div>

        <!--=============== MAIN JS ===============-->
        <script src="assets/js/menu.js"></script>
    </body>
</html>