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
    $studid = $_POST['studid'];
    $studfirstname = $_POST['studfirstname'];
    $studlastname = $_POST['studlastname'];
    $studmidname = $_POST['studmidname'];
    $studprogid = $_POST['studprogid'];
    $studcollid = $_POST['studcollid'];
    $studyear = $_POST['studyear'];

    if (empty($studid) || empty($studfirstname) || empty($studlastname) || empty($studprogid) || empty($studcollid) || empty($studyear)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (studid, studfirstname, studlastname, studmidname, studprogid, studcollid, studyear) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$studid, $studfirstname, $studlastname, $studmidname, $studprogid, $studcollid, $studyear]);

            $successMessage = "Student added successfully.";
            header("Location: read_students.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMessage = "Duplicate entry for Student ID.";
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
    <title>Add Student</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="axios.min.js"></script>
</head>
<body>
    <div class="main-content">
        <form id="addStudentForm" action="create_students.php" method="POST">
            <h2>Add Student</h2>
            <div class="form-group">
                <label for="studid">Student ID</label>
                <input type="text" id="studid" name="studid" required>
            </div>
            <div class="form-group">
                <label for="studfirstname">First Name</label>
                <input type="text" id="studfirstname" name="studfirstname" required>
            </div>
            <div class="form-group">
                <label for="studlastname">Last Name</label>
                <input type="text" id="studlastname" name="studlastname" required>
            </div>
            <div class="form-group">
                <label for="studmidname">Middle Name</label>
                <input type="text" id="studmidname" name="studmidname">
            </div>
            <div class="form-group">
                <label for="studcollid">College</label>
                <select id="studcollid" name="studcollid" required onchange="fetchProgramsByCollege(this.value)">
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
            <div class="form-group">
                <label for="studprogid">Program</label>
                <select id="studprogid" name="studprogid" required>
                    <option value="">Select Program</option>
                </select>
            </div>
            <div class="form-group">
                <label for="studyear">Year</label>
                <input type="number" id="studyear" name="studyear" required>
            </div>
            <div class="buttons">
                <button type="submit" name="submit" class="btn-primary">Add</button>
                <button type="button" class="btn-danger" onclick="document.getElementById('addStudentForm').reset();">Clear</button>
                <button type="button" class="btn-success" onclick="window.location.href='read_students.php';">Cancel</button>
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

    <script>
        function fetchProgramsByCollege(collegeId) {
            const programSelect = document.getElementById('studprogid');
            programSelect.innerHTML = '<option value="">Select Program</option>'; // Clear existing options

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
                    })
                    .catch(error => {
                        console.error('Error fetching programs:', error);
                    });
            }
        }

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