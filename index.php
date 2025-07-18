<?php
session_start();
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'manager') {
        header("Location: manager/dashboard.php");
    } else {
        header("Location: employee/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Project Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f2f4f8;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 25px;
            font-size: 26px;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Project Manager App</h2>
        <form action="login.php" method="post">
            <input type="text" name="email" placeholder="Email or Employee ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="footer-note">for new password contact Manager</p>
    </div>
</body>
</html>
