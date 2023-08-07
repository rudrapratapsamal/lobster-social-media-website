<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
    if(isset($_SESSION['user'])){
        if($_SESSION['user']['username']===$_GET['username']){
            header("Location: profile.php");
            exit();
        }

        $username = $_SESSION['user']['username'];
        $conn = require __DIR__ . "/database.php";

        $search_result = $_GET['username'];
        $sql = "SELECT * FROM user_details WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_result);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $username= $user['username'];
        $fullname = $user['fullname'];
        $email = $user['email'];
        $bio = $user['bio'];
        $profileimg = $user['profile_pic'];

        $stmt = $conn->prepare("SELECT COUNT(*) as total_rows FROM follow_details WHERE requested_user = ? AND request_condition = 't'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_following = $row['total_rows'];

        $stmt = $conn->prepare("SELECT COUNT(*) as total_rows FROM follow_details WHERE following_user = ? AND request_condition = 't'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_followers = $row['total_rows'];

        $stmt = $conn->prepare("SELECT COUNT(*) as total_rows FROM post_details WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_post = $row['total_rows'];

        $stmt = $conn->prepare("SELECT * FROM follow_details WHERE requested_user = ? AND following_user = ?");
        $stmt->bind_param("ss", $_SESSION['user']['username'], $_GET['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $is_following=FALSE;
        $is_requested=FALSE;
        if($row = $result->fetch_assoc()){
            if($row['request_condition']==='t'){
                $is_following=TRUE;
            }
            elseif($row['request_condition']==='f'){
                $is_following=FALSE;
                $is_requested=TRUE;
            }
        }

        if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['follow'])){
            $user = $_SESSION['username']; // get the username of the current user
            $following_user = $_GET['username']; // replace this with the username of the user you want to follow
            $condition = 'f';
            $currentDateTime = date("d-m-Y H:i:s");


            // connect to database
            $conn = require __DIR__ . "/database.php";

            // prepare query
            $stmt = $conn->prepare("INSERT INTO follow_details (requested_user, following_user, request_condition, date_time) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $user, $following_user,$condition,$currentDateTime);
            $stmt->execute();
            
            // Update $is_following to reflect the new state of following
            $is_requested = TRUE;
            header("Location: searched_profile.php?username=$following_user");
            exit();
        }
        elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['requested']) && $is_requested){
            $user = $_SESSION['username']; // get the username of the current user
            $following_user = $_GET['username'];
            $stmt = $conn->prepare("DELETE FROM follow_details WHERE requested_user = ? AND following_user = ?");
            $stmt->bind_param("ss", $_SESSION['username'], $_GET['username']);
            $stmt->execute();
            $is_requested = FALSE;
            header("Location: searched_profile.php?username=$following_user");
            exit();
        }
        elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfollow'])){
            $user = $_SESSION['username']; // get the username of the current user
            $following_user = $_GET['username'];
            $stmt = $conn->prepare("DELETE FROM follow_details WHERE requested_user = ? AND following_user = ?");
            $stmt->bind_param("ss", $_SESSION['username'], $_GET['username']);
            $stmt->execute();
            $is_requested = FALSE;
            $is_following = FALSE;
            header("Location: searched_profile.php?username=$following_user");
            exit();
        }
    }
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="lobster_logo.png">

        <!--=============== BOXICONS ===============-->
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

        <!--=============== CSS ===============-->
        <link rel="stylesheet" href="assets/css/searched_profile_styles.css">

        <title>Responsive sidebar menu - Bedimcode</title>
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

                    <a href="search.php" class="nav__link  active-link">
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
                        <span class="nav__name">Profile</span>
                    </a>
                </div>
            </nav>
        </div>

        <!--=============== MAIN ===============-->
        <div class="container section">
            <div class="main">
                <div class="profile-left">
                    <div class="top">
                        <img src="<?php echo 'data:image;base64,'.$profileimg ?>" alt="<?php echo $username?>">
                        <span class="username">@<span id="username"><?php echo $username?></span></span>
                        <span class="name" id="name"><?php echo $fullname?></span>
                        <span class="bio" id="bio"><?php echo $bio?></span>
                    </div>

                    <div class="connection-box">
                        <div class="post-numbers"><span class="connection-text">Posts</span><?php echo $total_post; ?></div>
                        <div class="follower-numbers"><span class="connection-text">Followers</span><a href="#" id="followers"><?php echo $total_followers; ?></a></div>
                        <div class="following-numbers"><span class="connection-text">Following</span><a href="#" id="followings"><?php echo $total_following; ?></a></div>
                    </div>
                    <div id="modal">
                        <div id="modal-content">
                            <span class="close">&times;</span>
                        </div>
                    </div>
                    <div class="bottom">
                        <form method="POST">
                            <?php if($is_following): ?>
                                <button name="following" id="following-btn" disabled>Following</button>
                                <button  name="unfollow" id="unfollow-btn">Unfollow</button>
                            <?php endif;?>
                            <?php if (!$is_requested AND !$is_following): ?>
                                <button name="follow" id="follow-btn">Follow</button>
                            <?php endif;?>
                            <?php if ($is_requested): ?>
                                <button name="requested" id="request-btn" title="Cancel Request">Requested</button>
                            <?php endif;?>
                        </form>
                    </div>
                </div>
                <div class="profile-right">
                <?php
                        // assuming session is already started
                        $user = $search_result;
                        // connect to database
                        $conn = require __DIR__ . "/database.php";
                        
                        // prepare query
                        $stmt = $conn->prepare("SELECT * FROM post_details WHERE username = ? ORDER BY post_date DESC");
                        $stmt->bind_param("s", $user);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // iterate through results and output HTML
                        while ($row = $result->fetch_assoc()) {
                            $img = $row['post_media'];
                            $user = $row['username'];
                            $caption = $row['post_caption'];
                            $date = $row['post_date'];
                            // format date
                            $date = date_create($date);
                            $date = date_format($date, "d-m-Y H:i");

                            // output HTML
                            echo '<div class="post-item">
                                    <div class="post-head">
                                        <div class=\'post-profile\'>
                                            <img src=\'data:image;base64,'.$profileimg.'\'>
                                        </div>
                                        <div class=\'post-details\'>
                                            '.$user.'
                                        </div>    
                                    </div>
                                    <div class="post-body">
                                        <img src=\'data:image;base64,'.$img.'\'>
                                    </div>
                                    <div class="post-foot">
                                        <div class="post-caption">'.$caption.'</div>
                                        <div class="post-time">Posted on: '.$date.'</div>
                                    </div>
                                </div>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <!--=============== MAIN JS ===============-->
        <script src="assets/js/menu.js"></script>
        <script>
            var followers_link = document.getElementById('followers');
            var following_link = document.getElementById('followings');
            var modal = document.getElementById('modal');
            var modalContent = document.getElementById('modal-content');
            var username = document.getElementById('username').innerHTML;

            // Add a click event listener to the link
            followers_link.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the link from opening the PHP file directly
                
                // Fetch the details from the PHP file
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'searched_show_followers.php?username='+username, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Display the details in the modal
                        modalContent.innerHTML = xhr.responseText;
                        modal.style.display = 'block';
                    } else {
                        alert('Error fetching details!');
                    }
                };
                xhr.send();
            });

            following_link.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the link from opening the PHP file directly
                
                // Fetch the details from the PHP file
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'searched_show_followings.php?username='+username, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Display the details in the modal
                        modalContent.innerHTML = xhr.responseText;
                        modal.style.display = 'block';
                    } else {
                        alert('Error fetching details!');
                    }
                };
                xhr.send();
            });

            var span = modal.querySelector(".close");
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </body>
</html>