<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

// Fetch tasks
$tasks = $conn->query("SELECT tasks.id, tasks.title, projects.name AS project_name 
                       FROM tasks JOIN projects ON projects.id = tasks.project_id");

// Handle feedback submit
if (isset($_POST['add_feedback'])) {
    $task_id = $_POST['task_id'];
    $comment = $_POST['comment'];
    $manager_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO feedback (task_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $task_id, $manager_id, $comment);
    $stmt->execute();

    $success = "Feedback submitted!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Feedback</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        form {
            margin-top: 30px;
        }

        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .success {
            background-color: #d4edda;
            padding: 10px;
            color: #155724;
            border-radius: 4px;
            margin-bottom: 20px;
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
        <h2>Give Task Feedback</h2>

        <?php if (!empty($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Select Task</label>
            <select name="task_id" required>
                <option value="">-- Choose Task --</option>
                <?php while ($task = $tasks->fetch_assoc()): ?>
                    <option value="<?= $task['id'] ?>">
                        <?= $task['title'] ?> (<?= $task['project_name'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Feedback Comment</label>
            <textarea name="comment" required></textarea>

            <button type="submit" name="add_feedback" class="btn">Submit Feedback</button>
        </form>
    </div>
</div>
</body>
</html>
