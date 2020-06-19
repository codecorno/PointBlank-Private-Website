<?php
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
   
		$id = $_POST["id"];
		$query = pg_query("DELETE FROM noticias WHERE id = '$id';");
		if(pg_affected_rows($query)>0){
			echo "Deletada com sucesso";
		}else{
			echo "merda";
		}
}
?>