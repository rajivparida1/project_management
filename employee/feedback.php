<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

$employee_id = $_SESSION['user']['id'];

$sql = "SELECT f.message, f.created_at, m.name AS manager_name
        FROM feedback f
        JOIN managers m ON f.manager_id = m.id
        WHERE f.employee_id = $employee_id
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Feedback</title>
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
            padding: 0;
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
            margin-bottom: 20px;
        }

        .feedback-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-left: 5px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .feedback-box .meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .feedback-box .message {
            font-size: 16px;
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
        <h2>Feedback Received</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="feedback-box">
                    <div class="meta">From: <?= htmlspecialchars($row['manager_name']) ?> | <?= $row['created_at'] ?></div>
                    <div class="message"><?= htmlspecialchars($row['message']) ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No feedback found yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
