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
    <title>Students Entry</title>
    <script src="axios.min.js"></script>
    <script src="crud.js"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-content expanded" id="main-content">
        <div class="header">
            <div class="add-button">
                <button class="btn-yellow" onclick="window.location.href='create_students.php'"><img src="assets/add-icon.png" alt="Add">Create New Student</button>
            </div>
            <div class="user-info">
                <span>Welcome <?php echo htmlspecialchars($user_data['user_name']); ?></span>
            </div>
            <div class="logout-container">
                <div class="back-button">
                    <button onclick="window.location.href='Dashboard.php'"><img src="assets/back-icon.png" alt="Back">Dashboard</button>
                </div>
                <div onclick="window.location.href='logout.php'">
                    <button type="submit"><img src="assets/logout-icon.png" alt="Logout">Logout</button>
                </div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Middle Name</th>
                    <th>Program</th>
                    <th>College</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentsTable">
            </tbody>
        </table>

        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <h2>Confirm Delete</h2>
                <p>Are you sure you want to delete this item?</p>
                <button id="confirmDeleteButton" class="confirm"><img src="assets/confirm-icon.png" alt="Confirm">Yes</button>
                <button id="cancelDeleteButton" class="cancel"><img src="assets/cancel-icon.png" alt="Cancel">No</button>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Document loaded');
                fetchStudents();
            });
        </script>
    </div>
</body>
</html>