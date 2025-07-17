<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to view your bookings.'); window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
try {
    $sql = "SELECT b.*, h.name, h.location FROM bookings b JOIN hotels h ON b.hotel_id = h.id WHERE b.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Bookings exception: " . $e->getMessage(), 3, "errors.log");
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - My Bookings</title>
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
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #003087, #0041c2);
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 12px rgba(0,0,0,0.4);
            transition: transform 0.3s ease;
        }
        .sidebar h2 {
            font-size: 1.8em;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .sidebar a:hover {
            background: #ff6200;
            transform: translateX(5px);
        }
        .content {
            margin-left: 270px;
            flex-grow: 1;
            padding: 20px;
        }
        .header {
            background: linear-gradient(90deg, #003087, #0041c2);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .booking-list {
            max-width: 1200px;
            margin: 20px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            padding: 0 20px;
        }
        .booking-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-8px);
        }
        .booking-card h3 {
            color: #003087;
            font-size: 1.6em;
        }
        .booking-card p {
            color: #444;
            margin: 10px 0;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Hilton Hotels</h2>
        <a href="home.php">Home</a>
        <a href="hotels.php">Hotels</a>
        <a href="bookings.php">My Bookings</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <h1>My Bookings</h1>
        </div>

        <div class="booking-list">
            <?php foreach ($bookings as $booking) { ?>
                <div class="booking-card">
                    <h3><?php echo htmlspecialchars($booking['name']); ?></h3>
                    <p>Location: <?php echo htmlspecialchars($booking['location']); ?></p>
                    <p>Check-in: <?php echo $booking['checkin']; ?></p>
                    <p>Check-out: <?php echo $booking['checkout']; ?></p>
                </div>
            <?php } ?>
            <?php if (empty($bookings)) { ?>
                <p>No bookings found.</p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
