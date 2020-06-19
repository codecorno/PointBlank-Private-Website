<?php
$id = $_GET['id'];
$resp = pg_query("SELECT * FROM suporte WHERE id = '$id';");
$resp1 = pg_fetch_assoc($resp);
$resp2 = $resp1['status'];
if ($id == ''){
	echo "<script>alert('Por Favor, insira o id do ticket!!');</script><script>window.location = '?pg=alltickets';</script>";
}else if ($resp2 == 0){
	echo "<script>alert('Ticket nao respondido!!');</script><script>window.location = '?pg=responder&id=$id';</script>";
}else if (pg_num_rows($resp)==0){
	echo "<script>alert('Ticket inexistente!!');</script><script>window.location = '?pg=alltickets';</script>";
}

$query = pg_query("SELECT * FROM suporte WHERE id = '$id';") or die(mysql_error());
$row = pg_fetch_assoc($query);
	
$inicio = $row['nickname'];
$rank = pg_query("SELECT * FROM accounts WHERE login = '$inicio'");
$ranking = pg_fetch_assoc($rank);
$inici1o = $ranking['email'];
?>

<div class="page-title">
	<div class="title-env">
		<h1 class="title">Responder Ticket</h1>
		<p class="description">Sempre ultilize de educação ao responder um ticket.</p>
	</div>
	
	<div class="breadcrumb-env">
		<ol class="breadcrumb bc-1">
			<li>
				<a href="index.php"><i class="fa-home"></i>Home</a>
			</li>
			<li>
				<a href="?pg=alltickets">todos os tickets</a>
			</li>
			<li class="active">
				<strong>Responder Ticket</strong>
			</li>
		</ol>
	</div>
</div>

<section class="mailbox-env">
	<div class="row">
		<div class="col-sm-12 mailbox-left">
			<div class="mail-single">
				<div class="mail-single-header">
					<h2>
						<?php echo $row['titulo']; ?>
						<a href="?pg=alltickets" class="go-back"><i class="fa-angle-left"></i>Voltar</a>
					</h2>
				</div>
							
				<div class="mail-single-info">
					<div class="mail-single-info-user" style="width:600px;">
						<a>
							<img src="../Ranking/PAT/<?php echo $ranking['rank']; ?>.gif" class="img-circle" width="38"> 
							<span><?php echo $inicio; ?></span> (<span><?php echo $ranking['player_name']; ?></span>)<br/>
							(<?php echo $inici1o; ?>) to <span>Point Blank Troll</span>
							<em class="time"></em>
						</a>					
					</div>
				</div>
							
				<p><?php echo $row['mensagem']; ?></p>
			</div>			
	
			<div class="mail-single">
				<div class="mail-single-header">
					<h2>
						Resposta
						<a href="?pg=alltickets" class="go-back"><i class="fa-angle-left"></i>Voltar</a>
					</h2>
				</div>
							
				<p><?php echo $row['resposta']; ?></p>
			</div>	
		</div>
	</div>
</section>