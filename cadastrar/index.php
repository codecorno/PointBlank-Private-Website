<?php
session_start();
if (isset($_SESSION['usernames'])) {

	header("Location: ../index.php.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
	<title>Registro | AzurePB</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="../images/icons/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="../fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="../vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="../vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="../css/util.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
</head>

<body style="background-color: #999999;">

	<div class="limiter">
		<div class="container-login100">
			<div class="login100-more" style="background-image: url('../../img/bg_login_top.jpg');background-position-y:0px;"></div>

			<div class="wrap-login100 p-l-50 p-r-50 p-t-72 p-b-50">
				<form class="login100-form validate-form" method="POST" action="../process/">
					<span class="login100-form-title p-b-59">
						Registre-se
					</span>
					<p style="color:red;font-size:16px;"><?php if (isset($_GET['error'])) {
																if ($_GET['error'] == "existing-username") {
																	echo "Usuário já existente";
																}
																if ($_GET['error'] == "existing-email") {
																	echo "Email em uso";
																}
															} ?></p>

					<div class="wrap-input100 validate-input" data-validate="Insira um email válido: ex@abc.xyz">
						<span class="label-input100">Email</span>
						<input class="input100" type="text" name="email" placeholder="Endereço de Email...">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 validate-input" data-validate="Insira um usuário">
						<span class="label-input100">Usuário</span>
						<input class="input100" type="text" name="username" placeholder="Usuário...">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Insira uma senha">
						<span class="label-input100">Senha</span>
						<input class="input100" type="password" name="password" placeholder="Senha">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Repita sua senha">
						<span class="label-input100">Confirme a senha</span>
						<input class="input100" type="password" name="password_again" placeholder="Confirme sua senha">
						<span class="focus-input100"></span>
					</div>
					<input type="hidden" name="process" value="reg">
					<div class="flex-m w-full p-b-33">
						<div class="contact100-form-checkbox">

							<span class="txt1">
								Ao clicar em "Registrar" você aceita nossos
								<a href="https://warface.cheaters.pro/terms" class="txt2 hov1">
									Termos de uso.
								</a>
							</span>
							</label>
						</div>


					</div>

					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" type="submit">
								Registrar
							</button>
						</div>

						<a href="../login/" class="dis-block txt3 hov1 p-r-30 p-t-10 p-b-10 p-l-30">
							Fazer login
							<i class="fa fa-long-arrow-right m-l-5"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>

</html>