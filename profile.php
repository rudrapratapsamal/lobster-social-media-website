<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];

        $username = $user['username'];
        $conn = require __DIR__ . "/database.php";
        $sql = "SELECT * FROM user_details WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_SESSION["username"]);
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

    }
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--=============== BOXICONS ===============-->
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
        <link rel="icon" type="image/png" href="lobster_logo.png">

        <!--=============== CSS ===============-->
        <link rel="stylesheet" href="assets/css/profile_styles.css">

        <title><?php echo $fullname." | @".$username;?></title>
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
    
                    <a href="notification.php" class="nav__link">
                        <i class='bx bx-bell' ></i>
                        <span class="nav__name">Notifications</span>
                    </a>
    
                    <a href="setting.php" class="nav__link">
                        <i class='bx bx-cog' ></i>
                        <span class="nav__name">Settings</span>
                    </a>

                    <a href="profile.php" class="nav__link active-link">
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
                    <div class="profile-box">
                        <img src="<?php echo 'data:image;base64,'.$profileimg?>">
                        <span class="username">@<span id="username"><?php echo $username?></span></span>
                        <span class="name" id="name"><?php echo $fullname?></span>
                        <span class="bio" id="bio"><?php echo $bio?></span>
                    </div>
                    <div class="connection-box">
                        <div class="post-numbers"><span class="connection-text">Posts</span><?php echo $total_post; ?></div>
                        <div class="follower-numbers"><span class="connection-text">Followers</span><a href="#" id="followers"><?php echo $total_followers; ?></a></div>
                        <div class="following-numbers"><span class="connection-text">Following</span><a href="#" id="followings"><?php echo $total_following; ?></a></div>
                    </div>
                    <a href="logout.php">
                        <span class="logout" id="logout-btn">Logout<i class='bx bx-log-out logout-icon'></i></span>
                    </a>     
                </div>
                <div id="modal">
                    <div id="modal-content">
                        <span class="close">&times;</span>
                    </div>
                </div>
                <div class="profile-right">
                    <form action="make_post.php" method="POST" enctype="multipart/form-data">
                        <div class="post-box" id="post-box">
                            
                                <label for="mediaInput" id="media-label">
                                    Select Image or Video
                                    <input type="file" name="media" accept="image/*" id="mediaInput">
                                </label>
                        
                        </div>
                        <div class="caption-box" id="caption-box">
                            <textarea name="caption" id="caption"></textarea>
                            <button name="post">Post</button><button name="post">Cancel</button>
                        </div>
                    </form>
                        <?php
                            // assuming session is already started
                            $user = $_SESSION['username'];
                            // connect to database
                            $conn = require __DIR__ . "/database.php";
                            
                            // check if the delete button was clicked
                            if (isset($_POST['deleteBtn'])) {
                                $post_id = $_POST['post_id'];
                                // prepare query
                                $stmt = $conn->prepare("DELETE FROM post_details WHERE post_id = ? AND username = ?");
                                $stmt->bind_param("is", $post_id, $user);
                                $stmt->execute();
                                
                            }

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
                                $post_id = $row['post_id'];
                                // format date
                                $date = date_create($date);
                                $date = date_format($date, "d-m-Y H:i:s");

                                // output HTML
                                echo '<div class="post-item">
                                        <div class="post-head">
                                            <div class="pseudo-post-head">
                                                <div class=\'post-profile\'>
                                                    <img src=\'data:image;base64,'.$profileimg.'\' alt=\''.$user.'\'>
                                                </div>
                                                <div class=\'post-details\'>
                                                    '.$user.'
                                                </div>    
                                            </div>
                                            <div class="post-delete ">
                                                <form method="POST">
                                                    <input type="hidden" name="post_id" value="'.$post_id.'">
                                                    <button type="submit" name="deleteBtn" id="del-btn"><i class=\'bx bx-trash\' ></i></button>
                                                </form>
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

            // Add a click event listener to the link
            followers_link.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the link from opening the PHP file directly
                
                // Fetch the details from the PHP file
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'show_followers.php', true);
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
                xhr.open('GET', 'show_followings.php', true);
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
            // Listen for change event on file input element
                document.getElementById('mediaInput').addEventListener('change', function (event) {
                var file = event.target.files[0];

                // Check if a file is selected
                if (file) {
                    var captionBox = document.getElementById('caption-box');
                    captionBox.style.display = "block";
                    var mediaContainer = document.getElementById('post-box');
                    var mediaLabel = document.getElementById('media-label');

                    // Check if the selected file is an image
                    if (file.type.includes('image')) {
                        var img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.style.width = '100%';
                        img.style.alignSelf = 'center';
                        mediaLabel.style.display='none';
                        // mediaContainer.innerHTML = '';
                        mediaContainer.appendChild(img);
                    }
                    // Check if the selected file is a video
                    else if (file.type.includes('video')) {
                        var video = document.createElement('video');
                        video.src = URL.createObjectURL(file);
                        video.controls = true;
                        video.style.width = '100%';
                        video.style.height = '30%';
                        mediaLabel.style.display='none';
                        // mediaContainer.innerHTML = '';
                        mediaContainer.appendChild(video);
                    }
                }
            });
        </script>
    </body>
</html>