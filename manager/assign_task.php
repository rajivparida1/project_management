<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

// Fetch all tasks
$tasks = $conn->query("SELECT tasks.id, tasks.title, projects.name AS project_name
                       FROM tasks JOIN projects ON tasks.project_id = projects.id");

// Fetch all employees
$employees = $conn->query("SELECT id, name FROM users WHERE role = 'employee'");

// Handle assignment
if (isset($_POST['assign'])) {
    $task_id = $_POST['task_id'];
    $selected_employees = $_POST['employees'];

    foreach ($selected_employees as $emp_id) {
        // Check if already assigned
        $check = $conn->query("SELECT * FROM task_team WHERE task_id=$task_id AND employee_id=$emp_id");
        if ($check->num_rows === 0) {
            $conn->query("INSERT INTO task_team (task_id, employee_id, status) VALUES ($task_id, $emp_id, 'pending')");
        }
    }

    $success = "Team members assigned successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Assign Employees to Task</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <style>
        
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
        <h2>Assign Employees to Task</h2>

        <?php if (!empty($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="task_id">Select Task</label>
            <select name="task_id" required>
                <option value="">-- Select Task --</option>
                <?php while ($task = $tasks->fetch_assoc()): ?>
                    <option value="<?= $task['id'] ?>">
                        <?= $task['title'] ?> (<?= $task['project_name'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="employees">Select Employees</label>
            <select name="employees[]" multiple required size="5">
                <?php while ($emp = $employees->fetch_assoc()): ?>
                    <option value="<?= $emp['id'] ?>"><?= $emp['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="assign" class="btn">Assign Employees</button>
        </form>
    </div>
</div>
</body>
</html>
