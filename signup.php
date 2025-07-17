<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    try {
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $email);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            $message = "Signup successful! Redirecting to homepage...";
            echo "<script>setTimeout(() => { window.location.href = 'home.php'; }, 2000);</script>";
        } else {
            $message = "Error: Could not register user.";
            error_log("Signup error: " . $conn->error, 3, "errors.log");
        }
    } catch (Exception $e) {
        $message = "Error: Database issue.";
        error_log("Signup exception: " . $e->getMessage(), 3, "errors.log");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Signup</title>
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
        .signup-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .signup-container h2 {
            color: #003087;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        .signup-container input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .signup-container button {
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
        .signup-container button:hover {
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
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #003087;
            text-decoration: none;
            font-weight: 700;
        }
        .login-link a:hover {
            color: #ff6200;
        }
        @media (max-width: 768px) {
            .signup-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hilton Hotels - Signup</h1>
    </div>

    <div class="signup-container">
        <h2>Create an Account</h2>
        <?php if (isset($message)) { ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>"><?php echo $message; ?></p>
        <?php } ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Log In</a></p>
        </div>
    </div>
</body>
</html>
