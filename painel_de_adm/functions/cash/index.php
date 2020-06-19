<?php
error_reporting(0);
session_start();
if (!isset($_SESSION['username'])) {
	echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = 'index.php';</script>";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$host        = "host=localhost";
	$port        = "port=5432";
	$dbname      = "dbname=postgres";
	$credentials = "user=postgres password=123456";
	$db = pg_connect( "$host $port $dbname $credentials"  );
   
	$valor1 = $_POST['number'];
	$login = $_POST['asdasd'];
	
	if ($valor1 == "" || $login == ""){
		echo "Nao podem haver campos vazios.";
	}else{
		$strSQL1 = "UPDATE accounts SET money=money+'$valor1' WHERE login='$login'"; 
		$objQuery1 = pg_query($strSQL1);
		if(pg_affected_rows($objQuery1)>0){
			$rank = pg_query("SELECT * FROM accounts WHERE login = '$login'");
			$ranking = pg_fetch_assoc($rank);
			echo "Cash Total: ".str_replace(",", ".", number_format($ranking['money']))."";
		}else{
			echo "Alguma coisa deu errado.";
		}
	}
}
?>