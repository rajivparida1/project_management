<?php
session_start();

// Check if POST data is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {

    $conn = new mysqli("localhost", "root", "", "project_manager");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $input = trim($_POST['email']);
    $pass = $_POST['password'];

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR emp_id = ?");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user'] = $user;
            if ($user['role'] === 'manager') {
                header("Location: manager/dashboard.php");
            } else {
                header("Location: employee/dashboard.php");
            }
            exit();
        } else {
            echo "❌ Invalid password.";
        }
    } else {
        echo "❌ User not found.";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "⚠️ Please submit the form correctly.";
}
?>
