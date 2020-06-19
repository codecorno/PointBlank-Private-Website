<?php
session_start();
if (!isset($_SESSION['username'])) {
	echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = 'index.php';</script>";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($_POST['captcha'] == $_SESSION['cap_code']) {
		$host        = "host=localhost";
   $port        = "port=5432";
   $dbname      = "dbname=postgres";
   $credentials = "user=postgres password=123456";
		$db = pg_connect( "$host $port $dbname $credentials"  );
   
		$pin = $_POST["pin"];

		$sql = pg_query("SELECT * FROM pin WHERE pin = '$pin'");
		if(pg_num_rows($sql)>0){
			while($verifica = pg_fetch_assoc($sql)){
				$valor1 = $verifica['valor'];
			}
			$query1 = pg_query("UPDATE accounts SET money=money+'$valor1' WHERE login='$_SESSION[username]'");
			if(pg_affected_rows($query1)>0){
				$query4 = pg_query("SELECT * FROM top_pin WHERE usuario='$_SESSION[username]'");
				if(pg_num_rows($query4)>0){
					$attpintop = pg_query("UPDATE top_pin SET valortotal=valortotal+'$valor1' WHERE usuario='$_SESSION[username]'");
				}else{
					$result = pg_query("SELECT * FROM top_pin");
					$num_rows = pg_num_rows($result);
					$idit = $num_rows + 1;
					$strSQL1 = "INSERT INTO top_pin (id,usuario,valortotal) VALUES ('".$idit."','$_SESSION[username]','".$valor1."')"; 
					$objQuery1 = pg_query($strSQL1);
				}		
				$query32 = pg_query("DELETE FROM pin WHERE pin = '$pin'");
				echo "Pin-Code ativado com sucesso";
			}else{
				echo "Erro Interno";
			}
		}else{
			echo "Pin code Invalido";
		}
	}else{
		echo "O Captcha esta errado!";
	}	
}
?>