<?php
session_start();
$hostname="127.0.0.1";
$username="root";
$password="";
$dbname="pglife";

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if(!$conn){
    echo("Connection failed: " . mysqli_connect_error());
    exit;
}
$name = $_POST['Full_Name'];
$phone = $_POST['Phone_number'];
$email = $_POST['Email'];
$password = $_POST['Password'];
$college = $_POST['College_name'];
$gender = $_POST['Gender'];

$sql="INSERT INTO users (`Full_Name`, `Phone`,`Email`, `Password`,`College_Name`,`Gender`) VALUES ('$name', '$phone', '$email', '$password', '$college', '$gender')";
    if(mysqli_query($conn, $sql)){
        echo "Registration Successful";
    }else{
        echo "Error: Already Registerd";
        }
mysqli_close($conn);
?>