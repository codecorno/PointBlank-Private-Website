<?php
error_reporting(0);
session_start();

require_once('class/class.phpmailer.php');
require_once('class/class.smtp.php');
require_once('include.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['email'];
	$captcha = $_POST['captcha'];
	$captchacorrect = $_SESSION['cap_code'];
	if ($username == '' || $captcha == ''){
			echo "Complete Todos os Campos.";
		}elseif ($captcha != $captchacorrect){
			echo "O Captcha esta Errado.";
		}else{
			$query = "SELECT * FROM accounts WHERE email = '$username';";
			$resultrow = pg_query($query);
			if (pg_num_rows($resultrow) == 1){
				$sql = "SELECT * FROM accounts WHERE email = '$username' ";
				$query = pg_query($sql);
				while ($rows = pg_fetch_array($query)){
					$dbuser = $rows['login'];
					$dbpassword = $rows['password'];
				}
		
				$mail= new PHPMailer;
				$mail->IsSMTP();        // Ativar SMTP
				$mail->SMTPDebug = false;       // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
				$mail->SMTPAuth = true;     // Autenticação ativada
				$mail->SMTPSecure = 'ssl';  // SSL REQUERIDO pelo GMail
				$mail->Host = 'mail.momzgames.com'; // SMTP utilizado
				$mail->Port = 465; 
				$mail->Username = 'suporte@momzgames.com';
				$mail->Password = '66HVp64qht';
				$mail->SetFrom('suporte@momzgames.com', 'MoMz Games');
				$mail->addAddress($username,'');
				$mail->Subject=("Dados Cadastrais");
				$body = "
				<style type='text/css'>
				body {
					margin:0px;
					font-family:Verdane;
					font-size:12px;
					color: #666666;
				}
				a{
					color: #666666;
					text-decoration: none;
				}
				a:hover {
					color: #FF0000;
					text-decoration: none;
				}
				</style>
				<html>
					<div style='font-size:15px;color:#000;'>
						<img src='http://i.imgur.com/2rAgKIT.png' style='width: 400px;margin-bottom: -20px;'>
						<p style='border-bottom:3px solid #000;width:430px;'></p>
						<p style='width:390px;'><br>Foi solicitado em nossos
							sistemas o reenvio de sua senha. Confira seu Usuario 
							e sua senha, caso tenha problemas, contate o suporte:</p><p>
						<br><br>
						<b style='margin-left: 16px;'>Usuario:</b> $dbuser<br>
						<b style='margin-left: 30px;'>Senha</b>: $dbpassword<br><br></p>
						<p style='border-bottom:3px solid #000;color:red;width:430px;'></p>
						<div>Atenciosamente,<span style='color:#080EFF;'> Equipe - Point Blank Troll 2016</span></div>
						<a style='color:rgb(255,0,0);' href='http://pb.momzgames.com' target='_blank'>www.momzgames.com</a>
					</div>
				</html>";
				$mail->msgHTML($body);
				if(!$mail->Send()) { 
					echo $Email->ErrorInfo; 
				} else { 
					echo "Email enviado com sucesso!";	
				}
			}else{
				echo "Email nao encontrado.";
			}
		}
}
?>