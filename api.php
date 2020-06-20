<?php 
if (!isset($_POST['username'])){header('location: index.php');}

function encripitar($senha){
	$salt = '/x!a@r-$r%an¨.&e&+f*f(f(a)';
	$output = hash_hmac('md5', $senha, $salt);
	return $output;
}

$password = $_POST['password'];
$username = $_POST['username'];
$encript = encripitar($password);


	if($username !== '' && $password !== ''){
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
			echo "404";
		}else{
			header('Content-type: text/javascript');
            echo json_encode($values, JSON_PRETTY_PRINT);
		}
	}else{
		echo "<script>alert('Usuario ou senha esta incorreta.');</script><script>window.history.back()</script>"; 
	}



?>