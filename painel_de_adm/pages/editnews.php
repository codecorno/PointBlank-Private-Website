<?php
$rank =  pg_query("SELECT * FROM noticias");
$total = pg_num_rows($rank);
$id = @$_GET['id'];
$query = pg_query("SELECT * FROM noticias WHERE id = '$id';");
$num_rows = pg_num_rows($query);
if ($num_rows <= 0){
	echo "<script>alert('Noticia nao existente.');</script><script>window.location = 'index.php';</script>";
}else{
	$row = pg_fetch_assoc($query);
	$titulo = $row[titulo];
	$mensagem = $row[noticia];
	$tipo = $row[tipo];
}
?>

<div class="page-title">
	<div class="title-env">
		<h1 class="title">Editar Noticia</h1>
		<p class="description">Verifique sua ortografia ao criar uma nova noticia..</p>
	</div>
	
	<div class="breadcrumb-env">
		<ol class="breadcrumb bc-1">
			<li>
				<a href="index.php"><i class="fa-home"></i>Home</a>
			</li>
			<li class="active">
				<strong>Editar Noticia</strong>
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
					<li>
						<a href="?pg=addnew">Adicionar noticia</a>
					</li>
					<li>
						<a href="#">Ler Noticia</a>
					</li>
					<li class="active">
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
							<input name="titulo" type="text" class="form-control" style="width: 300px;" value="<?php echo $titulo; ?>">
						</div>
						<select name='tipo' id="tipo" class="form-control" style="width: 146px;margin: -32px 0px 0px 372px;">
							<option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
							<option value="Eventos">Evento</option>
							<option value="Atualizacao">Atualizaçao</option>
							<option value="Noticias">Noticia/comunicado</option>
						</select>
						<input type="submit" class="btn btn-blue btn-sm" name="hrthr" value="Atualizar New" style="margin-top: -31px;float: right;margin-right: 24px;">
					</div>
					<div style="width: 617px;margin: 11px;">
						<textarea name="noticia" id="noticia"><?php echo $mensagem; ?></textarea>
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
						$strSQL1 = "UPDATE noticias SET titulo='".$_POST["titulo"]."',tipo='".$_POST["tipo"]."', noticia='".$_POST["noticia"]."', data='".date('d-m-Y')."', autor='".$_SESSION["username"]."' WHERE id='$id'";
						$objQuery1 = pg_query($strSQL1);
						if(pg_affected_rows($objQuery1)>0){
							echo "<script>alert('Noticia Atualizada com sucesso!');</script><script>window.location='?pg=ler_noticia&id=$id';</script>";
						}					
					}
				}
			}
			?>
	</div>		
</section>