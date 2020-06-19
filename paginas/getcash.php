<?php 
error_reporting(0);
session_start();

if (!isset($_SESSION['username'])) {
	echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = 'index.php';</script>";
	exit;
}
require_once('include.php');

if($_POST['token']=='bz5NTA2ODJlZTRjZThlOF9CYXJlX0Zpc3RzLnBuZwRjZThlOF9c3RzLnBZpc3A2ODnBuZwRjZT'){ 
	$username = $_SESSION['username'];
	$timeout = 60*60*24; //60*60*24 seconds = 1 day 
	$time = time(); 
	$out = $time-$timeout;

	$sql = pg_query("SELECT * FROM accounts WHERE login='$_SESSION[username]';");
	while($row = pg_fetch_assoc($sql))
	$nf = $row[timegetcash];						 

	if($nf > $out){ 
		echo "<script>alert('Ja recebeu seu cash diário. Volte amanhã!');</script><script>window.history.back()</script>"; 
	}else{  
		pg_query("UPDATE accounts SET timegetcash = $time WHERE login = '$username'");						
		echo "<script>alert('Parabéns $username você recebeu 100 de cash com sucesso. Volte amanhã para receber mais!');</script><script>window.history.back()</script>";
		pg_query("UPDATE accounts SET money = money + 100 WHERE login = '$username'");
	}
} 
?>