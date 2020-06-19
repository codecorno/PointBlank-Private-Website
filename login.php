<?php
error_reporting(0);
session_start();

function encripitar($senha){
	$salt = '/x!a@r-$r%an¨.&e&+f*f(f(a)';
	$output = hash_hmac('md5', $senha, $salt);
	return $output;
}

$form = $_POST['submit'];
$username = $_POST['username'];
$passwordori = $_POST['password'];
$id = $_POST['id'];
$encript = encripitar($passwordori);

if(isset($form)){
	if($username !== '' && $passwordori !== ''){
		$host        = "host=localhost";
		$port        = "port=5433";
		$dbname      = "dbname=postgres";
		$credentials = "user=postgres password=159753456";

		$conn = pg_connect( "$host $port $dbname $credentials" );
		$query = "SELECT * FROM accounts WHERE login = '$username' AND password = '$encript';";
		$result = pg_query($conn, $query);
		$resultado = pg_num_rows($result);
		$values = pg_fetch_all($result);
		if(pg_num_rows($result) != 1){
			echo "<script>alert('Usuario ou senha esta incorreta');</script><script>window.history.back()</script>";
		}else{
			$_SESSION['username'] = $_POST['username'];
			echo "<script>window.history.back()</script>";
		}
	}else{
		echo "<script>alert('Usuario ou senha esta incorreta.');</script><script>window.history.back()</script>"; 
	}
} 





?>