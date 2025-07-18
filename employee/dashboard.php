<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

$employee_id = $_SESSION['user']['id'];

// Update status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['task_id']) && isset($_POST['status'])) {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE task_team SET status=? WHERE task_id=? AND employee_id=?");
    $stmt->bind_param("sii", $status, $task_id, $employee_id);
    $stmt->execute();
}

// Load assigned tasks
$sql = "SELECT t.id, t.title, t.deadline, t.project_id, p.name AS project_name, at.status
        FROM tasks t
        JOIN task_team at ON t.id = at.task_id
        JOIN projects p ON t.project_id = p.id
        WHERE at.employee_id = $employee_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f2f4f8;
        }

        .wrapper {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #fff;
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 30px;
            color: #007bff;
        }

        nav ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        nav ul li a {
            display: block;
            text-decoration: none;
            padding: 12px 16px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #0056b3;
            color: white;
        }
        
        .logout {
            text-align: left;
        }

        .logout a {
            text-decoration: none;
            color: #fff;
            background: #dc3545;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .content {
            flex-grow: 1;
            padding: 40px;
        }

        h2 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-save:hover {
            background-color: #218838;
        }

        select {
            padding: 5px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h3 {
            margin: 0;
        }

        .btn-feedback {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <div>
            <h2>Employee Panel</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                </ul>
            </nav>
        </div>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <div class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></h2>
        </div>

        <h3>Your Assigned Tasks</h3>
        <table>
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Title</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
            <?php
            $i = 1;
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['project_name']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['deadline']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                        <select name="status">
                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="in progress" <?= $row['status'] === 'in progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="completed" <?= $row['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                </td>
                <td>
                        <button type="submit" class="btn-save">Save</button>
                    </form>
                </td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="6">No tasks assigned yet.</td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
</body>
</html>
