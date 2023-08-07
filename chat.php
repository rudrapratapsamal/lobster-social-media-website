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
        <link rel="stylesheet" href="assets/css/chat_styles.css">
        <link rel="icon" type="image/png" href="lobster_logo.png">

        <title>Chat<?php echo " | @".$username?></title>
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
    
                    <a href="chat.php" class="nav__link  active-link">
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
                <div class="main-left">
                    <div class="left-head">
                        <span class="chat-label">
                            Chats
                        </span>
                    </div>
                    <div class="search-box">
                        <form action="">
                            <input type="text" class="chat-search" placholder="Search">
                            <i class="bx bx-search search-icon"></i>
                        </form>
                    </div>
                    <a href="#">
                        <div class="left-chat">
                            <div class="chat-profile-pic">
                                <img src="logo.jpg" alt="profile">
                            </div>
                            <div class="chat-details">
                                <span class="chat-name">Name</span>
                                <span class="last-chat">last chat</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="main-right">
                    <div class="right-head">
                        <div class="chat-profile-pic">
                            <img src="logo.jpg" alt="profile">
                        </div>
                        <div class="chat-details">
                            <span class="chat-name">Name</span>
                        </div>
                    </div>  
                    <div class="right-body">

                    </div>
                    <div class="right-msg-box">
                        <input type="text" class="msg-input">
                        <a href="#">
                            <i class='bx bx-send send-icon'></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!--=============== MAIN JS ===============-->
        <script src="assets/js/menu.js"></script>
    </body>
</html>