<?php
function encripitar($senha){
	$salt = '/x!a@r-$r%an¨.&e&+f*f(f(a)';
	$output = hash_hmac('md5', $senha, $salt);
	return $output;
}


if (!isset($_SESSION['username'])) {
echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = 'index.php';</script>";
exit;
}

$query = pg_query("SELECT * FROM accounts WHERE login = '$_SESSION[username]';");
$row = pg_fetch_assoc($query);

if(@$_POST['go']){
		$username = $_SESSION['username'];
		$password = $_POST['oldpassword'];
		$pass = $_POST['oldpass'];
		$npassword = $_POST['newpassword'];
		$rpassword = $_POST['rpassword'];
		$captcha = $_POST['captcha'];
		$captchacorrect = $_SESSION['cap_code'];

		$sql = "SELECT * FROM accounts WHERE login = '$username' ";
		$query = pg_query($sql);
		$numrows = pg_num_rows($query);

		while ($rows = pg_fetch_array($query)){
			$dbuser = $rows['login'];
			$dbpassword = $rows['password'];
			$dbkey = $rows['uniqueid'];
		}

		if ($pass == '' || $npassword == '' || $rpassword == ''){
			echo "<script>alert('Complete Todos os Campos.');</script><script>window.location='#';</script>";
		}elseif ($npassword != $rpassword){
			echo "<script>alert('A confirmação de senha não corresponde.');</script><script>window.location='#';</script>";
		}elseif (encripitar($pass) != $dbpassword){
			echo "<script>alert('Senha Incorretos.');</script><script>window.location='#';</script>";
		}elseif ($key != $dbkey){
			echo "<script>alert('KeyID Incorretos.');</script><script>window.location='#';</script>";
		}elseif ($npassword == $pass){
			echo "<script>alert('Sua senha nova é igual a antiga.');</script><script>window.location='#';</script>";
		}elseif ($captcha != $captchacorrect){
			echo "<script>alert('O Captcha esta Errado. $captchacorrect');</script><script>window.location='#';</script>";
		}else{
			$encryptpass = encripitar($npassword);

			pg_query("UPDATE accounts SET password = '$encryptpass' WHERE login = '$username'");
			echo "<script>alert('Senha Alterada com sucesso.');</script><script>window.location='index.php';</script>";
		}
}
?>
	<div id="left_wrapper">
        <div class="header">
           <h2><span>PB Troll //</span> Trocar Senha</h2>
        </div>
		<center><br/>
				<table>
					<form name="submit" action="<?php $PHP_SELF; ?>" method="post">
					<tbody>
						<tr>
							<td style="color:orange;">Senha Antiga</td>
							<td><input name="oldpass" type="password" maxlength="18" class="text" style="text-align:center" autocomplete="off"></td>
							<td></td>
							<td></td>
						</tr>

						<tr>
							<td style="color:orange;">Nova Senha</td>
							<td><input name="newpassword" type="password" maxlength="16" class="text" style="text-align:center" autocomplete="off"></td>
							<td></td>
							<td></td>
						</tr>

						<tr>
							<td style="color:orange;">Confirmar Senha</td>
							<td><input name="rpassword" type="password" maxlength="16" class="text" style="text-align:center" autocomplete="off"></td>
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