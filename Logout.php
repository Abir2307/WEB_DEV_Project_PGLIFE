<?php
session_start();
session_destroy();
// Redirect to the desired URL
header("Location:index.php");
exit();
?>