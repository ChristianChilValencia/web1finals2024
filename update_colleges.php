<?php
session_start();
include("connection.php");
$user_data = check_login($con);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM colleges WHERE collid = :collid");
    $stmt->bindParam(':collid', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $college = $stmt->fetch(PDO::FETCH_ASSOC);
        $collfullname = $college['collfullname'];
        $collshortname = $college['collshortname'];
    } else {
        header("Location: read_colleges.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collfullname = $_POST['collfullname'] ?? '';
    $collshortname = $_POST['collshortname'] ?? '';

    if (empty($collfullname) || empty($collshortname)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE colleges SET collfullname = ?, collshortname = ? WHERE collid = ?");
            $stmt->execute([$collfullname, $collshortname, $id]);

            $successMessage = "College updated successfully.";
            header("Location: read_colleges.php");
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
    <title>Edit College</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="main-content">
        <form id="editCollegeForm" action="update_colleges.php?id=<?php echo $id; ?>" method="POST">
            <h2>Edit College</h2>
            <div class="form-group">
                <label for="collfullname">College Full Name</label>
                <input type="text" id="collfullname" name="collfullname" value="<?php echo htmlspecialchars($collfullname); ?>" required>
            </div>
            <div class="form-group">
                <label for="collshortname">College Short Name</label>
                <input type="text" id="collshortname" name="collshortname" value="<?php echo htmlspecialchars($collshortname); ?>" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn-primary">Update</button>
                <button type="button" class="btn-success" onclick="window.location.href='read_colleges.php';">Cancel</button>
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