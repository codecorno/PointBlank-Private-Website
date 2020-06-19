<?php
if (!isset($_SESSION['username'])) {
echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = 'index.php';</script>";
exit;
}

if (isset($_SESSION['username'])) {
echo "<script>alert('Este recurso esta desativado durante o beta!');</script><script>window.location = 'index.php';</script>";
exit;
}

$query = pg_query("SELECT * FROM accounts WHERE login = '$_SESSION[username]';");
while($row = pg_fetch_assoc($query))
$uniqueid = $row[uniqueid];
if ($uniqueid == '') {
echo "<script>alert('Por Favor, Gere sua key primeiro!');</script><script>window.location='?pg=keyid';</script>";
exit;
}

function keyid($q){
	$cryptKey  = 'dqwoindqwoiiwiw1982912n';
    $qEncoded      = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey) ) ) );
    return( $qEncoded );
}

if(@$_POST['go']){
		$username = $_SESSION['username'];
		$pass = $_POST['oldmail'];
		$npassword = $_POST['newmail'];
		$rpassword = $_POST['rmail'];
		$key = keyid($_POST['keyid']);
		$captcha = $_POST['captcha'];
		$captchacorrect = $_SESSION['cap_code'];
			
		$sql = "SELECT * FROM accounts WHERE login = '$username' ";
		$query = pg_query($sql);

		while ($rows = pg_fetch_array($query)){
        $dbuser = $rows['login'];
        $dbpassword = $rows['email'];
		$dbkey = $rows['uniqueid'];
		}
		
		if ($pass == '' || $npassword == '' || $rpassword == '' || $captcha == '' || $key == ''){
			echo "<script>alert('Complete Todos os Campos.');</script><script>window.location='#';</script>";
		}elseif ($npassword != $rpassword){
			echo "<script>alert('A confirmação de email não corresponde.');</script><script>window.location='#';</script>";
		}elseif ($pass != $dbpassword || $key != $dbkey){
			echo "<script>alert('Dados Incorretos.');</script><script>window.location='#';</script>";
		}elseif ($npassword == $pass){
			echo "<script>alert('O novo email é igual ao antigo email.');</script><script>window.location='#';</script>";
		}elseif ($captcha != $captchacorrect){
			echo "<script>alert('O Captcha esta Errado.');</script><script>window.location='#';</script>";
		}else{
			$encryptpass = $npassword;
			pg_query("UPDATE accounts SET email = '$encryptpass' WHERE login = '$username'");
			echo "<script>alert('Email Alterado com sucesso.');</script><script>window.location='index.php';</script>";
		}
}
?>
	<div id="left_wrapper">
        <div class="header">
           <h2><span>PB Troll //</span> Trocar Email</h2>
        </div>		
		<center><br/>	
				<table>
					<form name="submit" action="<?php $PHP_SELF; ?>" method="post">	
					<tbody>
						<tr>
							<td style="color:orange;">Email Antigo</td>
							<td><input name="oldmail" type="text" maxlength="30" class="text" style="text-align:center" autocomplete="off"></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td style="color:orange;">Email Novo</td>
							<td><input name="newmail" type="text" maxlength="30" class="text" style="text-align:center" autocomplete="off"></td>
							<td></td>
							<td></td>
						</tr>									
						<tr>
							<td style="color:orange;">Confirmar Email</td>
							<td><input name="rmail" type="text" maxlength="30" class="text" style="text-align:center" autocomplete="off"></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td style="color:orange;">Key ID</td>
							<td><input name="keyid" type="text" maxlength="15" class="text" style="text-align:center" autocomplete="off"></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table><br/>
						<img src="captcha.php" id="captcha" style="margin-bottom: -11px;">
						<input style="margin-left: 7px;width: 56px;" type="text" name="captcha" id="captcha-form" autocomplete="off"><br/><br/>
						<input type="submit" name="go" class="read_more2" value="Salvar" style="display: inline-block;width: 80px;"></input> 
					</form>
				</center>
	</div>