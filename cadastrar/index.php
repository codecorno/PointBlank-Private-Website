<?php session_start();if (isset($_SESSION['usernames'])) {header("Location: ../index.php.php");}?>
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
					<p style="color:red;font-size:22px;"><span id="status-code"></span></p>

					<div class="wrap-input100 validate-input" data-validate="Insira um email válido: email@email.com">
						<span class="label-input100">Email</span>
						<input class="input100" type="text" id="email" name="email" placeholder="Endereço de Email...">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 validate-input" data-validate="Insira um usuário">
						<span class="label-input100">Usuário</span>
						<input class="input100" type="text" id="txtUsername" name="txtUsername" placeholder="Usuário...">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 validate-input" data-validate="Insira uma senha">
						<span class="label-input100">Senha</span>
						<input class="input100" type="password" id="txtPassword" name="txtPassword" placeholder="Senha">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 validate-input" data-validate="Repita sua senha">
						<span class="label-input100">Confirme a senha</span>
						<input class="input100" type="password" id="txtConPassword" name="txtConPassword" placeholder="Confirme sua senha">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 validate-input" data-validate="Repita sua senha">
						<span class="label-input100">Forneça o código a seguir</span>
						<img style="float:right" src="../captcha.php" id="captcha" /><br/>
						<input class="input100" type="text" id="captchaInput" name="captchaInput" autocomplete="off" placeholder="Captcha"> 
						<span class="focus-input100"></span>
					</div>
					<div class="flex-m w-full p-b-33">
						<div class="contact100-form-checkbox">
							<span class="txt1">
								Ao clicar em "Registrar" você aceita nossos
								<a href="#" class="txt2 hov1">
									Termos de uso.
								</a>
							</span>
							</label>
						</div>
					</div>
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" id="submitButton" type="button" onclick="signupUser()">
								Registrar
							</button>
						</div>

					</div>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript"> 

        function signupUser() {
            var email = document.getElementById("email").value;
            var Username = document.getElementById("txtUsername").value;
            var Password = document.getElementById("txtPassword").value;
            var captcha = document.getElementById("captchaInput").value;
			var txtConPassword = document.getElementById("txtConPassword").value;
            $.ajax({
                type: 'POST',
                url: 'save_register.php',
                data: {
                    'email': email,
                    'txtUsername': Username,
                    'txtPassword': Password,
                    'captcha': captcha,
					'txtConPassword': txtConPassword,
                },
                beforeSend: function() {
                    $('.submitButton').hide();
                    $('#status-code').html("<b style='color:gold'>Processando seu registro</b>");
                },
                success: function(data) {
                    $('#login_wait').html(data);
                    if (data == "Sucesso") {
                        $('#status-code').html("<p style='color:green'>Cadastrado com sucesso</p>");
                        setTimeout(' window.location.href = "../"; ', 2000);
                    }
					if (data == "Vazio") {
						$('#status-code').html("Complete todos os campos!");
                        $('.submitButton').show();
                    }
					if (data == "Diferentes") {
						$('#status-code').html("As senhas não são iguais!");
                        $('.submitButton').show();
                    }
					if (data == "Captcha") {
						$('#status-code').html("Captcha incorreto!");
                        $('.submitButton').show();
                    }
					if (data == "EmailUso") {
						$('#status-code').html("Email em uso!");
                        $('.submitButton').show();
                    }
					if (data == "UserUso") {
						$('#status-code').html("Usuário em uso!");
                        $('.submitButton').show();
                    }
                },
                error: function(err) {
                    alert(err);

                }
            });
        }
        $('#submitButton').click(function() {
            signupUser();
        });

       
    </script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="js/main.js"></script>

</body>

</html>