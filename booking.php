<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to book a hotel.'); window.location.href = 'login.php';</script>";
    exit;
}

$hotel_id = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : 0;
$_SESSION['hotel_id'] = $hotel_id;

// Validate user
try {
    $sql = "SELECT id, username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user_result = $stmt->get_result();
    if ($user_result->num_rows === 0) {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        error_log("Booking error: User ID {$_SESSION['user_id']} not found", 3, "errors.log");
        echo "<script>alert('Session invalid. Please log in again.'); window.location.href = 'login.php';</script>";
        exit;
    }
    $user = $user_result->fetch_assoc();
    $_SESSION['username'] = $user['username']; // Ensure username is set
} catch (Exception $e) {
    error_log("User validation exception: " . $e->getMessage(), 3, "errors.log");
    echo "<script>alert('Database error. Please try again.'); window.location.href = 'hotels.php';</script>";
    exit;
}

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['username'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];

    // Validate inputs
    if (!$hotel_id || !$checkin || !$checkout) {
        $message = "Error: All fields are required.";
    } else {
        try {
            // Verify hotel exists
            $sql = "SELECT id, name, location, price_per_night FROM hotels WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $hotel_id);
            $stmt->execute();
            $hotel_result = $stmt->get_result();
            if ($hotel_result->num_rows === 0) {
                $message = "Error: Hotel not found.";
                error_log("Booking error: Hotel ID $hotel_id not found", 3, "errors.log");
                echo "<script>alert('Invalid hotel selection.'); window.location.href = 'hotels.php';</script>";
                exit;
            }

            // Insert booking
            $sql = "INSERT INTO bookings (hotel_id, user_id, user_name, checkin, checkout) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisss", $hotel_id, $user_id, $user_name, $checkin, $checkout);
            if ($stmt->execute()) {
                $message = "Booking confirmed!";
                unset($_SESSION['hotel_id']);
                unset($_SESSION['checkin']);
                unset($_SESSION['checkout']);
            } else {
                $message = "Error: Could not complete booking.";
                error_log("Booking insert error: " . $conn->error, 3, "errors.log");
            }
        } catch (Exception $e) {
            $message = "Error: Database issue.";
            error_log("Booking exception: " . $e->getMessage(), 3, "errors.log");
        }
    }
}

// Fetch hotel details
try {
    $sql = "SELECT id, name, location, price_per_night FROM hotels WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel = $stmt->get_result()->fetch_assoc();
    if (!$hotel) {
        error_log("Hotel fetch error: Hotel ID $hotel_id not found", 3, "errors.log");
        echo "<script>alert('Invalid hotel selection.'); window.location.href = 'hotels.php';</script>";
        exit;
    }
} catch (Exception $e) {
    error_log("Hotel fetch exception: " . $e->getMessage(), 3, "errors.log");
    echo "<script>alert('Database error. Please try again.'); window.location.href = 'hotels.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Booking</title>
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
        .booking-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .booking-container h2 {
            color: #003087;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        .booking-container input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .booking-container button {
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
        .booking-container button:hover {
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
        @media (max-width: 768px) {
            .booking-container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Book Your Stay</h1>
    </div>

    <div class="booking-container">
        <h2><?php echo htmlspecialchars($hotel['name']); ?></h2>
        <p>Location: <?php echo htmlspecialchars($hotel['location']); ?></p>
        <p>Price: $<?php echo $hotel['price_per_night']; ?>/night</p>
        <?php if (isset($message)) { ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>"><?php echo $message; ?></p>
        <?php } ?>
        <form method="POST">
            <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
            <input type="hidden" name="confirm_booking" value="1">
            <input type="text" name="user_name" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
            <input type="date" name="checkin" value="<?php echo isset($_SESSION['checkin']) ? htmlspecialchars($_SESSION['checkin']) : ''; ?>" required>
            <input type="date" name="checkout" value="<?php echo isset($_SESSION['checkout']) ? htmlspecialchars($_SESSION['checkout']) : ''; ?>" required>
            <button type="submit">Confirm Booking</button>
        </form>
    </div>
</body>
</html>
