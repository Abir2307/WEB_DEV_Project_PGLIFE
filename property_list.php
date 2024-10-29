<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    // If not logged in, redirect them to login page
    header("Location: /PG-Life/login.html");
    exit();
}

$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "pglife";       

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    echo "Connection failed: " . mysqli_connect_error();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$city_name = isset($_GET['city']) ? $_GET['city'] : '';
$properties = [];

if (empty($city_name)) {
    // Fetch properties with interested users count and check if the current user is interested
    $sql = "SELECT p.*, 
                   COUNT(iup.user_id) AS interested_count,
                   (SELECT COUNT(*) FROM interested_users_properties 
                    WHERE user_id = '$user_id' AND property_id = p.id) AS is_user_interested
            FROM properties p
            LEFT JOIN interested_users_properties iup 
            ON p.id = iup.property_id
            GROUP BY p.id";
} else {
    // Fetch properties with interested users count for a specific city and check if the current user is interested
    $sql = "SELECT p.*, 
                   COUNT(iup.user_id) AS interested_count,
                   (SELECT COUNT(*) FROM interested_users_properties 
                    WHERE user_id = '$user_id' AND property_id = p.id) AS is_user_interested
            FROM properties p
            LEFT JOIN interested_users_properties iup 
            ON p.id = iup.property_id
            INNER JOIN cities c ON p.city_id = c.id
            WHERE c.name = '$city_name'
            GROUP BY p.id";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "SQL error: " . mysqli_error($conn);
}

$properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in Major cities | PG Life</title>
    <link href="../css/property_list.css" rel="stylesheet" />
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
    <link href="../css/common.css" rel="stylesheet" />
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
            <ul class="navbar-nav ml-auto">
                <!-- Welcome message with the user's name -->
                <li class="nav-item">
                    <span class="nav-link">Welcome, <?= $_SESSION['user_name'] ?></span>
                </li>
                <!-- Link to dashboard -->
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                
                <!-- Logout link -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<div class="page-container">
    <div class="filter-bar row justify-content-around">
        <div class="col-auto" data-toggle="modal" data-target="#filter-modal">
            <img src="../img/filter.png" alt="filter" />
            <span>Filter</span>
        </div>
        <div class="col-auto">
            <img src="../img/desc.png" alt="sort-desc" />
            <span>Highest rent first</span>
        </div>
        <div class="col-auto">
            <img src="../img/asc.png" alt="sort-asc" />
            <span>Lowest rent first</span>
        </div>
    </div>

    <?php
    if (count($properties) > 0) {
        foreach ($properties as $property) {
            $property_images = glob("../img/properties/" . $property['id'] . "/*");
    ?>
            <div class="property-card row">
                <div class="image-container col-md-4">
                    <img src="<?= !empty($property_images) ? $property_images[0] : '../img/properties/default.jpg' ?>" alt="Property Image" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <?php
                        $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                        $total_rating = round($total_rating, 1);
                        ?>
                        <div class="star-container" title="<?= $total_rating ?>">
                            <?php
                            for ($i = 0; $i < 5; $i++) {
                                if ($total_rating >= $i + 0.8) {
                            ?>
                                    <i class="fas fa-star"></i>
                                <?php
                                } elseif ($total_rating >= $i + 0.3) {
                                ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php
                                } else {
                                ?>
                                    <i class="far fa-star"></i>
                            <?php
                                }
                            }
                            ?>
                        </div>
                        <div class="interested-container">
                        <?php
                        $is_user_interested = $property['is_user_interested'] > 0;  // Check if current user is interested
                        ?>
                        <i 
                        class="is-interested-image <?= $is_user_interested ? 'fas' : 'far' ?> fa-heart" 
                        data-property-id="<?= $property['id'] ?>" 
                        onclick="toggleInterested(this)">
                        </i> <!-- Filled heart for interested; empty heart for not interested -->
                        <div class="interested-user-count "><?= $property['interested_count'] ?> interested</div>
                    </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= $property['name'] ?></div>
                        <div class="property-address"><?= $property['address'] ?></div>
                        <div class="property-gender">
                            <img src="../img/<?= $property['gender'] ?>.png" alt="<?= ucfirst($property['gender']) ?>" />
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    } else {
    ?>
        <div class="no-property-container">
            <p>No PG to list</p>
        </div>
    <?php
    }
    ?>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="filter-heading">Filters</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h5>Gender</h5>
                <hr />
                <div>
                    <button class="btn btn-outline-dark btn-active">
                        No Filter
                    </button>
                    <button class="btn btn-outline-dark">
                        <i class="fas fa-venus-mars"></i>Unisex
                    </button>
                    <button class="btn btn-outline-dark">
                        <i class="fas fa-mars"></i>Male
                    </button>
                    <button class="btn btn-outline-dark">
                        <i class="fas fa-venus"></i>Female
                    </button>
                </div>
            </div>

            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-success">Okay</button>
            </div>
        </div>
    </div>
</div>
<div class="footer">
        <div class="page-container footer-container">
            <div class="footer-cities">
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
            </div>
            <div class="footer-copyright">© 2020 Copyright PG Life </div>
        </div>
    </div>
    <script type="text/javascript" src="../js/pselect.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/filter.js"></script>
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
