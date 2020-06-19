<?php
session_start();
error_reporting(1);

function encripitar($senha){
	$salt = '/x!a@r-$r%an¨.&e&+f*f(f(a)';
	$output = hash_hmac('md5', $senha, $salt);
	return $output;
}

function getRealIpAddr(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

$host        = "host=localhost";
$port        = "port=5433";
$dbname      = "dbname=postgres";
$credentials = "user=postgres password=159753456";
$db = pg_connect( "$host $port $dbname $credentials"  );
   
$result = pg_query("SELECT * FROM accounts");// Limite de email por contas
$num_rows = pg_num_rows($result);
$_SESSION['id'] = $num_rows + 1;
$captcha = $_POST['captcha'];
$captchacorrect = $_SESSION['cap_code'];
$ip = getRealIpAddr();

$result3 = pg_query("SELECT * FROM accounts WHERE lastip='".$ip."'");
$num_rows2 = pg_num_rows($result3);
$redrirac = "../index.php";

	if(trim($_POST["txtUsername"]) == ""){
		echo("<script> alert('Complete todos os campos!');</script><script>window.location='".$redrirac."';</script>");
		exit();	
	}elseif(trim($_POST["txtPassword"]) == ""){
		echo("<script> alert('Complete todos os campos!');</script><script>window.location='".$redrirac."';</script>");
		exit();	
	}elseif(trim($_POST["txtConPassword"]) == ""){
		echo("<script> alert('Complete todos os campos!');</script><script>window.location='".$redrirac."';</script>");
		exit();	
	}elseif(trim($_POST["email"]) == ""){
		echo("<script> alert('Complete todos os campos!');</script><script>window.location='".$redrirac."';</script>");
		exit();	
	}elseif(trim($_POST["captcha"]) == ""){
		echo("<script> alert('Complete todos os campos!');</script><script>window.location='".$redrirac."';</script>");
		exit();	
	}elseif($_POST["txtPassword"] != $_POST["txtConPassword"]){
		echo("<script> alert('As senhas nao combinam!');</script><script>window.location='".$redrirac."';</script>");
		exit();	
	}elseif (trim($_POST['captcha']) != $_SESSION['cap_code']){
		echo "<script>alert('O Captcha esta Errado.');</script><script>window.location='".$redrirac."';</script>";
		exit();
	}else{
		$strSQL5 = "SELECT * FROM accounts WHERE email = '".trim($_POST["email"])."' ";// Verifica email
		$objQuery5 = pg_query($strSQL5);
		$objResult5 = pg_fetch_array($objQuery5);
		if($objResult5){
			echo("<script> alert('Este e-mail já está em uso!');</script><script>window.location='".$redrirac."';</script>");
			exit();
		}else{
			$strSQL = "SELECT * FROM accounts WHERE login = '".trim($_POST['txtUsername'])."' "; // Verifica  ID
			$objQuery = pg_query($strSQL);
			$objResult = pg_fetch_array($objQuery);
			if($objResult){
				echo("<script> alert('Este usuario já está em uso!');</script><script>window.location='".$redrirac."';</script>");
				exit();
			}else{	
				$strSQL = "INSERT INTO accounts (login,password,player_id,email,gp,money,token,cad_ip) VALUES ('".$_POST["txtUsername"]."','".encripitar($_POST["txtPassword"])."','".$_SESSION['id']."','".$_POST["email"]."',100000,100000,'".encripitar($_POST["txtUsername"])."','".$ip."')";   //Alterar Cash e Gold inicial //Change cash and gold
				$objQuery = pg_query($strSQL);
		
				$_SESSION['username'] = $_POST['txtUsername'];
				echo "<script>alert('Conta criada com sucesso.');</script><script>window.location='../index.php';</script>";
			}
		}
	
	
	pg_close();
	
}
?>
