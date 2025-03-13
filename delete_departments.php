<?php
session_start();
include("connection.php");
$user_data = check_login($con);

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $deptid = $_GET['id'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM departments WHERE deptid = :deptid");
        $stmt->bindParam(':deptid', $deptid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Deleted successfully."]);
        } else {
            echo json_encode(["error" => "Failed to delete the department."]);
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
    <title>Delete Department</title>
    <script src="axios.min.js"></script>
    <script src="crud.js"></script>
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            deleteDepartment(id);
        });
    </script>
</body>
</html>