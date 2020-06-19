<?php
error_reporting(0);
session_start();

require_once('include.php');
include "class/Ranking.class.php";

date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['username'])) {
echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = '../index.php';</script>";
exit;
}

$sql = pg_query("SELECT * FROM accounts WHERE login='$_SESSION[username]';");
while( $row = pg_fetch_assoc($sql) )
if ($row[access_level] < 4){
	echo "<script>alert('Você nao tem privilegios sulficientes.');</script><script>window.location = '../index.php';</script>";
}

$result77 = pg_query("SELECT * FROM suporte WHERE status='0'");
$num_rows77 = pg_num_rows($result77);
$result772 = pg_query("SELECT * FROM suporte");
$num_rows772 = pg_num_rows($result772);
$result773 = pg_query("SELECT * FROM noticias");
$num_rows773 = pg_num_rows($result773);
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Xenon Boostrap Admin Panel" />
	<meta name="author" content="" />
	<title>Xenon - Dashboard 4</title>
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Arimo:400,700,400italic">
	<link rel="stylesheet" href="assets/css/fonts/linecons/css/linecons.css">
	<link rel="stylesheet" href="assets/css/fonts/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/xenon-core.css">
	<link rel="stylesheet" href="assets/css/xenon-forms.css">
	<link rel="stylesheet" href="assets/css/xenon-components.css">
	<link rel="stylesheet" href="assets/css/xenon-skins.css">
	<link rel="stylesheet" href="assets/css/custom.css">
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
    <script src="http://malsup.github.com/jquery.form.js"></script> 
	<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	<script>
	tinymce.init({
		selector: 'textarea',
		height: 300,
		theme: 'modern',
		plugins: [
			'advlist autolink lists link image charmap print preview hr anchor pagebreak',
			'searchreplace wordcount visualblocks visualchars code fullscreen',
			'insertdatetime media nonbreaking save table contextmenu directionality',
			'emoticons template paste textcolor colorpicker textpattern imagetools'
		],
		toolbar1: 'preview | forecolor backcolor | bold italic underline | alignleft aligncenter alignright | numlist | link image',
		image_advtab: true,
		templates: [
			{ title: 'Test template 1', content: 'Test 1' },
			{ title: 'Test template 2', content: 'Test 2' }
		],
		content_css: [
			'//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
			'//www.tinymce.com/css/codepen.min.css'
		]
	});

	$(document).ready(function() { 
		$('#checkform').ajaxForm({ 
			target: '#htmlExampleTarget', 
			success: function() { 
				$('#htmlExampleTarget').fadeIn('slow'); 
			} 
		}); 
	});
    </script> 
	
</head>

<body class="page-body wysihtml5-supported">
	<div class="page-container">	
		<div class="sidebar-menu toggle-others fixed">
			<div class="sidebar-menu-inner">	
				
				<header class="logo-env">
					<div class="logo">
						<a href="index.php" class="logo-expanded">
							<img src="assets/images/logo@2x.png" width="290" alt="" style="margin: -14px 0px -21px -1px;"/>
						</a>
					</div>		
				</header>
						
				<ul id="main-menu" class="main-menu">
					<li class="active">
						<a href="index.php">
							<i class="linecons-cog"></i>
							<span class="title">Administração</span>
						</a>
					</li>
					<li class="active">
						<a href="?pg=contas_ip">
							<i class="linecons-cog"></i>
							<span class="title">Contas por IP</span>
						</a>
					</li>
					<li class="active">
						<a href="?pg=idlocaliza">
							<i class="fa-search"></i>
							<span class="title">Localizar ID</span>
						</a>
					</li>
					<li class="active">
						<a href="?pg=addpin">
							<i class="linecons-key"></i>
							<span class="title">Adicionar PIN</span>
						</a>
					</li>
					<li class="active">
						<a href="?pg=gift">
							<i class="fa-gift"></i>
							<span class="title">Enviar Presente</span>
						</a>
					</li>
					<li class="active">
						<a href="?pg=alltickets">
							<i class="linecons-mail"></i>
							<span class="title">Tickets</span>
							<span class="label label-purple pull-right"><?php echo $num_rows772; ?></span>
						</a>
					</li>
					<li class="active">
						<a href="?pg=allnews">
							<i class="fa-newspaper-o"></i>
							<span class="title">Noticias</span>
							<span class="label label-blue pull-right"><?php echo $num_rows773; ?></span>
						</a>
					</li>
				</ul>	
			</div>
		</div>
		
		<div class="main-content">		
			<nav class="navbar user-info-navbar" role="navigation">
				<ul class="user-info-menu left-links list-inline list-unstyled">
					<li class="hidden-sm hidden-xs">
						<a href="#" data-toggle="sidebar">
							<i class="fa-bars"></i>
						</a>
					</li>
					
					<li class="dropdown hover-line">
						<a href="#" data-toggle="dropdown">
							<i class="fa-envelope-o"></i>
							<span class="badge badge-green"><?php echo $num_rows77; ?></span>
						</a>
							
						<ul class="dropdown-menu messages">
							<li>	
								<ul class="dropdown-menu-list list-unstyled ps-scrollbar">
									<?php
									$query = pg_query("SELECT * FROM suporte WHERE status='0' ORDER BY id DESC LIMIT 4") or die();
									while ($array = pg_fetch_object($query)) {
									echo "<li class='active'>";
										echo 
										"<a href='?pg=responder&id=".$array->id."'>
											<span class='line'>
												<strong>".$array->nickname."</strong>
											</span>
											
											<span class='line desc small'>
												".$array->titulo."
											</span>
										</a>";
									echo "</li>";
									} 
									
									?>								
								</ul>
							</li>
							
							<li class="external">
								<a href="?pg=alltickets">
									<span>Todos os Tickets</span>
									<i class="fa-link-ext"></i>
								</a>
							</li>
						</ul>
					</li>
				</ul>
				
				<ul class="user-info-menu right-links list-inline list-unstyled">
					<li class="dropdown user-profile">
						<a href="#" data-toggle="dropdown">
							<img src="../Ranking/PAT/53.gif" alt="user-image" style="top:6px;" class="img-rounded img-inline userpic-32" width="25" />
							<span>
								<?php echo $_SESSION['username']; ?>
								<i class="fa-angle-down"></i>
							</span>
						</a>
						
						<ul class="dropdown-menu user-profile-menu list-unstyled">
							<li>
								<a href="?pg=addnew">
									<i class="fa-newspaper-o"></i>
									Nova Noticia
								</a>
							</li>
							<li>
								<a href="?pg=accedit">
									<i class="linecons-cog"></i>
									Editar conta
								</a>
							</li>
							<li class="last">
								<a href="../logout.php">
									<i class="fa-lock"></i>
									Logout
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</nav>
			
			<?php			
			if(@$_GET['pg'] == ""){
				$_GET['pg'] = 'paineladm';
            }
            switch ($_GET['pg']){
                case 'paineladm':
                    include 'pages/home.php';
                    break;
				case 'addnew':
                    include 'pages/addnews.php';
                    break;
				case 'editnew':
                    include 'pages/editnews.php';
                    break;
				case 'contas_ip':
                    include 'pages/contasip.php';
                    break;
				case 'responder':
                    include 'pages/responder.php';
                    break;
				case 'accedit':
                    include 'pages/accounteditor.php';
                    break;
				case 'addpin':
                    include 'pages/addpin.php';
                    break;
				case 'allpins':
                    include 'pages/allpin.php';
                    break;
				case 'idlocaliza':
                    include 'pages/idlocate.php';
                    break;
				case 'alltickets':
                    include 'pages/alltickets.php';
                    break;
				case 'newtickets':
                    include 'pages/newtickets.php';
                    break;
				case 'resptickets':
                    include 'pages/resptickets.php';
                    break;
				case 'lerticket':
                    include 'pages/lerticket.php';
                    break;
				case 'gift':
                    include 'pages/gift.php';
                    break;
				case 'allnews':
                    include 'pages/allnews.php';
                    break;
				case 'ler_noticia':
                    include 'pages/ler_noticia.php';
                    break;
				case 'vipcount':
                    include 'pages/vip.php';
                    break;
            } 
			?>
		
			
			
		</div>
	</div>

	<!-- Imported styles on this page -->
	<link rel="stylesheet" href="assets/css/fonts/meteocons/css/meteocons.css">
	<link rel="stylesheet" href="assets/js/select2/select2.css">
	<link rel="stylesheet" href="assets/js/select2/select2-bootstrap.css">

	<!-- Bottom Scripts -->
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/TweenMax.min.js"></script>
	<script src="assets/js/resizeable.js"></script>
	<script src="assets/js/joinable.js"></script>
	<script src="assets/js/xenon-api.js"></script>
	<script src="assets/js/xenon-toggles.js"></script>
	<script src="assets/js/moment.min.js"></script>
	<link rel="stylesheet" href="assets/css/fonts/elusive/css/elusive.css">
	<script src="assets/js/toastr/toastr.min.js"></script>
	<!-- Imported scripts on this page -->
	
	<script src="assets/js/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="assets/js/jvectormap/regions/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/js/xenon-widgets.js"></script>
	<script src="assets/js/inputmask/jquery.inputmask.bundle.js"></script>
	<script src="assets/js/jquery-validate/jquery.validate.min.js" class=""></script>
	<script src="assets/js/wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
	<script src="assets/js/wysihtml5/src/bootstrap-wysihtml5.js"></script>
	<link rel="stylesheet" href="assets/js/wysihtml5/src/bootstrap-wysihtml5.css">
	<script src="assets/js/select2/select2.min.js"></script>
	<script src="assets/js/jquery-ui/jquery-ui.min.js"></script>
	<script src="assets/js/selectboxit/jquery.selectBoxIt.min.js"></script>
	
	<!-- JavaScripts initializations and stuff -->
	<script src="assets/js/xenon-custom.js"></script>
</body>
</html>