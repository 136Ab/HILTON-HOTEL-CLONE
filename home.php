<?php
session_start();
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Home</title>
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
            position: relative;
        }
        .header h1 {
            font-size: 2.8em;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .nav-links {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .nav-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #ff6200;
        }
        .search-container {
            background: url('https://images.unsplash.com/photo-1566073771259-6a8506099945') no-repeat center;
            background-size: cover;
            padding: 60px 20px;
            text-align: center;
            border-radius: 15px;
            margin: 30px auto;
            max-width: 1200px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }
        .search-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .search-box input, .search-box button {
            padding: 15px;
            margin: 10px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            transition: transform 0.3s ease;
        }
        .search-box input {
            width: 250px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        .search-box button {
            background: #ff6200;
            color: white;
            cursor: pointer;
        }
        .search-box button:hover {
            background: #e55a00;
            transform: scale(1.05);
        }
        .hotel-list {
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            padding: 0 20px;
        }
        .hotel-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hotel-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.4);
        }
        .hotel-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .hotel-card h3 {
            padding: 15px;
            font-size: 1.6em;
            color: #003087;
        }
        .hotel-card p {
            padding: 0 15px 15px;
            color: #444;
        }
        .hotel-card button {
            background: #ff6200;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 0 0 15px 15px;
            font-weight: 700;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .hotel-card button:hover {
            background: #e55a00;
            transform: scale(1.02);
        }
        @media (max-width: 768px) {
            .search-box input {
                width: 100%;
            }
            .header h1 {
                font-size: 2em;
            }
            .nav-links {
                position: static;
                margin-top: 15px;
            }
            .nav-links a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hilton Hotels</h1>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="bookings.php">My Bookings</a>
                <a href="logout.php">Logout</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Signup</a>
            <?php } ?>
        </div>
    </div>

    <div class="search-container">
        <div class="search-box">
            <input type="text" id="destination" placeholder="Destination" required>
            <input type="date" id="checkin" required>
            <input type="date" id="checkout" required>
            <button onclick="searchHotels()">Search Hotels</button>
        </div>
    </div>

    <div class="hotel-list">
        <?php
        $sql = "SELECT id, name, location, price_per_night, rating FROM hotels LIMIT 6";
        try {
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='hotel-card'>";
                    echo "<img src='https://images.unsplash.com/photo-1566073771259-6a8506099945' alt='Hotel'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['location']) . " - $" . $row['price_per_night'] . "/night</p>";
                    echo "<p>Rating: " . $row['rating'] . "/5</p>";
                    if (isset($_SESSION['user_id'])) {
                        echo "<button onclick='bookHotel(" . $row['id'] . ")'>Book Now</button>";
                    } else {
                        echo "<button onclick='alert(\"Please log in to book a hotel.\"); window.location.href=\"login.php\"'>Book Now</button>";
                    }
                    echo "</div>";
                }
            } else {
                error_log("Home page hotel fetch error: " . $conn->error, 3, "errors.log");
                echo "<p>Error fetching hotels. Please try again later.</p>";
            }
        } catch (Exception $e) {
            error_log("Home page exception: " . $e->getMessage(), 3, "errors.log");
            echo "<p>Error fetching hotels. Please try again later.</p>";
        }
        ?>
    </div>

    <script>
        function searchHotels() {
            const destination = document.getElementById('destination').value;
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;
            if (destination && checkin && checkout) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'hotels.php';
                form.innerHTML = `
                    <input type="hidden" name="destination" value="${destination}">
                    <input type="hidden" name="checkin" value="${checkin}">
                    <input type="hidden" name="checkout" value="${checkout}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please fill all fields');
            }
        }
        function bookHotel(id) {
            if (!id) {
                alert('Invalid hotel selection.');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'booking.php';
            form.innerHTML = `<input type="hidden" name="hotel_id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
