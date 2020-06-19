<?php

session_start();
if (isset($_SESSION['usernames'])){
	header("Location: ../home/");
}
    $mysqli = new mysqli('localhost', 'overpower', 'overpower', 'warface') or die (mysqli_error($mysqli));
    $mysqli->set_charset("utf8");
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = md5($_POST['password']);
    if ($_POST['process'] == "reg"){
    $result = $mysqli->query("SELECT * from users WHERE username='$username'");
    if($result->num_rows > 0)
    {
        // user exists
        header("Location: ../register/?error=existing-username");
    }else{
    $result = $mysqli->query("SELECT * from users WHERE email='$email'");
    if($result->num_rows > 0)
    {
        // user exists
        header("Location: ../register/?error=existing-email");
    }else{
   


    if ($mysqli->query("INSERT INTO users (pass, email, username)VALUES('$pass', '$email', '$username')")){
      $_SESSION['username'] = $username;
      $_SESSION['premium'] = '0';
      $_SESSION['email'] = $email;
    header("location: ../home/");
    }else{
    die ($mysqli->error); 
}}}}
    if ($_POST['process'] == 'login'){
        $result = $mysqli->query("SELECT * from users WHERE username='$username' and pass='$pass'");
        if($result->num_rows > 0)
    {
        $values = $result->fetch_assoc();
        $_SESSION['username'] = $values['username'];
        $_SESSION['premium'] = $values['premium'];
        $_SESSION['email'] = $values['email'];
        $_SESSION['time'] = $values['premium_time'];
        header("location: ../home/");
        }else{

            header("Location: ../login/?error=try-again");

        }


    }
