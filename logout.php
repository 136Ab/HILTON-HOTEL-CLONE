<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Logout</title>
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
        .logout-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            text-align: center;
        }
        .logout-container p {
            color: #003087;
            font-size: 1.3em;
            margin-bottom: 20px;
        }
        .logout-container a {
            background: #ff6200;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .logout-container a:hover {
            background: #e55a00;
            transform: scale(1.02);
        }
        @media (max-width: 768px) {
            .logout-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hilton Hotels - Logout</h1>
    </div>

    <div class="logout-container">
        <p>You have been logged out successfully.</p>
        <a href="home.php">Return to Homepage</a>
    </div>
</body>
</html>
