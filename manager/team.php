<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

// Generate EMP ID
function generateEmpID($conn) {
    $prefix = "EMP" . date('Y');
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='employee'");
    $count = $result->fetch_assoc()['count'] + 1;
    return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
}

// Add or Edit employee
if (isset($_POST['save_employee'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $id = $_POST['id'] ?? '';

    // Check for duplicate email
    $check = $conn->prepare("SELECT id FROM users WHERE email=? AND role='employee'" . ($id ? " AND id!=?" : ""));
    $id ? $check->bind_param("si", $email, $id) : $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();
    
    if ($checkResult->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $emp_id = generateEmpID($conn);

        $password = $_POST['password'] ?? '';
        $hashed_pass = password_hash($password ?: "123456", PASSWORD_DEFAULT);

        if ($id) {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $hashed_pass, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, emp_id) VALUES (?, ?, ?, 'employee', ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_pass, $emp_id);
        }
        $stmt->execute();
        $success = $id ? "Employee updated!" : "Employee added!";
    }
}

// Delete employee
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM users WHERE id=" . $_GET['delete']);
    header("Location: team.php");
    exit();
}

// Edit load
$edit_employee = null;
if (isset($_GET['edit'])) {
    $res = $conn->query("SELECT * FROM users WHERE id=" . $_GET['edit']);
    $edit_employee = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Team</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        .btn {
            padding: 6px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .btn-edit {
            background-color: #ffc107;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .error, .success {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }
        .wrapper {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 240px;
            background-color: #fff;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            color: #007bff;
            margin-bottom: 25px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            display: block;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 500;
        }

        .sidebar ul li a:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <div>
            <h2>Project Manager</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="tasks.php">Tasks</a></li>
                <li><a href="team.php">Team</a></li>
                <li><a href="assign_task.php">Assign Task</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            </ul>
        </div>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <h2><?= $edit_employee ? "Edit Employee" : "Add New Employee" ?></h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="post">
            <?php if ($edit_employee): ?>
                <input type="hidden" name="id" value="<?= $edit_employee['id'] ?>">
            <?php endif; ?>

            <label>Name</label>
            <input type="text" name="name" required value="<?= $edit_employee['name'] ?? '' ?>">

            <label>Email</label>
            <input type="email" name="email" required value="<?= $edit_employee['email'] ?? '' ?>">

            <label>Password (optional)</label>
            <input type="password" name="password" placeholder="Leave empty for default (123456)">

            <button type="submit" name="save_employee" class="btn"><?= $edit_employee ? "Update" : "Add" ?></button>
        </form>

        <h3>Team Members</h3>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Employee ID</th>
                <th>Actions</th>
            </tr>
            <?php
            $res = $conn->query("SELECT * FROM users WHERE role='employee'");
            $i = 1;
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['emp_id']) ?></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this employee?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
