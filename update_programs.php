<?php
session_start();
include("connection.php");
$user_data = check_login($con);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE progid = :progid");
    $stmt->bindParam(':progid', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
        $progfullname = $program['progfullname'];
        $progshortname = $program['progshortname'];
        $progcollid = $program['progcollid'];
        $progcolldeptid = $program['progcolldeptid'];
    } else {
        header("Location: read_programs.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $progfullname = $_POST['progfullname'] ?? '';
    $progshortname = $_POST['progshortname'] ?? '';
    $progcollid = $_POST['progcollid'] ?? '';
    $progcolldeptid = $_POST['progcolldeptid'] ?? '';

    if (empty($progfullname) || empty($progcollid) || empty($progcolldeptid)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE programs SET progfullname = ?, progshortname = ?, progcollid = ?, progcolldeptid = ? WHERE progid = ?");
            $stmt->execute([$progfullname, $progshortname, $progcollid, $progcolldeptid, $id]);

            $successMessage = "Program updated successfully.";
            header("Location: read_programs.php");
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
    <title>Edit Program</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="axios.min.js"></script>
</head>
<body>
    <div class="main-content">
        <form id="editProgramForm" action="update_programs.php?id=<?php echo $id; ?>" method="POST">
            <h2>Edit Program</h2>
            <div class="form-group">
                <label for="progfullname">Program Full Name</label>
                <input type="text" id="progfullname" name="progfullname" value="<?php echo htmlspecialchars($progfullname); ?>" required>
            </div>
            <div class="form-group">
                <label for="progshortname">Program Short Name</label>
                <input type="text" id="progshortname" name="progshortname" value="<?php echo htmlspecialchars($progshortname); ?>">
            </div>
            <div class="form-group">
                <label for="progcollid">College</label>
                <select id="progcollid" name="progcollid" required onchange="fetchDepartmentsByCollege(this.value)">
                    <option value="">Select College</option>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT collid, collshortname FROM colleges");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['collid']}'" . ($row['collid'] == $progcollid ? ' selected' : '') . ">{$row['collshortname']}</option>";
                        }
                    } catch (PDOException $e) {
                        echo "Connection failed: " . $e->getMessage();
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="progcolldeptid">Department</label>
                <select id="progcolldeptid" name="progcolldeptid" required>
                    <option value="">Select Department</option>
                </select>
            </div>
            <div class="buttons">
                <button type="submit" class="btn-primary">Update</button>
                <button type="button" class="btn-success" onclick="window.location.href='read_programs.php';">Cancel</button>
            </div>
        </form>
        <?php if ($errorMessage): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchDepartmentsByCollege('<?php echo $progcollid; ?>').then(() => {
                document.getElementById('progcolldeptid').value = '<?php echo $progcolldeptid; ?>';
            });
            document.getElementById('progcollid').value = '<?php echo $progcollid; ?>';
        });

        function fetchDepartmentsByCollege(collegeId) {
            const departmentSelect = document.getElementById('progcolldeptid');
            departmentSelect.innerHTML = '<option value="">Select Department</option>';

            if (collegeId) {
                return axios.get(`fetch.php?type=departments&collid=${collegeId}`)
                    .then(response => {
                        const departments = response.data;
                        departments.forEach(department => {
                            const option = document.createElement('option');
                            option.value = department.deptid;
                            option.textContent = department.deptfullname;
                            departmentSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching departments:', error);
                    });
            }
        }
    </script>
</body>
</html>