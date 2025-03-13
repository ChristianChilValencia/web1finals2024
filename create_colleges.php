<?php
session_start();
include("connection.php");
$user_data = check_login($con);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collid = $_POST['collid'];
    $collfullname = $_POST['collfullname'];
    $collshortname = $_POST['collshortname'];

    if (empty($collid) || empty($collfullname) || empty($collshortname)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO colleges (collid, collfullname, collshortname) VALUES (?, ?, ?)");
            $stmt->execute([$collid, $collfullname, $collshortname]);

            $successMessage = "College added successfully.";
            header("Location: read_colleges.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMessage = "Duplicate entry for College ID.";
            } else {
                $errorMessage = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add College</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-content">
        <div class="form-container">
            <form id="addCollegeForm" action="create_colleges.php" method="POST">
                <h2>Add College</h2>
                <div class="form-group">
                    <label for="collid">College ID</label>
                    <input type="text" id="collid" name="collid" required>
                </div>
                <div class="form-group">
                    <label for="collfullname">College Full Name</label>
                    <input type="text" id="collfullname" name="collfullname" required>
                </div>
                <div class="form-group">
                    <label for="collshortname">College Short Name</label>
                    <input type="text" id="collshortname" name="collshortname" required>
                </div>
                <div class="buttons">
                    <button type="submit" name="submit" class="btn-primary">Add</button>
                    <button type="button" class="btn-danger" onclick="document.getElementById('addCollegeForm').reset();">Clear</button>
                    <button type="button" class="btn-success" onclick="window.location.href='read_colleges.php';">Cancel</button>
                </div>
            </form>
            <?php if ($errorMessage): ?>
                <div id="errorModal" class="modal">
                    <div class="modal-content">
                        <h2>Error</h2>
                        <p><?php echo $errorMessage; ?></p>
                        <button onclick="document.getElementById('errorModal').style.display='none'">Close</button>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($successMessage): ?>
                <div id="successModal" class="modal">
                    <div class="modal-content">
                        <h2>Success</h2>
                        <p><?php echo $successMessage; ?></p>
                        <button onclick="document.getElementById('successModal').style.display='none'">Close</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (<?php echo json_encode($errorMessage); ?>) {
                document.getElementById('errorModal').style.display = 'flex';
            }
            if (<?php echo json_encode($successMessage); ?>) {
                document.getElementById('successModal').style.display = 'flex';
            }
        });
    </script>
</body>
</html>