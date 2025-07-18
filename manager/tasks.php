<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

// Fetch all projects
$projects = $conn->query("SELECT * FROM projects");

// Add or Update Task
if (isset($_POST['save_task'])) {
    $task_id = $_POST['task_id'] ?? '';
    $project_id = $_POST['project_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $deadline = $_POST['deadline'];

    if ($task_id) {
        $stmt = $conn->prepare("UPDATE tasks SET project_id=?, title=?, description=?, deadline=? WHERE id=?");
        $stmt->bind_param("isssi", $project_id, $title, $desc, $deadline, $task_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (project_id, title, description, deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $project_id, $title, $desc, $deadline);
        $stmt->execute();
    }
}

// Delete Task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
}

// Load for Edit
$edit_task = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM tasks WHERE id=$id");
    $edit_task = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Tasks</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        form, table {
            margin-top: 30px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn {
            padding: 6px 10px;
            border: none;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #ffc107;
        }

        .btn-delete {
            background-color: #dc3545;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
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

        .logout {
            text-align: center;
        }

        .logout a {
            text-decoration: none;
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
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
        <h2><?= $edit_task ? "Edit Task" : "Add New Task" ?></h2>

        <form method="post">
            <?php if ($edit_task): ?>
                <input type="hidden" name="task_id" value="<?= $edit_task['id'] ?>">
            <?php endif; ?>

            <label>Select Project</label>
            <select name="project_id" required>
                <option value="">-- Choose Project --</option>
                <?php while ($p = $projects->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>" <?= ($edit_task && $edit_task['project_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Task Title</label>
            <input type="text" name="title" value="<?= $edit_task['title'] ?? '' ?>" required>

            <label>Description</label>
            <textarea name="description" required><?= $edit_task['description'] ?? '' ?></textarea>

            <label>Deadline</label>
            <input type="date" name="deadline" value="<?= $edit_task['deadline'] ?? '' ?>" required>

            <button type="submit" name="save_task" class="btn">
                <?= $edit_task ? "Update Task" : "Add Task" ?>
            </button>
        </form>

        <h3>All Tasks</h3>
        <table>
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>Title</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php
            $res = $conn->query("SELECT tasks.*, projects.name AS project_name FROM tasks
                                 JOIN projects ON tasks.project_id = projects.id");
            $i = 1;
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['project_name']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['deadline'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this task?')" class="btn btn-delete">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
