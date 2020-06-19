<?php
session_Start();
if (isset($_session['username'])){
	echo "Sem permissão!";
	exit();
}
$rank =  pg_query("SELECT * FROM noticias");$total = pg_num_rows($rank);
$rank2 =  pg_query("SELECT max(id) as id FROM noticias");$total2 = pg_fetch_object($rank2);
?>

<div class="page-title">
	<div class="title-env">
		<h1 class="title">Todas as Noticias</h1>
		<p class="description">Verifique sua ortografia ao criar uma nova noticia..</p>
	</div>
	
	<div class="breadcrumb-env">
		<ol class="breadcrumb bc-1">
			<li>
				<a href="index.php"><i class="fa-home"></i>Home</a>
			</li>
			<li class="active">
				<strong>Adicionar Noticia</strong>
			</li>
		</ol>
	</div>
</div>

<section class="mailbox-env">
	<div class="row">					
		<div class="col-sm-3 mailbox-left">
			<div class="mailbox-sidebar">
				<ul class="list-unstyled mailbox-list" style="margin-top: 13px;">
					<li>
						<a href="?pg=allnews">Todas as noticias
						<span class="badge badge-blue pull-right"><?php echo $total; ?></span></a>
					</li>
					<li class="active">
						<a href="#">Adicionar noticia</a>
					</li>
					<li>
						<a href="#">Ler Noticia</a>
					</li>
					<li>
						<a href="#">Editar Noticia</a>
					</li>
				</ul>			
			</div>
		</div>
		
		<div class="col-sm-9 mailbox-right">
			<div class="mail-env">
				<form method="post">
					<div style="border-bottom: 1px solid #DDD;padding: 0px 0px 9px 5px;">
						<div class="input-group" style="width: 300px;margin-left:5px;margin-top:-5px;">
							<span class="input-group-addon">Titulo</span>
							<input name="titulo" type="text" class="form-control" style="width: 300px;">
						</div>
						<select name='tipo' id="tipo" class="form-control" style="width: 146px;margin: -32px 0px 0px 372px;">
							<option value="Eventos">Evento</option>
							<option value="Atualizacao">Atualizaçao</option>
							<option value="Noticias">Noticia/comunicado</option>
							<option value="Punicoes">Punicoes</option>
							<option value="Avisos">Avisos</option>
						</select>
						<input type="submit" class="btn btn-blue btn-sm" name="hrthr" value="Enviar noticia" style="margin-top: -31px;float: right;margin-right: 24px;">
					</div>
					<div style="width: 617px;margin: 11px;">
						<textarea name="noticia" id="noticia"></textarea>
					</div>	
				</form>
			</div>
		</div>
		<?php
			if($_POST['hrthr']){
				if ($_POST['noticia'] == ''){
					echo "<script>alert('Noticia vazia.');</script><script>window.location='#';</script>";
				}else{
					if ($_POST['titulo'] == ''){
						echo "<script>alert('Titulo vazio! erro.');</script><script>window.location='#';</script>";
					}else{
						$id2 = $total2->id + 1;
						$strSQL1 = "INSERT INTO noticias (titulo, noticia, data, autor, id, tipo) VALUES ('".$_POST["titulo"]."','".$_POST["noticia"]."','".date('Y-m-d')."','".$_SESSION["username"]."', '$id2', '".$_POST["tipo"]."')"; 
						$objQuery1 = pg_query($strSQL1);
						if(pg_affected_rows($objQuery1)>0){
							echo "<script>alert('Noticia Publicada com sucesso!');</script><script>window.location='index.php';</script>";
						}					
					}
				}
			}
			?>
	</div>		
</section>