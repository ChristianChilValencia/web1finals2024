<?php
    session_start();
    include("connection.php");

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];

        if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
        {
            $user_id = uniqid();
            $query = "insert into users (user_id, user_name, password) values ('$user_id', '$user_name', '$password')";
            mysqli_query($con, $query);
            header("Location: login.php");
            die;

        }else{
            echo "Please enter some valid information!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div id="container">
        <div id="box">
            <h2 class="webtitle">WEB FINALS</h2>
            <h2>Sign Up</h2>
            <form class="input" action="signup.php" method="post">
                <div class="inputBox">
                    <input id="text" type="text" name="user_name" required="">
                    <label>Username</label>
                </div>
                <div class="inputBox">
                    <input id="text" type="password" name="password" required="">
                    <label>Password</label>
                </div>
                <input id="text" type="submit" name="submit" value="Create Account">
                <a href="login.php">Have an account?</a>
            </form>
        </div>
    </div>
</body>
</html>