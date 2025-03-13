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
    $progid = $_POST['progid'];
    $progfullname = $_POST['progfullname'];
    $progshortname = $_POST['progshortname'];
    $progcollid = $_POST['progcollid'];
    $progcolldeptid = $_POST['progcolldeptid'];

    if (empty($progid) || empty($progfullname) || empty($progcollid) || empty($progcolldeptid)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$progid, $progfullname, $progshortname, $progcollid, $progcolldeptid]);

            $successMessage = "Program added successfully.";
            header("Location: read_programs.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errorMessage = "Duplicate entry for Program ID.";
            } else {
                $errorMessage = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Program</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="axios.min.js"></script>
</head>
<body>
    <div class="main-content">
        <form id="createProgramForm" action="create_programs.php" method="POST">
            <h2>Add Program</h2>
            <div class="form-group">
                <label for="progid">Program ID</label>
                <input type="text" id="progid" name="progid" required>
            </div>
            <div class="form-group">
                <label for="progfullname">Program Full Name</label>
                <input type="text" id="progfullname" name="progfullname" required>
            </div>
            <div class="form-group">
                <label for="progshortname">Program Short Name</label>
                <input type="text" id="progshortname" name="progshortname">
            </div>
            <div class="form-group">
                <label for="progcollid">College</label>
                <select id="progcollid" name="progcollid" required onchange="fetchDepartmentsByCollege(this.value)">
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
                <label for="progcolldeptid">Department</label>
                <select id="progcolldeptid" name="progcolldeptid" required>
                    <option value="">Select Department</option>
                </select>
            </div>
            <div class="buttons">
                <button type="submit" class="btn-primary">Save</button>
                <button type="button" class="btn-danger" onclick="document.getElementById('createProgramForm').reset();">Clear</button>
                <button type="button" class="btn-success" onclick="window.location.href='read_programs.php';">Cancel</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            fetchColleges();
            document.getElementById('createProgramForm').addEventListener('submit', function(event) {
                event.preventDefault();
                createProgram();
            });
            document.getElementById('progcollid').addEventListener('change', function() {
                fetchDepartmentsByCollege(this.value);
            });

            if (<?php echo json_encode($errorMessage); ?>) {
                document.getElementById('errorModal').style.display = 'flex';
            }
            if (<?php echo json_encode($successMessage); ?>) {
                document.getElementById('successModal').style.display = 'flex';
            }
        });

        function fetchColleges() {
            return axios.get('fetch.php?type=colleges')
                .then(response => {
                    const colleges = response.data;
                    const collegeSelect = document.getElementById('progcollid');
                    colleges.forEach(college => {
                        const option = document.createElement('option');
                        option.value = college.collid;
                        option.textContent = college.collshortname;
                        collegeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching colleges:', error));
        }

        function fetchDepartmentsByCollege(collid) {
            axios.get(`fetch.php?type=departments&collid=${collid}`)
                .then(response => {
                    const departments = response.data;
                    const departmentSelect = document.getElementById('progcolldeptid');
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';
                    departments.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.deptid;
                        option.textContent = department.deptfullname;
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching departments:', error));
        }

        function createProgram() {
            const form = document.getElementById('createProgramForm');
            form.submit();
        }
    </script>
</body>
</html>