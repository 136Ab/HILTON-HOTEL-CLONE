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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .header {
            background: #003087;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .header h1 {
            font-size: 2.5em;
            letter-spacing: 2px;
        }
        .search-container {
            background: url('https://images.unsplash.com/photo-1566073771259-6a8506099945') no-repeat center;
            background-size: cover;
            padding: 50px 20px;
            text-align: center;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        .search-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
        }
        .search-box input, .search-box button {
            padding: 12px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
        }
        .search-box input {
            width: 200px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-box button {
            background: #ff6200;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-box button:hover {
            background: #e55a00;
        }
        .featured-hotels {
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 0 20px;
        }
        .hotel-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        .hotel-card:hover {
            transform: translateY(-5px);
        }
        .hotel-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .hotel-card h3 {
            padding: 15px;
            font-size: 1.5em;
            color: #003087;
        }
        .hotel-card p {
            padding: 0 15px 15px;
            color: #555;
        }
        @media (max-width: 768px) {
            .search-box input {
                width: 100%;
            }
            .header h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hilton Hotels</h1>
    </div>

    <div class="search-container">
        <div class="search-box">
            <input type="text" id="destination" placeholder="Destination" required>
            <input type="date" id="checkin" required>
            <input type="date" id="checkout" required>
            <button onclick="searchHotels()">Search Hotels</button>
        </div>
    </div>

    <div class="featured-hotels">
        <?php
        $sql = "SELECT * FROM hotels LIMIT 3";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<div class='hotel-card'>";
            echo "<img src='https://images.unsplash.com/photo-1566073771259-6a8506099945' alt='Hotel'>";
            echo "<h3>" . $row['name'] . "</h3>";
            echo "<p>" . $row['location'] . " - $" . $row['price_per_night'] . "/night</p>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
        function searchHotels() {
            const destination = document.getElementById('destination').value;
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;
            if (destination && checkin && checkout) {
                localStorage.setItem('destination', destination);
                localStorage.setItem('checkin', checkin);
                localStorage.setItem('checkout', checkout);
                window.location.href = 'hotels.php';
            } else {
                alert('Please fill all fields');
            }
        }
    </script>
</body>
</html>
