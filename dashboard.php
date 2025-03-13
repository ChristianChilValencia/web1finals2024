<?php
session_start();
include("connection.php");
$user_data = check_login($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOMEPAGE</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="main-content expanded" id="main-content">
    <div class="header">
        <div class="user-info">
            <h3>Welcome <?php echo htmlspecialchars($user_data['user_name']); ?></h3>
        </div>
        <div class="logout-container">
            <div onclick="window.location.href='logout.php'">
                    <button type="submit"><img src="assets/logout-icon.png" alt="Logout">Logout</button>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="box" onclick="location.href='read_students.php'">
        <p>Students</p>
    </div>
    <div class="box" onclick="location.href='read_programs.php'">
        <p>Programs</p>
    </div>
    <div class="box" onclick="location.href='read_departments.php'">
        <p>Departments</p>
    </div>
    <div class="box" onclick="location.href='read_colleges.php'">
        <p>Colleges</p>
    </div>
</div>
</body>
</html>

