<?php
$is_valid = false;
$pass_incorrect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "lobsterdb";

    $conn = require __DIR__ . "/database.php";

    $stmt = $conn->prepare("SELECT * FROM login_users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();
    if($user){
        if(password_verify($_POST['password'],$user['password'])){
            session_start();
            session_regenerate_id();
            $_SESSION['username']=$user['username'];
            // Password matches, redirect to home.php or perform necessary actions
            header('Location: home.php');
            exit;
        }
        else {
            // Incorrect password
            $pass_incorrect = true;
        }
    } else {
        // User not found
        $is_valid = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lobster</title>
    <link rel="icon" type="image/png" href="lobster_logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
</head>
<body>
    <div class="main-box animate__animated" id="main-box">
        <div class="logo-box">
            <span class="lobster-logo">lobster</span><br> <br>
            <span class="lobster-info">
                <span class="lobster"> doesn't let go of your hand!</span> <br> <br>
            </span>
        </div>
        <div class="login-box">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="user-box">
                    <input type="text" name="username" value="<?=htmlspecialchars($_POST['username']??"")?>" required/>
                    <label>Username</label>
                </div>
                <div class="user-box">
                    <input type="password" name="password"  required/>
                    <label>Password</label>
                </div>
                <center>
                    <button><span>Login</span></button>
                    <!-- <a href="#"><button type="submit" name="login">Log In</button></a> -->
                </center>
            </form>
            <div class="new-account">
                    <center>
                    not have account | <b id="signup">Create Here</b>
                    </center>
            </div>
            <div class="e-box">
                <?php
                    if($is_valid){
                        echo '<center>*Invalid Login</center>';
                    }
                    elseif($pass_incorrect){
                        echo '<center>*Password Incorrect</center>';
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="signingup animate__animated">
        <div class="main-signup">
            <div class="form-box">
                <form action="process_signup.php" method="POST" novalidate>
                    <i class='bx bx-arrow-back' id="back-btn"></i>
                    <center>    
                    <label class="signup-label">Sign up</label>
                    </center>
                    <div class="info-box">
                        <input type="text" id="fullname" name="fullname" required>
                        <label class="text">Full Name</label>
                    </div>
                    <div class="info-box">
                        <input type="text" id="email" name="email" required>
                        <label class="text">E-mail</label>
                    </div>
                    <div class="info-box">
                        <div class="cal-box">
                            <label class="cal-label">Birthday</label>
                            <div class="cal">
                                <input type="date" id="dob" name="dob" class="calendar" required>
                            </div>
                        </div>
                    </div>
                    <div class="gender-box">
                            <label class="gender">Gender</label>
                            <div class="radio-inputs">
                                <label class="radio">
                                    <input type="radio" id="male" value="m" name="gender" checked="">
                                    <span class="name">Male</span>
                                </label>
                                <label class="radio">
                                    <input type="radio" id="female" value="f" name="gender">
                                    <span class="name">Female</span>
                                </label> 
                                <label class="radio">
                                    <input type="radio" id="other" value="o" name="gender">
                                    <span class="name">Other</span>
                                </label>
                            </div>
                        </div>
                    <div class="info-box"  style="margin-top:3rem;">
                    <input type="text" id="username" name="username"required>
                    <label class="text">Username</label>
                    </div>
                    <div class="info-box">
                        <input type="password" id="password" name="password" required>
                        <label class="text">Password</label>
                    </div>
                    <div class="info-box">
                        <input type="password" id="confirmpassword" name="confirmpassword"required>
                        <label class="text">Confirm Password</label>
                    </div>
                    <button><span>Signup</span></button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script>
        const validator = new window.JustValidate('#signup-form');
        validator
            .addField("#fullname",[
                {
                    rule: 'required',
                },
                {
                    rule: 'maxLength',
                    value: 30,
                },
            ])
            .addField("#email",[
                {
                    rule: 'required',
                },
                {
                    rule: 'email',
                },
            ])
            .addField("#password",[
                {
                    rule: 'required',
                },
                {
                    rule: 'password',
                },
            ])
            .addField("#username",[
                {
                    rule: 'required',
                },
                {
                    rule: 'maxLength',
                    value: 30,
                },
            ])
            .addField('#confirmpassword', [
                {
                    rule: 'required',
                },
                {
                validator: (value, fields) => {
                    if (
                        fields['#password'] &&
                        fields['#password'].elem
                    ) {
                        const repeatPasswordValue =
                        fields['#password'].elem.value;

                        return value === repeatPasswordValue;
                    }

                    return true;
                },
                    errorMessage: 'Passwords should be the same',
                }   
            ]);
    </script>
</body>
</html>