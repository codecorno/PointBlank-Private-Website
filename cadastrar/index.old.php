<?php
error_reporting(0);
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="author" content="MoMzGames @ Point Blank Troll" />
<title>Cadastrar Conta</title>
<link rel="stylesheet" type="text/css" href="../stylesheets/css/login.css" media="screen" />

<script type='text/javascript'>
function check_email(elm){
    var regex_email=/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*\@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([a-zA-Z]){2,4})$/
    if(!elm.value.match(regex_email)){
        alert('Please enter a valid email address');
    }else{

}
}
</script>
</head>
<body>
	<div class="wrap">
	<div id="content">
<div style="background: url('https://www.azurepb.net/img/Logo-topo.png') no-repeat; background-size: 500px; width:500px; height:120px; margin: 20px 0px 0px -40px"></div><div id="main">
		<br>
		<br>
		<br>
		<br>
			<div class="full_w">
					<form method="post" action="save_register.php">
						
					<label for="login">Usuário:</label>
					<input name="txtUsername" type="text" id="txtUsername" class="text" maxlength="15" />

					<label for="pass">Senha:</label>
					<input name="txtPassword" type="password" id="txtPassword" maxlength="16" class="text"/>

					<label for="pass">Confirmar senha :</label>
					<input name="txtConPassword" type="password" id="txtConPassword" maxlength="16" class="text"/>

					<label for="email">E-mail*:</label>
					<input name="email" onblur="check_email(this)" size="45" id="email" type="email" class="text"/>
					
					<img src="../captcha.php" id="captcha" /><br/>
					<input style="margin-top: 5px;" type="text" name="captcha" id="captcha" autocomplete="off" /><br/><br/>
					
					<input type="checkbox" name="check1"><span style="vertical-align: super;">Eu li e aceito os <a href="#">Termos de uso e condiçoes</a></span></input><br/>
					<div class="sep"></div>
					<button type="submit" name="submit" class="ok">Cadastrar</button> 
					<br>
					<br>
					<b><p align="center" style="color:white; background-color:green;"><font size="2">Lembre-se de preencher com seus dados corretos!</font></p></b>
				    <?php
						echo "<br /><span style=\"color:red\">$error</span>";
					?>
				</form>
		  </div>
			<div style="color:#000;" class="footer">COPYRIGHT© 2020 - AZURE PB</div>
	  </div>
  </div>
</div>
</body>
</html>