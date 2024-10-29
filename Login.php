<?php
session_start();
$hostname = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "pglife";
$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn) {
    echo("Connection failed: " . mysqli_connect_error());
    exit;
}

// Check if the form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure both 'Email', 'Password', and 'city' are set
    if (isset($_POST['Email']) && isset($_POST['Password']) && isset($_POST['city'])) {
        $email = $_POST['Email'];
        $password = $_POST['Password'];
        $city = $_POST['city']; // Capture the city input

        // SQL query to check the user credentials
        $sql = "SELECT * FROM users WHERE Email='$email' AND Password='$password'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo "Error: " . mysqli_error($conn);
            exit;
        } else {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                // User is authenticated, set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['Full_Name'];

                // Redirect to the property listing page based on the selected city
                switch ($city) {
                    case 'Delhi':
                        header("Location: property_list.php?city=Delhi");
                        exit;
                    case 'Mumbai':
                        header("Location:  property_list.php?city=Mumbai");
                        exit;
                    case 'Bangalore':
                        header("Location:  property_list.php?city=Bangalore.php");
                        exit;
                    case 'Hyderabad':
                        header("Location:  property_list.php?city=Hyderabad.php");
                        exit;
                    default:
                        header("Location: property_list.php"); // Fallback if city is unrecognized
                        exit;
                }
            } else {
                // Invalid login credentials
                echo "Invalid Email or Password";
            }
        }
    }
}
mysqli_close($conn);
?>

