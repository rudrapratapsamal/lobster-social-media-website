<?php
    session_start(); // Start the session

    // Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }

    if (isset($_SESSION['username'])) {
        // Get the user details from session variables
        $username = $_SESSION['username'];
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
        $img = $user['profile_pic'];

        $_SESSION['user'] =$user; 

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
            // Get form data
            $newUsername = $_POST['username'];
            $newFullname = $_POST['fullname'];
            $newEmail = $_POST['email'];
            $newBio = $_POST['bio'];
            $image = $_FILES['imagefile']['tmp_name'];
            $name = $_FILES['imagefile']['name'];
            $image = base64_encode(file_get_contents(addslashes($image)));

            // Update user_details table
            $sql = "UPDATE user_details SET username=?, fullname=?, email=?, bio=?, profile_pic=? WHERE username=?";
            $stmt1 = $conn->prepare($sql);
            $stmt1->bind_param("ssssss", $newUsername, $newFullname, $newEmail, $newBio, $image, $username);

            $sql = "UPDATE login_users SET username=?, fullname=?, email=? WHERE username=?";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("ssss", $newUsername, $newFullname, $newEmail, $username);

            if ($stmt1->execute() === TRUE && $stmt2->execute() === TRUE) {
                // Redirect to setting.php
                header("Location: setting.php");
                exit;
            } else {
                echo "Error updating user details: " . $stmt->error;
            }
        }
    }
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--=============== BOXICONS ===============-->
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

        <!--=============== CSS ===============-->
        <link rel="stylesheet" href="assets/css/setting_styles.css">
        <link rel="icon" type="image/png" href="lobster_logo.png">

        <title>Settings<?php echo " | @".$username?></title>
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
    
                    <a href="setting.php" class="nav__link  active-link">
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
                <div class="setting-head">
                    <span class="setting-label">Settings</span>
                </div>
                <div class="setting-body">
                    <div class="pseudo-left">
                        <div class="left">
                            <div class="setting-pic">
                                <img src="<?php echo 'data:image;base64,'.$img ?>" alt="">
                            </div>
                            <div class="setting-details">                        
                                <span class="setting-username">@<?php echo $username; ?></span>
                                <span class="setting-name"><?php echo $fullname; ?></span>
                            </div>
                            <form action="delete_account.php" method="POST">
                                <button id="delete-button" name="delete">
                                    Delete My Account
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="right">
                        <form method="POST" enctype="multipart/form-data">
                            <table border="0">
                                <tr class="setting-item">
                                    <td></td>
                                    <td>
                                        <label for="profile_pic" id="select-pic">
                                            Change Profile Photo
                                            <input type="file" name="imagefile" accept="image/*" id="profile_pic">
                                        </label>
                                    </td>
                                </tr>
                                <tr class="setting-item">
                                    <td><label>Name</label></td>
                                    <td><input type="text" name="fullname" value="<?php echo $fullname; ?>"></td>
                                </tr>
                                <tr class="setting-item">
                                    <td><label>Username</label></td>
                                    <td><input type="text" name="username" value="<?php echo $username; ?>"></td>
                                </tr>
                                <tr class="setting-item">
                                    <td><label>E-mail</label></td>
                                    <td><input type="text" name="email" value="<?php echo $email; ?>"></td>
                                </tr>
                                <tr class="setting-item">
                                    <td><label>Bio</label></td>
                                    <td><input type="text" value="<?php echo $bio; ?>" name="bio"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <button class="submit" name="submit">Submit</button>
                                        <button class="cancel">Cancel</button></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--=============== MAIN JS ===============-->
        <script src="assets/js/menu.js"></script>
    </body>
</html>