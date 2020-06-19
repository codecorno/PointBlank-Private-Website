<?php
if (!isset($_SESSION['username'])) {
	echo "<script>window.location = '../index.php';</script>";
	exit;
}

$not_resp = pg_query("SELECT * FROM suporte WHERE nickname='$_SESSION[username]' AND status='0'");
$ticktes_not = pg_num_rows($not_resp);
if ($ticktes_not >= 4){
	echo "<script>alert('Voce possui mais de 4 tickets pendentes. Aguarde!!');</script><script>window.location='./';</script>";
	exit;
}							
?>

<script type="text/javascript" src="js/nicEdit-latest.js"></script> 
<script type="text/javascript">
    bkLib.onDomLoaded(function() {
        new nicEditor({buttonList : ['fontSize','bold','italic','underline','forecolor','left','center','right','strikeThrough','html','link','unlink','upload']}).panelInstance('area1');
	});
</script>

<aside class="aside bg-primary hidden-xs" id="nav">
	<section class="vbox">
		<header class="header dker">
			<p class="h4 text-white">Meus Tickets</p>
		</header>
		<section class="w-f">
			<section>
				<section>
					<div class="wrapper">
						<ul class="nav nav-pills nav-stacked">
							<li>
								<a href="?pg=Meus_Tickets">
								<i class="fa fa-fw fa-inbox"></i>
								Meus Tickets
								</a>
							</li>
							<li class="active">
								<a href="?pg=New_Ticket">
								<i class="fa fa-fw fa-envelope-o"></i>
								Novo Ticket
								</a>
							</li>
							<li>
								<a href="#">
								<i class="fa fa-fw fa-bookmark-o"></i>                            
								Ler Ticket
								</a>
							</li>
						</ul>
					</div>
					<div class="line dk"></div>
				</section>
			</section>
		</section>
	</section>
</aside>

<section id="content">
    <section class="hbox stretch">
		<section>
			<section class="vbox">
				<section class="scrollable padder">   
					<div class="row">
						<div class="col-md-13">
							<section>
								<header class="header" style="border-left: 1px #177bbb solid;background-color: #0d5e92;">
									<p class="h4 text-white"><i class="fa fa-chevron-circle-down"></i> Enviar um ticket</p>
								</header><br>

								<div class="panel-body service_code">
									<form method="POST" class="form-horizontal">
										<table>
											<tbody>
												<tr>
													<td width="100"><label class="control-label">Title</label></td>
													<td width="600"><input class="form-control" style="margin: 5px 0 15px 0;" placeholder="" id="titulo" name="titulo"></td>
												</tr>
												
												<tr>	
													<td><label class="control-label">Message</label></td>
													<td>
														<textarea name="area1" cols="50" id="area1" style="height:250px;width:600px"></textarea>
													</td>
												</tr>
											</tbody>
										</table>
										<br>
										<div class="col-md-12">
											<center><input class="mb-xs mt-xs mr-xs btn btn-sm btn-primary" type="submit" name="go" value="Publicar"/></center>						
										</div>
									</form>
									<?php
									if($_POST['go']){
										if ($_POST['area1'] <> ""){
											if ($_POST['titulo'] <> ""){
												try{
													$not_resp = pg_query("SELECT * FROM suporte WHERE nickname='$_SESSION[username]' AND status='0'");
													$ticktes_not = pg_num_rows($not_resp);
													
													if ($ticktes_not >= 4){
														echo "<script>alert('Voce possui mais de 4 tickets pendentes. Aguarde!!');</script><script>window.location='./';</script>";
													}else{
														$result = pg_query("SELECT max(id) as id FROM suporte");
														$num_rows = pg_fetch_object($result);
														$id = $num_rows->id + 1;
														$strSQL1 = "INSERT INTO suporte (nickname, titulo, mensagem, status, id, create_date) VALUES ('".$_SESSION[username]."','".$_POST['titulo']."','".$_POST['area1']."','0','$id','".date("Y-m-d H:i:s")."')"; 
														$objQuery1 = pg_query($strSQL1);
														if(pg_affected_rows($objQuery1)>0){
															echo "<script>alert('Ticket Publicado. Aguarde um GM responder!');</script><script>window.location='./';</script>";
														}	
													}
												}catch(Exception $e){
													echo "Erro: ".$e->getMessage();
												}   
											}else{
												echo "<script>alert('O Titulo nao pode ficar em branco');</script>";
											}
										}else{
											echo "<script>alert('O ticket nao pode ficar em branco');</script>";
										}
									}
									?>
								</div>
							</section>
						</div>
					</div>
				</section>
			</section>
		</section>
	</section>
</section>