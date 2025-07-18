<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_manager");

// Add or Update
if (isset($_POST['save_project'])) {
    $id = $_POST['id'] ?? '';
    $name = $_POST['project_name'];
    $desc = $_POST['description'];
    $created_by = $_SESSION['user']['id'];

    $document = '';
    if (!empty($_FILES['document']['name'])) {
        $uploadDir = "../uploads/";
        $document = time() . '_' . basename($_FILES['document']['name']);
        move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $document);
    }

    if ($id) {
        if ($document) {
            $stmt = $conn->prepare("UPDATE projects SET name=?, description=?, document=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $desc, $document, $id);
        } else {
            $stmt = $conn->prepare("UPDATE projects SET name=?, description=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $desc, $id);
        }
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO projects (name, description, document, created_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $desc, $document, $created_by);
        $stmt->execute();
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM projects WHERE id=$id");
}

// Load for edit
$edit_project = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM projects WHERE id=$id");
    $edit_project = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Projects</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
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
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        form {
            max-width: 600px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button.btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-edit {
            background-color: #ffc107;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            color: #000;
        }

        .btn-delete {
            background-color: #dc3545;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
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
                <li><a href="assign_task.php">Assign Task</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            </ul>
        </div>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2><?= $edit_project ? "Edit Project" : "Add New Project" ?></h2>

        <form method="post" enctype="multipart/form-data">
            <?php if ($edit_project): ?>
                <input type="hidden" name="id" value="<?= $edit_project['id'] ?>">
            <?php endif; ?>

            <label>Project Name</label>
            <input type="text" name="project_name" value="<?= $edit_project['name'] ?? '' ?>" required>

            <label>Description</label>
            <textarea name="description" required><?= $edit_project['description'] ?? '' ?></textarea>

            <label>Upload Document (Optional)</label>
            <input type="file" name="document">

            <button type="submit" name="save_project" class="btn">
                <?= $edit_project ? "Update Project" : "Add Project" ?>
            </button>
        </form>

        <h3>All Projects</h3>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Document</th>
                <th>Actions</th>
            </tr>
            <?php
            $res = $conn->query("SELECT * FROM projects");
            $i = 1;
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <?php if ($row['document']): ?>
                        <a href="../uploads/<?= $row['document'] ?>" target="_blank">View</a>
                    <?php else: ?> -
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
