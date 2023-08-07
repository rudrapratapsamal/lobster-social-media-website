<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
    if(isset($_SESSION['username'])){
        $conn = require __DIR__ . "/database.php";
        $sql = "SELECT * FROM user_details WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION["username"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();  
        $_SESSION['user'] = $user;

    }
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="lobster_logo.png">
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="assets/css/home_styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="home.js"></script>
        <title>lobster<?php echo " | @".$_SESSION['username']?></title>
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
                        <a href="home.php" class="nav__link active-link">
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
        
                        <a href="notification.php" class="nav__link">
                            <i class='bx bx-bell' ></i>
                            <span class="nav__name">Notifications</span>
                        </a>
        
                        <a href="setting.php" class="nav__link">
                            <i class='bx bx-cog' ></i>
                            <span class="nav__name">Settings</span>
                        </a>

                        <a href="profile.php" class="nav__link">
                            <i class='bx bx-user' ></i>
                            <span class="nav__name">My Profile</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!--=============== MAIN ===============-->
            <div class="container section">
            <div class="main">
            <?php
                            // assuming session is already started
                            $user = $_SESSION['username'];

                            $conn = require __DIR__ . "/database.php";
                            // First query
                            $stmt = $conn->prepare("SELECT following_user FROM follow_details WHERE requested_user = ? AND request_condition = 't'");
                            $stmt->bind_param("s", $_SESSION['username']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $all_users[] = $_SESSION['username'];
                            $posts_yet = FALSE;
                            while ($row = $result->fetch_assoc()) {
                                $all_users[] = $row['following_user'];
                                $posts_yet = TRUE;
                            }
                            if($posts_yet){
                                foreach($all_users as $user){
                                    $stmt = $conn->prepare("SELECT * FROM post_details WHERE username = ? ORDER BY post_id DESC");
                                    $stmt->bind_param("s", $user);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $posts[] = $row;
                                    }
                                }
                                usort($posts, function($a, $b) {
                                    return strtotime($b['post_date']) - strtotime($a['post_date']);
                                });
                                foreach($posts as $p){
                                    $stmt = $conn->prepare("SELECT * FROM post_details WHERE post_id = ? ORDER BY post_date DESC");
                                    $stmt->bind_param("s", $p['post_id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
    
                                    // iterate through results and output HTML
                                    while ($row = $result->fetch_assoc()) {
                                        $img = $row['post_media'];
                                        $user = $row['username'];
                                        $caption = $row['post_caption'];
                                        $date = $row['post_date'];
                                        $stmt = $conn->prepare("SELECT * FROM user_details WHERE username = ?");
                                        $stmt->bind_param("s", $user);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_assoc();
                                        $profileimg = $row['profile_pic'];
                                        // format date
                                        $date = date_create($date);
                                        $date = date_format($date, "d-m-Y H:i:s");
    
                                        // output HTML
                                        echo '<div class="post-item">
                                                <a href="searched_profile.php?username='.$user.'">
                                                    <div class="post-head">
                                                        <div class="pseudo-post-head">
                                                            <div class=\'post-profile\'>
                                                                <img src=\'data:image;base64,'.$profileimg.'\' alt=\''.$user.'\'>
                                                            </div>
                                                            <div class=\'post-details\'>
                                                                '.$user.'
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="post-body">
                                                    <img src=\'data:image;base64,'.$img.'\'>
                                                </div>
                                                <div class="post-foot">
                                                    <div class="post-caption">'.$caption.'</div>
                                                    <div class="post-time">Posted on: '.$date.'</div>
                                                </div>
                                            </div>';
                                    }
                                }
                            }
                            else{
                                echo "<span class='no-post'>No Posts Yet</span>";
                            }
                        ?>
                </div>
            </div>
        </div>

        <!--=============== MAIN JS ===============-->
        <script src="assets/js/menu.js"></script>
    </body>
</html>