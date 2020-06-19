<?php
header('Location: index.php');
session_start();

// if the user is logged in, unset the session

unset($_SESSION['username']);

session_destroy();

// now that the user is logged out,

// go to login page

echo "<script>window.history.back()</script>";

?>