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
    $deptid = $_POST['deptid'];
    $deptfullname = $_POST['deptfullname'];
    $deptshortname = $_POST['deptshortname'];
    $deptcollid = $_POST['deptcollid'];

    if (empty($deptid) || empty($deptfullname) || empty($deptcollid)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid) VALUES (?, ?, ?, ?)");
            $stmt->execute([$deptid, $deptfullname, $deptshortname, $deptcollid]);

            $successMessage = "Department added successfully.";
            header("Location: read_departments.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMessage = "Duplicate entry for Department ID.";
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
    <title>Add Department</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-content">
        <div class="form-container">
            <form id="addDepartmentForm" action="create_departments.php" method="POST">
                <h2>Add Department</h2>
                <div class="form-group">
                    <label for="deptid">Department ID</label>
                    <input type="text" id="deptid" name="deptid" required>
                </div>
                <div class="form-group">
                    <label for="deptfullname">Department Full Name</label>
                    <input type="text" id="deptfullname" name="deptfullname" required>
                </div>
                <div class="form-group">
                    <label for="deptshortname">Department Short Name</label>
                    <input type="text" id="deptshortname" name="deptshortname">
                </div>
                <div class="form-group">
                    <label for="deptcollid">College</label>
                    <select id="deptcollid" name="deptcollid" required>
                        <option value="">Select College</option>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT collid, collshortname FROM colleges");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['collid']}'>{$row['collshortname']}</option>";
                            }
                        } catch (PDOException $e) {
                            echo "Connection failed: " . $e->getMessage();
                        }
                        ?>
                    </select>
                </div>
                <div class="buttons">
                    <button type="submit" name="submit" class="btn-primary">Add</button>
                    <button type="button" class="btn-danger" onclick="document.getElementById('addDepartmentForm').reset();">Clear</button>
                    <button type="button" class="btn-success" onclick="window.location.href='read_departments.php';">Cancel</button>
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