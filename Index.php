<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGLIFE - Happiness per Square Foot</title>
    <link rel="stylesheet" href="../css/Home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="header sticky-top">
    <nav class="navbar navbar-expand-md navbar-light">
        <a class="navbar-brand" href="index.php">
            <img src="../img/logo.png" alt="PG Life Logo" />
        </a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#my-navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="my-navbar">
                <ul class="navbar-nav ms-auto">
                    <?php if (!isset($_SESSION['user_name'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../signup.html">Signup</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login.html">Login</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?= $_SESSION['user_name']; ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="property_list.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Logout.php">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</div>

    <div class="banner-container">
        <h2 class="white">Happiness per Square Foot</h2>
        <form id="search-form" action="property_list.php?city=" method="GET">
            <div class="input-group city-search">
                <input type="text" class="form-control input-city" id='city' name='city' placeholder="Enter your city to search for PGs" />
                <div>
                    <button type="submit" class="btn btn-secondary">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
    <style>.banner-container {
        background-image: url("../img/bg.png");
        background-size: cover;
        background-position: fixed;
        padding: 250px;
        text-align: center;
        box-shadow: inset 0 0 0 2000px rgba(0, 0, 0, 0.5);
        }
        @media (max-width: 768px) {
    .banner-container {
    padding: 100px;
    }}</style>

<div class="page-container">
    <h1 class="city-heading">Major Cities</h1>
    <div class="city-card-container">
        <div class="city-card">
            <a href="property_list.php?city=Delhi"><img src="../img/delhi.png" class="city-img" alt="Delhi" /></a>
        </div>
        <div class="city-card">
            <a href="property_list.php?city=Mumbai"><img src="../img/mumbai.png" class="city-img" alt="Mumbai" /></a>
        </div>
        <div class="city-card">
            <a href="property_list.php?city=Bangalore"><img src="../img/bangalore.png" class="city-img" alt="Bangalore" /></a>
        </div>
        <div class="city-card">
            <a href="property_list.php?city=Hyderabad"><img src="../img/hyderabad.png" class="city-img" alt="Hyderabad" /></a>
        </div>
    </div>
</div>


    <div class="footer-container">
        <div class="footer-city">
            <a href="property_list.php?city=Delhi">PG in Delhi</a>
        </div>
        <div class="footer-city">
            <a href="property_list.php?city=Mumbai">PG in Mumbai</a>
        </div>
        <div class="footer-city">
            <a href="property_list.php?city=Bangalore">PG in Bangalore</a>
        </div>
        <div class="footer-city">
            <a href="property_list.php?city=Hyderabad">PG in Hyderabad</a>
        </div>
        <div class="copyright">© 2020 Copyright PG Life</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php
// Set inactivity timeout duration (in seconds)
$timeout_duration = 900;  // 900 seconds = 15 minutes

// Check if the last activity timestamp exists
if (isset($_SESSION['last_activity'])) {
    // Calculate inactivity duration
    $elapsed_time = time() - $_SESSION['last_activity'];

    // If the elapsed time exceeds the timeout duration, log out the user
    if ($elapsed_time >= $timeout_duration) {
        // Redirect to logout page
        header("Location: Logout.php");
        exit();
    }
}

// Update the last activity timestamp to the current time
$_SESSION['last_activity'] = time();
?>
</body>
</html>
