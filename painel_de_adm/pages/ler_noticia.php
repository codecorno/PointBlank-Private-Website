<?php
$rank =  pg_query("SELECT * FROM noticias");
$total = pg_num_rows($rank);
$id = @$_GET['id'];
$query = pg_query("SELECT * FROM noticias WHERE id = '$id';");
$num_rows = pg_num_rows($query);
if ($num_rows <= 0) {
	echo "<script>alert('Noticia nao existente.');</script><script>window.location = 'index.php';</script>";
}
$row = pg_fetch_assoc($query);
?>
<script>
	$(document).ready(function() {
		$(".del a").click(function() {
			if (!confirm("Do you want to delete")) {
				return false;
			} else {
				var contentId = $(this).attr('id');
				$.ajax({
					type: 'post',
					url: 'http://127.0.0.1/functions/deletarnew/',
					data: {
						id: contentId
					},

					success: function(data) {
						if (data == "Deletada com sucesso") {
							alert(data);
						} else {
							alert(data);
						}
						window.location = 'index.php?pg=allnews';
					},

					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert('Erro');
					},
				});
			}
		});
	});
</script>

<div class="page-title">
	<div class="title-env">
		<h1 class="title">Ler Noticia</h1>
	</div>

	<div class="breadcrumb-env">
		<ol class="breadcrumb bc-1" style="margin-top: 0;">
			<li>
				<a href="index.php"><i class="fa-home"></i>Home</a>
			</li>
			<li>
				<a href="?pg=allnews">Todas as noticias</a>
			</li>
			<li class="active">
				<strong>Ler Noticia</strong>
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
					<li class="active">
						<a href="#">Ler Noticia</a>
					</li>
					<li>
						<a href="#">Editar Noticia</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="col-sm-9 mailbox-right">
			<div class="mail-single">
				<div class="mail-single-header">
					<h2 style="font-size: 20px;">
						<?php echo $row['titulo']; ?>
						<a href="?pg=allnews" class="go-back"><i class="fa-angle-left"></i>Go Back</a>
					</h2>
					<div class="mail-single-header-options">
						<span class="del">
							<a href="" id="<?php echo $id; ?>" class="btn btn-gray btn-icon">
								<i class="fa-trash"></i>
							</a>
						</span>

						<a href="?pg=editnew&id=<?php echo $id; ?>" class="btn btn-gray btn-icon">
							<i class="fa-pencil"></i>
						</a>
					</div>
				</div>

				<div class="mail-single-info">
					<div class="mail-single-info-user" style="width:600px;">
						<a>
							<span><?php echo $row['autor']; ?> (<?php echo $row['tipo']; ?>)</span>
							<em class="time"><?php echo $row['data']; ?></em>
						</a>
					</div>
				</div>
				<p><?php echo $row['noticia']; ?></p>
			</div>
		</div>
	</div>
</section>