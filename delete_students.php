<?php
session_start();
include("connection.php");
$user_data = check_login($con);

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $studid = $_GET['id'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM students WHERE studid = :studid");
        $stmt->bindParam(':studid', $studid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Deleted successfully."]);
        } else {
            echo json_encode(["error" => "Failed to delete the student."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid request."]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Student</title>
    <script src="axios.min.js"></script>
    <script src="crud.js"></script>
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            deleteStudent(id);
        });
    </script>
</body>
</html>