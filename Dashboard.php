<?php
session_start(); 

if (!isset($_SESSION['user_name'])) {
    // If not logged in, redirect to login page
    header("Location: /PG-Life/Login.html");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pglife";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'];

// Fetch interested properties for the current user
$sql = "SELECT p.* FROM properties p 
        INNER JOIN interested_users_properties iup ON p.id = iup.property_id
        WHERE iup.user_id = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching interested properties: " . mysqli_error($conn));
}

$interested_properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
<div class="header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../img/logo.png" alt="PGLIFE Logo" height="40">
            </a>        
    <div class="container">
        <h2>Welcome to your Dashboard, <?php echo $name; ?>!</h2>
        <p>You are logged in as: <?php echo $_SESSION['user_name']; ?></p>
        <a href="logout.php" class="btn btn-danger">Logout</a>

        <h3>Your Interested PGs</h3>

        <?php if (count($interested_properties) > 0): ?>
            <div class="row">
                <?php foreach ($interested_properties as $property): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                        <?php
                        $property_image_path = "../img/properties/" . $property['id'] . "/*";
                        $property_images = glob($property_image_path);
                        $image_src = !empty($property_images) ? $property_images[0] : '../img/default-property.jpg';
?>
                            <img src="<?= $image_src ?>" class="card-img-top" alt="Property Image">
                            <div class="card-body">
                                <h5 class="card-title"><?= $property['name'] ?></h5>
                                <p class="card-text"><?= $property['address'] ?></p>
                                <p class="card-text">Rent: ₹<?= number_format($property['rent']) ?>/- per month</p>
                                <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You have not shown interest in any PGs yet.</p>
        <?php endif; ?>
    </div>
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
        header("Location: logout.php");
        exit();
    }
}

// Update the last activity timestamp to the current time
$_SESSION['last_activity'] = time();
?>

</body>
</html>
