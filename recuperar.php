<?php
echo "<script>alert('Este recurso esta desativado durante o beta!');</script><script>window.location = 'index.php';</script>";
exit;

$subject = ";
$message = "";
mail($to,$subject, $message )
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="xenoz @ i3coredev" />
	<title>Point Blank Troll</title>
	<link rel="stylesheet" type="text/css" href="stylesheets/css/login.css" media="screen" />
	<script src="stylesheets/js/jquery-1.11.0.min.js"></script>
	<link type="text/css" href="stylesheets/rec.css" rel="stylesheet">
</head>
<body>
<script src="stylesheets/javascript/rec.js"></script>
<div class="wrap">
	<div id="content">
		<p><img src="img/logo-troll.png" width="400" height="200" /></p>
		<div id="main">
			<div class="full_w">
				<div id="ativar_pincode">		
					<form id="rec" name="rec" onsubmit="return false;">
						<p>Digite o email cadastrado em sua conta:<br/><br/>
						<input style="width: 300px;" type="text" name="email"/></p><br/>
					
						<img src="captcha.php" id="captcha" style="margin-bottom: -11px;margin-right: 5px;"/>
						<input style="height: 30px;width: 68px;" type="text" name="captcha" id="captcha-form" autocomplete="off" /><br/><br/>

						<button class="read_more2" style="width: 68px;padding: 5px 2px 5px 2px;">Ativar</button>
					</form>
				</div>
				<div id="retorno_ativar_pincode" style="display:none;margin-left: 22px;height: 65px;">
					<div class="resultado_validacao">
						<div class="ico loading"></div>
						<p>Por favor aguarde...</p>
						<div class="loader"></div>
					</div>
				</div>
			</div>
			<div class="footer" style="color:#000;">COPYRIGHT&copy; 2016 MOMZGAMES ENTRETENIMENTO. TODOS DIREITOS RESERVADOS</div>
		</div>
  </div>
</div>
</body>
</html>
