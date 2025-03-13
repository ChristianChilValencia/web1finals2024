<?php
session_start();
include("connection.php");
$user_data = check_login($con);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE studid = :studid");
    $stmt->bindParam(':studid', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        $studfirstname = $student['studfirstname'];
        $studlastname = $student['studlastname'];
        $studmidname = $student['studmidname'];
        $studprogid = $student['studprogid'];
        $studcollid = $student['studcollid'];
        $studyear = $student['studyear'];
    } else {
        header("Location: read_students.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studfirstname = $_POST['studfirstname'] ?? '';
    $studlastname = $_POST['studlastname'] ?? '';
    $studmidname = $_POST['studmidname'] ?? '';
    $studprogid = $_POST['studprogid'] ?? '';
    $studcollid = $_POST['studcollid'] ?? '';
    $studyear = $_POST['studyear'] ?? '';

    if (empty($studfirstname) || empty($studlastname) || empty($studprogid) || empty($studcollid) || empty($studyear)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE students SET studfirstname = ?, studlastname = ?, studmidname = ?, studprogid = ?, studcollid = ?, studyear = ? WHERE studid = ?");
            $stmt->execute([$studfirstname, $studlastname, $studmidname, $studprogid, $studcollid, $studyear, $id]);

            $successMessage = "Student updated successfully.";
            header("Location: read_students.php");
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
    <title>Edit Student</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="axios.min.js"></script>
</head>
<body>
    <div class="main-content">
        <form id="editStudentForm" action="update_students.php?id=<?php echo $id; ?>" method="POST">
            <h2>Edit Student</h2>
            <div class="form-group">
                <label for="studfirstname">First Name</label>
                <input type="text" id="studfirstname" name="studfirstname" value="<?php echo htmlspecialchars($studfirstname); ?>" required>
            </div>
            <div class="form-group">
                <label for="studlastname">Last Name</label>
                <input type="text" id="studlastname" name="studlastname" value="<?php echo htmlspecialchars($studlastname); ?>" required>
            </div>
            <div class="form-group">
                <label for="studmidname">Middle Name</label>
                <input type="text" id="studmidname" name="studmidname" value="<?php echo htmlspecialchars($studmidname); ?>">
            </div>
            <div class="form-group">
                <label for="studcollid">College</label>
                <select id="studcollid" name="studcollid" required onchange="fetchProgramsByCollege(this.value)">
                    <option value="">Select College</option>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT collid, collshortname FROM colleges");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['collid']}'" . ($row['collid'] == $studcollid ? ' selected' : '') . ">{$row['collshortname']}</option>";
                        }
                    } catch (PDOException $e) {
                        echo "Connection failed: " . $e->getMessage();
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="studprogid">Program</label>
                <select id="studprogid" name="studprogid" required>
                    <option value="">Select Program</option>
                </select>
            </div>
            <div class="form-group">
                <label for="studyear">Year</label>
                <input type="number" id="studyear" name="studyear" value="<?php echo htmlspecialchars($studyear); ?>" required>
            </div>
            <div class="buttons">
                <button type="submit" class="btn-primary">Update</button>
                <button type="button" class="btn-success" onclick="window.location.href='read_students.php';">Cancel</button>
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
            fetchProgramsByCollege('<?php echo $studcollid; ?>').then(() => {
                document.getElementById('studprogid').value = '<?php echo $studprogid; ?>';
            });
            document.getElementById('studcollid').value = '<?php echo $studcollid; ?>';
        });

        function fetchProgramsByCollege(collegeId) {
            const programSelect = document.getElementById('studprogid');
            programSelect.innerHTML = '<option value="">Select Program</option>';

            if (collegeId) {
                axios.get(`fetch.php?type=programs&collid=${collegeId}`)
                    .then(response => {
                        const programs = response.data;
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.progid;
                            option.textContent = program.progfullname;
                            programSelect.appendChild(option);
                        });
                        programSelect.value = '<?php echo $studprogid; ?>';
                    })
                    .catch(error => {
                        console.error('Error fetching programs:', error);
                    });
            }
        }
    </script>
</body>
</html>