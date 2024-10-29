<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pglife";

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$property_id =$_POST['property_id'] ?? null;

if (!$property_id) {
    echo json_encode(["status" => "error", "message" => "No property ID provided"]);
    exit();
}

// Check if the user already marked this property as "interested"
$sql = "SELECT * FROM interested_users_properties WHERE user_id = '$user_id' AND property_id = '$property_id'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Database query failed: " . mysqli_error($conn)]);
    exit();
}

if (mysqli_num_rows($result) > 0) {
    // User is already interested; remove the row
    $sql = "DELETE FROM interested_users_properties WHERE user_id = '$user_id' AND property_id = '$property_id'";
    $action = "removed";
} else {
    // User is not interested; add a row
    $sql = "INSERT INTO interested_users_properties (user_id, property_id) VALUES ('$user_id', '$property_id')";
    $action = "added";
}

// Execute the action
if (mysqli_query($conn, $sql)) {
    // Fetch the updated interested count
    $sql = "SELECT COUNT(*) AS interested_count FROM interested_users_properties WHERE property_id = '$property_id'";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo json_encode(["status" => "error", "message" => "Failed to fetch interested count: " . mysqli_error($conn)]);
        exit();
    }
    
    $row = mysqli_fetch_assoc($result);
    $interested_count = $row['interested_count'];

    echo json_encode([
        "status" => "success",
        "message" => "Interest $action",
        "interested_count" => $interested_count
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update interest: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
