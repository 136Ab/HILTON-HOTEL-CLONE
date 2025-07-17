<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $message = "Login successful! Redirecting to homepage...";
                echo "<script>setTimeout(() => { window.location.href = 'home.php'; }, 2000);</script>";
            } else {
                $message = "Error: Invalid password.";
            }
        } else {
            $message = "Error: User not found.";
        }
    } catch (Exception $e) {
        $message = "Error: Database issue.";
        error_log("Login exception: " . $e->getMessage(), 3, "errors.log");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #e6f0fa 0%, #b8c6db 100%);
        }
        .header {
            background: linear-gradient(90deg, #003087, #0041c2);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .login-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .login-container h2 {
            color: #003087;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        .login-container input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .login-container button {
            background: #ff6200;
            color: white;
            padding: 15px;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 700;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .login-container button:hover {
            background: #e55a00;
            transform: scale(1.02);
        }
        .message {
            color: green;
            text-align: center;
            margin: 20px 0;
            font-size: 1.1em;
        }
        .error {
            color: red;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a {
            color: #003087;
            text-decoration: none;
            font-weight: 700;
        }
        .signup-link a:hover {
            color: #ff6200;
        }
        @media (max-width: 768px) {
            .login-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hilton Hotels - Login</h1>
    </div>

    <div class="login-container">
        <h2>Log In</h2>
        <?php if (isset($message)) { ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>"><?php echo $message; ?></p>
        <?php } ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
        <div class="signup-link">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</body>
</html>
