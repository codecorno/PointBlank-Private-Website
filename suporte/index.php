<?php
error_reporting(0);
session_start();

include "../class/Ranking.class.php";
require_once('include.php');

if (isset($_SESSION['username'])) {

$rank =  pg_query("SELECT * FROM suporte WHERE nickname='$_SESSION[username]'");
$total_ticktes = pg_num_rows($rank);

$rank2 =  pg_query("SELECT * FROM suporte WHERE nickname='$_SESSION[username]' AND status='0'");
$total_ticktes_not = pg_num_rows($rank2);

$inicio = $_SESSION['username'];
$rank = pg_query("SELECT * FROM accounts WHERE login = '$inicio'");
$ranking = pg_fetch_assoc($rank);
}

date_default_timezone_set('America/Sao_Paulo');
?>
<html lang="en" class="app js no-touch no-android chrome no-firefox no-iemobile no-ie no-ie8 no-ie10 no-ie11 no-ios">
<head>  
	<meta charset="utf-8" />
	<title>Scale | Web Application</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
	<link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="css/animate.css" type="text/css" />
	<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
	<link rel="stylesheet" href="css/icon.css" type="text/css" />
	<link rel="stylesheet" href="css/font.css" type="text/css" />
	<link rel="stylesheet" href="css/app.css" type="text/css">
	<link rel="stylesheet" href="stylesheets/mypb2.css" />
</head>

<body style="background-color: #FFF;">
	<section class="vbox">
		<header class="bg-white header header-md navbar box-shadow">
			<div class="navbar-header aside-md">
				<a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen" data-target="#nav">
					<i class="fa fa-bars"></i>
				</a>
				<a href="index.php" class="navbar-brand">
					<img src="images/logo.png" alt="scale">
				</a>
			</div>
			<ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user user">
			<?php if (isset($_SESSION['username'])) { ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="thumb-sm avatar pull-left">
						</span>
						<?php echo $_SESSION['username']; ?> <b class="caret"></b>
					</a>
					<ul class="dropdown-menu animated fadeInRight">     
						<span class="arrow top"></span>
						<li><a href="../">Voltar</a></li>
						<li class="divider"></li>
						<li><a href="../logout.php">Sair</a></li>
					</ul>
				</li>
			<?php } else {?>
				<div style="margin-top:13px;margin-right:10px;">
					<a href="login.php" class="btn btn-success" >Logar</a>
					<a href="#" class="btn btn-primary" >Cadastrar</a>
				</div>
			<?php } ?>
			</ul>      
		</header>
	
		<section>
			<section class="hbox stretch">
						<?php			
				if(@$_GET['pg'] == ""){
					$_GET['pg'] = 'Meus_Tickets';
				}
				switch ($_GET['pg']){
					case 'Meus_Tickets':
						include 'pages/Meus_Tickets.php';
						break;
					case 'New_Ticket':
						include 'pages/New_Ticket.php';
						break;
					case 'Ler_Ticket':
						include 'pages/Ler_Ticket.php';
						break;
				} 
			?>
			</section>
		</section>
	</section>
	<script src="js/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="js/bootstrap.js"></script>
	<!-- App -->
	<script src="js/app.js"></script>  
	<script src="js/slimscroll/jquery.slimscroll.min.js"></script>
  
	<script src="js/app.plugin.js"></script>
	
	<script src="js/wysiwyg/jquery.hotkeys.js"></script>
	<script src="js/wysiwyg/bootstrap-wysiwyg.js"></script>
	<script src="js/wysiwyg/demo.js"></script>
</body>
</html>