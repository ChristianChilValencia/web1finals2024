<?php
session_start();
include("connection.php");
$user_data = check_login($con);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE deptid = :deptid");
    $stmt->bindParam(':deptid', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $department = $stmt->fetch(PDO::FETCH_ASSOC);
        $deptfullname = $department['deptfullname'];
        $deptshortname = $department['deptshortname'];
        $deptcollid = $department['deptcollid'];
    } else {
        header("Location: read_departments.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptfullname = $_POST['deptfullname'] ?? '';
    $deptshortname = $_POST['deptshortname'] ?? '';
    $deptcollid = $_POST['deptcollid'] ?? '';

    if (empty($deptfullname) || empty($deptcollid)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE departments SET deptfullname = ?, deptshortname = ?, deptcollid = ? WHERE deptid = ?");
            $stmt->execute([$deptfullname, $deptshortname, $deptcollid, $id]);

            $successMessage = "Department updated successfully.";
            header("Location: read_departments.php");
            exit();
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-content">
        <form id="editDepartmentForm" action="update_departments.php?id=<?php echo $id; ?>" method="POST">
            <h2>Edit Department</h2>
            <div class="form-group">
                <label for="deptfullname">Department Full Name</label>
                <input type="text" id="deptfullname" name="deptfullname" value="<?php echo htmlspecialchars($deptfullname); ?>" required>
            </div>
            <div class="form-group">
                <label for="deptshortname">Department Short Name</label>
                <input type="text" id="deptshortname" name="deptshortname" value="<?php echo htmlspecialchars($deptshortname); ?>">
            </div>
            <div class="form-group">
                <label for="deptcollid">College</label>
                <select id="deptcollid" name="deptcollid" required>
                    <option value="">Select College</option>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT collid, collshortname FROM colleges");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['collid']}'" . ($row['collid'] == $deptcollid ? ' selected' : '') . ">{$row['collshortname']}</option>";
                        }
                    } catch (PDOException $e) {
                        echo "Connection failed: " . $e->getMessage();
                    }
                    ?>
                </select>
            </div>
            <div class="buttons">
                <button type="submit" class="btn-primary">Update</button>
                <button type="button" class="btn-success" onclick="window.location.href='read_departments.php';">Cancel</button>
            </div>
        </form>
        <?php if ($errorMessage): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>