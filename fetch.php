<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usjr", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $type = $_GET['type'] ?? '';
    $id = $_GET['id'] ?? '';
    $collid = $_GET['collid'] ?? '';

    switch ($type) {
        case 'students':
            handleStudents($pdo, $id);
            break;
        case 'programs':
            handlePrograms($pdo, $id, $collid);
            break;
        case 'departments':
            handleDepartments($pdo, $id, $collid);
            break;
        case 'colleges':
            handleColleges($pdo, $id);
            break;
        default:
            echo json_encode([]);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

function handleStudents($pdo, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM colleges WHERE collid = :studcollid");
        $stmt->execute([':studcollid' => $requestData['studcollid']]);
        if ($stmt->fetchColumn()) {
            $stmt = $pdo->prepare("INSERT INTO students (studfirstname, studlastname, studmidname, studprogid, studcollid, studyear)
                                   VALUES (:studfirstname, :studlastname, :studmidname, :studprogid, :studcollid, :studyear)");
            $stmt->execute($requestData);
            echo json_encode(["message" => "Student added successfully"]);
        } else {
            echo json_encode(["error" => "Invalid college ID"]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT s.*, p.progfullname, c.collshortname FROM students s
                  LEFT JOIN programs p ON s.studprogid = p.progid
                  LEFT JOIN colleges c ON s.studcollid = c.collid";
        if ($id) {
            $query .= " WHERE s.studid = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->query($query);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $stmt = $pdo->prepare("DELETE FROM students WHERE studid = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        echo json_encode($stmt->execute() ? ["message" => "Student deleted successfully"] : ["error" => "Failed to delete student"]);
    }
}

function handlePrograms($pdo, $id, $collid) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO programs (progfullname, progshortname, progcollid, progcolldeptid)
                               VALUES (:progfullname, :progshortname, :progcollid, :progcolldeptid)");
        $stmt->execute($requestData);
        echo json_encode(["message" => "Program added successfully"]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT p.*, c.collshortname, d.deptfullname FROM programs p
                  LEFT JOIN colleges c ON p.progcollid = c.collid
                  LEFT JOIN departments d ON p.progcolldeptid = d.deptid";
        if ($id) {
            $query .= " WHERE p.progid = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } elseif ($collid) {
            $query .= " WHERE p.progcollid = :collid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':collid', $collid, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->query($query);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $stmt = $pdo->prepare("DELETE FROM programs WHERE progid = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        echo json_encode($stmt->execute() ? ["message" => "Program deleted successfully"] : ["error" => "Failed to delete program"]);
    }
}

function handleDepartments($pdo, $id, $collid) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO departments (deptfullname, deptshortname, deptcollid)
                               VALUES (:deptfullname, :deptshortname, :deptcollid)");
        $stmt->execute($requestData);
        echo json_encode(["message" => "Department added successfully"]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT d.*, c.collshortname FROM departments d
                  LEFT JOIN colleges c ON d.deptcollid = c.collid";
        if ($id) {
            $query .= " WHERE d.deptid = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } elseif ($collid) {
            $query .= " WHERE d.deptcollid = :collid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':collid', $collid, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->query($query);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $stmt = $pdo->prepare("DELETE FROM departments WHERE deptid = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        echo json_encode($stmt->execute() ? ["message" => "Department deleted successfully"] : ["error" => "Failed to delete department"]);
    }
}

function handleColleges($pdo, $id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO colleges (collfullname, collshortname)
                               VALUES (:collfullname, :collshortname)");
        $stmt->execute($requestData);
        echo json_encode(["message" => "College added successfully"]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT * FROM colleges";
        if ($id) {
            $query .= " WHERE collid = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->query($query);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $stmt = $pdo->prepare("DELETE FROM colleges WHERE collid = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        echo json_encode($stmt->execute() ? ["message" => "College deleted successfully"] : ["error" => "Failed to delete college"]);
    }
}
?>
