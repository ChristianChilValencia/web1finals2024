<?php
    session_start();
    include("connection.php");

    $error_message = "";

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];

        if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
        {
            $query = "select * from users where user_name = '$user_name' limit 1";
            $result = mysqli_query($con, $query);
            
            if($result){
                if($result && mysqli_num_rows($result) > 0){
                    $user_data = mysqli_fetch_assoc($result);
                    if($user_data['password'] === $password){
                        $_SESSION['user_id'] = $user_data['user_id'];
                        header("Location: dashboard.php");
                        die;
                    }
                }
            }
            $error_message = "Wrong Username or Password!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div id="container">
        <div id="box">
            <h2 class="webtitle">WEB FINALS</h2>
            <h2>Login</h2>
            <?php if(!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form class="input" action="login.php" method="post">
                <div class="inputBox">
                    <input id="text" type="text" name="user_name" required="">
                    <label>Username</label>
                </div>
                <div class="inputBox">
                    <input id="text" type="password" name="password" required="">
                    <label>Password</label>
                </div>
                <input id="text" type="submit" name="submit" value="Login">
                <a href="signup.php">Don't have an account?</a>
            </form>
        </div>
    </div>
</body>
</html>