<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Safely fetch counts
function getCount($conn, $query) {
    $result = $conn->query($query);
    if (!$result) return 0;
    return $result->fetch_assoc()['total'] ?? 0;
}

$projectCount = getCount($conn, "SELECT COUNT(*) AS total FROM projects");
$taskCount = getCount($conn, "SELECT COUNT(*) AS total FROM tasks");
$employeeCount = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'employee'");
$feedbackCount = getCount($conn, "SELECT COUNT(*) AS total FROM feedback");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <style>

        
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <div>
            <h2>Manager Panel</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="tasks.php">Tasks</a></li>
                <li><a href="team.php">Team</a></li>
                <li><a href="assign_task.php">Assign Tasks</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            </ul>
        </div>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> 👋</h2>
        <h1 class="dashboard-title">DASHBOARD</h1>

        <div class="widgets">
            <div class="widget">
                <h3>📁 Projects</h3>
                <p><?= $projectCount ?></p>
            </div>
            <div class="widget">
                <h3>📝 Tasks</h3>
                <p><?= $taskCount ?></p>
            </div>
            <div class="widget">
                <h3>👨‍💻 Employees</h3>
                <p><?= $employeeCount ?></p>
            </div>
            <div class="widget">
                <h3>💬 Feedback</h3>
                <p><?= $feedbackCount ?></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
