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
        <link rel="stylesheet" href="assets/css/search_styles.css">
        <link rel="icon" type="image/png" href="lobster_logo.png">

        <title>Search Account<?php echo " | @".$username?></title>
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
                <div class="search-head">
                    <center>
                        <form action="search.php" method="POST">
                            <input type="text" name="searchQuery" placeholder="Search..."><button id="search-icon"><i class='bx bx-search'></i></button>
                        </form>
                    </center>
                </div>
                <div class="search-body">
                    <?php
                        // Check if searchQuery is set in $_POST
                        if (isset($_POST['searchQuery'])) {
                            // Retrieve search query from $_POST
                            $searchQuery = $_POST['searchQuery'];

                            // Set searchQuery to null if empty
                            if (empty($searchQuery)) {
                                $searchQuery = null;
                            }

                            // Perform basic search on user_details table if searchQuery is not null
                            if ($searchQuery !== null) {

                                // Create database connection
                                $conn = require __DIR__ . "/database.php";

                                // Check for database connection errors
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Prepare and execute search query
                                $sql = "SELECT * FROM user_details WHERE username LIKE '%" . $searchQuery . "%' or fullname LIKE '%" . $searchQuery . "%'";
                                $result = $conn->query($sql);

                                // Fetch search results and generate HTML content
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Generate HTML content for each search result
                                        $fullname = $row['fullname'];
                                        $username = $row['username'];
                                        $img = $row['profile_pic'];
                                        echo "<a href='searched_profile.php?username=$username'>
                                                <div class='search-item'>
                                                    <div class='search-profile'>
                                                        <img src='data:image;base64,$img'>
                                                    </div>
                                                    <div class='search-details'>
                                                        <span class='profile-name'>$fullname</span>
                                                        <span class='profile-username'>$username</span>
                                                    </div>
                                                </div>
                                            </a>";
                                    }
                                } else {
                                    // Display message if no search results found
                                    echo "No results found.";
                                }

                                // Close database connection
                                $conn->close();
                            }
                        }
                    ?>

                </div>
            </div>
        </div>

        <!--=============== MAIN JS ===============-->
        <script src="assets/js/menu.js"></script>
    </body>
</html>