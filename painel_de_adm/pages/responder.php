<?php
$id = $_GET['id'];
$resp = pg_query("SELECT * FROM suporte WHERE id = '$id';");
$resp1 = pg_fetch_assoc($resp);
$resp2 = $resp1['status'];
if ($id == ''){
	echo "<script>alert('Por Favor, insira o id do ticket!!');</script><script>window.location = '?pg=alltickets';</script>";
}else if ($resp2 == 1){
	echo "<script>alert('Ticket já respondido!!');</script><script>window.location = '?pg=lerticket&id=$id';</script>";
}else if (pg_num_rows($resp)==0){
	echo "<script>alert('Ticket inexistente!!');</script><script>window.location = '?pg=alltickets';</script>";
}

$query = pg_query("SELECT * FROM suporte WHERE id = '$id'");
$row = pg_fetch_assoc($query);
	
$inicio = $row['nickname'];
$rank = pg_query("SELECT * FROM accounts WHERE login='$inicio'");
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
						<a href="?pg=alltickets" class="go-back"><i class="fa-angle-left"></i>Go Back</a>
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
	
			<div class="mail-compose">
				<div class="mail-header">
					<div class="row">
						<div class="col-sm-6">							
							<h3>
								<i class="linecons-pencil"></i>
								Resposta
							</h3>
						</div>
					</div>
				</div>
				
				<form method="POST" role="form">			
					<div>
						<textarea class="form-control" data-html="false" data-color="true" data-stylesheet-url="assets/css/wysihtml5-color.css" name="sample_wysiwyg" id="sample_wysiwyg"></textarea>
					</div>
					<br/>
					<div class="row">
						<div class="col-sm-3">
							<input type="submit" name="hrthr" value="Responder" class="btn btn-secondary btn-block" style="width:90px;"/>
						</div>			
					</div>				
				</form>		
				<?php
				if($_POST['hrthr']){
					if ($_POST['sample_wysiwyg'] != ''){
						$resposta = $_POST['sample_wysiwyg'];
						$gm = $_SESSION['username'];
						$query1 = pg_query("UPDATE suporte SET status='1', resposta='$resposta', gm='$gm', resp_date='".date("Y-m-d H:i:s")."' WHERE nickname='$inicio' AND id='$id'");
						if(pg_affected_rows($query1)==1){
							echo "<script>alert('Ticket Respondido!!');</script><script>window.location = '?pg=lerticket&id=$id';</script>";
						}else if(pg_affected_rows($query1)>1){
							echo "<script>alert('Parecem haver tickets duplicados, contate um ADM.');</script>";
						}else if(pg_affected_rows($query1)<1){
							echo "<script>alert('Algo deu errado, contate um ADM.".pgsql_error()."');</script>";
						}
					}else{
						echo "<script>alert('A caixa de resposta nao pode estar vazia!');</script>";
					}
				}
				?>
			</div>
							
			
		</div>
	</div>
</section>