<?php 
if (!isset($_SESSION['username'])) {
	echo "<script>window.location = 'index.php';</script>";
	exit;
}
$rank = new Ranking();
$id = @$_GET['id'];
$nick = @$_GET['nick'];
if ($nick != $_SESSION['username']){
	echo "<script>alert('Ticket Inexistente.');</script><script>window.location = 'index.php';</script>";
	exit;
}
$query = pg_query("SELECT * FROM suporte WHERE id = '$id' AND nickname='$nick';");
if (pg_num_rows($query) != 1){
	echo "<script>alert('Ticket Inexistente.');</script><script>window.location = '?pg=suporte';</script>";
	exit;
}
while($row = pg_fetch_assoc($query)){
?>
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
							<li>
								<a href="?pg=New_Ticket">
								<i class="fa fa-fw fa-envelope-o"></i>
								Novo Ticket
								</a>
							</li>
							<li class="active">
								<a href="#">
								<i class="fa fa-fw fa-bookmark-o"></i>                            
								Ler Tickets
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
									<p class="h4 text-white"><i class="fa fa-chevron-circle-up"></i> Ler Ticket</p>
								</header><br>
								
								<div class="playone_cs_detail_list">
									<table class="table">
										<tbody>
											<tr style="border-top: 1px #ccc solid;">
												<td width="100" class="tt text-center">Titulo</td>
												<td><b><?php echo $row[titulo];?></b></td>
											</tr>
											<tr>
												<td class="tt text-center">Data</td>
												<td><b><?php echo date("d.m.Y  H:i:s", strtotime($row[create_date]));?></b></td>
											</tr>
											<tr>
												<td class="tt text-center">Status</td>
												<td><?php $rank->statusticket($row[status]); ?></td>
											</tr>
										</tbody>
									</table>
									
									<div class="playone_cs_history">
										<div class="playone_cs_header">Mensagem</div>
										
										<div class="playone_cs_contents">
											<div class="content-reply bglistqa">
												<span class="pull-left"><b><?php echo $row[nickname]; ?></b></span>
												<span class="pull-right"><?php echo date("d.m.Y  H:i:s", strtotime($row[create_date]));?></span>
												<br><br>
												<?php echo $row[mensagem];?>
											</div>		
										</div>
										
										<div class="playone_cs_detail">Resposta</div>
										<?php if ($row[status] <= 0) {?>
										<div class="playone_cs_contents">
											<div class="content-reply bglistqa">
												<p class="text-center" style="color:Red;font-size:14px;">Sem Resposta Até o momento</p>
											</div>	
											
											<div class="row text-center">
												<br><a href="index.php"><img src="http://img.playone.asia/playone/cs/Portal_CS_MyQuestion_detail_03.gif"></a>
											</div>
										</div>
										<?php }else{ ?>
										<div class="playone_cs_contents">
											<div class="content-reply bglistqa">
												<span class="pull-left"><b><?php echo $row[gm];?></b></span>
												<span class="pull-right"><?php echo date("d.m.Y  H:i:s", strtotime($row[resp_date]));?></span>
												<br><br>
												<?php echo $row[resposta];?>
											</div>		
													
											<div class="row text-center">
												<br><a href="index.php"><img src="http://img.playone.asia/playone/cs/Portal_CS_MyQuestion_detail_03.gif"></a>
											</div>	
										</div>
										<?php } ?>
									</div>
								</div>	
							</section>
						</div>
					</div>
				</section>
			</section>
		</section>
	</section>
</section>
<?php } ?>