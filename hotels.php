<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to view hotels.'); window.location.href = 'login.php';</script>";
    exit;
}

$destination = isset($_POST['destination']) ? $_POST['destination'] : '';
$checkin = isset($_POST['checkin']) ? $_POST['checkin'] : '';
$checkout = isset($_POST['checkout']) ? $_POST['checkout'] : '';
$_SESSION['destination'] = $destination;
$_SESSION['checkin'] = $checkin;
$_SESSION['checkout'] = $checkout;

$hotels = [];
try {
    $sql = $destination ? "SELECT id, name, location, price_per_night, rating FROM hotels WHERE location LIKE ?" : "SELECT id, name, location, price_per_night, rating FROM hotels";
    $stmt = $conn->prepare($sql);
    if ($destination) {
        $searchTerm = "%$destination%";
        $stmt->bind_param("s", $searchTerm);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
} catch (Exception $e) {
    error_log("Hotels page exception: " . $e->getMessage(), 3, "errors.log");
    echo "<p>Error fetching hotels. Please try again later.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hotels - Listings</title>
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
        .filter-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .filter-container select, .filter-container input {
            padding: 12px;
            margin: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .hotel-list {
            max-width: 1200px;
            margin: 20px auto;
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
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
            .filter-container select, .filter-container input {
                width: 100%;
                margin: 10px 0;
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
            <h1>Available Hotels</h1>
        </div>

        <div class="filter-container">
            <select id="sort">
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="rating_desc">Rating: High to Low</option>
            </select>
            <input type="number" id="price_max" placeholder="Max Price">
            <input type="number" id="rating_min" placeholder="Min Rating">
        </div>

        <div class="hotel-list" id="hotel-list">
            <?php foreach ($hotels as $hotel) { ?>
                <div class="hotel-card" data-price="<?php echo $hotel['price_per_night']; ?>" data-rating="<?php echo $hotel['rating']; ?>">
                    <img src='https://images.unsplash.com/photo-1566073771259-6a8506099945' alt='Hotel'>
                    <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                    <p><?php echo htmlspecialchars($hotel['location']); ?> - $<?php echo $hotel['price_per_night']; ?>/night</p>
                    <p>Rating: <?php echo $hotel['rating']; ?>/5</p>
                    <button onclick="bookHotel(<?php echo $hotel['id']; ?>)">Book Now</button>
                </div>
            <?php } ?>
            <?php if (empty($hotels)) { ?>
                <p>No hotels found. Try a different destination.</p>
            <?php } ?>
        </div>
    </div>

    <script>
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

        document.getElementById('sort').addEventListener('change', filterHotels);
        document.getElementById('price_max').addEventListener('input', filterHotels);
        document.getElementById('rating_min').addEventListener('input', filterHotels);

        function filterHotels() {
            const sort = document.getElementById('sort').value;
            const priceMax = document.getElementById('price_max').value || Infinity;
            const ratingMin = document.getElementById('rating_min').value || 0;
            const hotels = document.querySelectorAll('.hotel-card');

            let sortedHotels = Array.from(hotels).filter(hotel => {
                const price = parseFloat(hotel.dataset.price);
                const rating = parseFloat(hotel.dataset.rating);
                return price <= priceMax && rating >= ratingMin;
            });

            if (sort === 'price_asc') {
                sortedHotels.sort((a, b) => a.dataset.price - b.dataset.price);
            } else if (sort === 'price_desc') {
                sortedHotels.sort((a, b) => b.dataset.price - a.dataset.price);
            } else if (sort === 'rating_desc') {
                sortedHotels.sort((a, b) => b.dataset.rating - a.dataset.rating);
            }

            const hotelList = document.getElementById('hotel-list');
            hotelList.innerHTML = '';
            sortedHotels.forEach(hotel => hotelList.appendChild(hotel));
            if (sortedHotels.length === 0) {
                hotelList.innerHTML = '<p>No hotels match your filters.</p>';
            }
        }
    </script>
</body>
</html>
