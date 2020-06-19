<?php
if (!isset($_SESSION['username'])) {
	echo "<script>window.location = 'index.php';</script>";
	exit;
}

$rank = new Ranking();
			
$pagina=$_GET['pagina']; 
if ($pagina == null) {
	$pc = "1"; 
	$pagina = "1";
} else { 
	$pc = $pagina; 
}
			
$inicio = $pc - 1; 
$inicio = $inicio * 20;
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
							<li class="active">
								<a href="?pg=Meus_Tickets">
									<span class="badge bg-info pull-right"><?php echo $total_ticktes;?></span>
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
									<p class="h4 text-white"><i class="fa fa-chevron-circle-right"></i> Meus Tickets</p>
								</header><br>
								
								<?php if ($rank->totaltickets($_SESSION['username']) == 0){ ?>
									<div class="row">
										<div class="col-md-11">
											<div class="panel-body">
												<p class="text-center" style="font-size: 25px;color:Red;">Sem tickets criados</p>
											</div>
										</div>
									</div>
								<?php }else{ ?>
									<div class="row">
										<div class="col-md-11" style="margin-left: 38px;">
											<table class="table table-bordered mb-none">
												<thead>
													<tr>
														<th style="background: #333;width: 15px;"><p class="text-white">Nº</p></th>
														<th style="background: #333;"><p class="text-white">Titulo</p></th>
														<th style="background: #333;"><p class="text-white">Status</p></th>
														<th style="background: #333;"><p class="text-white">GM</p></th>
													</tr>
												</thead>
												<tbody>
													<?php for($b = 0; $b < 20; $b++){ ?>
													<tr>
													<td style="color: white;width: 15px;">
														<a style="text-decoration: none;" href="?pg=Ler_Ticket&nick=<?php echo $_SESSION[username];?>&id=<?php echo $rank->tickets($inicio)[$b]['id'];?>"/>
														<?php echo $rank->tickets($inicio)[$b]['id']; ?></span>
														</a>
													</td>
													
													<td style="color: white;width: 500px;">
														<a style="text-decoration: none;" href="?pg=Ler_Ticket&nick=<?php echo $_SESSION[username];?>&id=<?php echo $rank->tickets($inicio)[$b]['id'];?>"/>
														<?php echo $rank->tickets($inicio)[$b]['titulo']; ?></span>
														</a>
													</td>
													
													<td style="width: 175px;">
														<a style="text-decoration: none;" href="?pg=Ler_Ticket&nick=<?php echo $_SESSION[username];?>&id=<?php echo $rank->tickets($inicio)[$b]['id'];?>"/>
															<?php $rank->statusticket($rank->tickets($inicio)[$b]['status']); ?>
														</a>
													</td>
													
													<td style="width: 175px;">
														<a style="text-decoration: none;" href="?pg=Ler_Ticket&nick=<?php echo $_SESSION[username];?>&id=<?php echo $rank->tickets($inicio)[$b]['id'];?>"/>
														<?php echo $rank->tickets($inicio)[$b]['gm']; ?>
														</a>
													</td>
													<?php                   
														$atual = count($rank->tickets($inicio));
														if($b > $atual - 2){ break; }
														echo "</tr>";
													} 
													?>
												</tbody>
											</table>
											
											<div class="col-lg-4">
											</div>
											
											<div class="col-lg-4" style="margin-bottom:20px">
												<div class="btn-toolbar">
													<?php		
														$pags = ceil($rank->totaltickets($_SESSION['username'])); 
														$max_links = 5;
														
														echo "<div class='btn-group'>";
														if($pc == 1){
															echo "<a type='button' href='#' class='btn btn-default' target='_self'><</a>";
														}else{
															echo "<a type='button' href='?pg=Meus_Tickets&pagina=".($pc-1)."' class='btn btn-default' target='_self'><</a>";
														}
														echo "</div>";
														
														echo "<div class='btn-group'>";
															for($a = $pagina-$max_links; $a <= $pagina-1; $a++) {
																if($a <=0) { }else { 
																	echo "<a type='button' href='?pg=Meus_Tickets&pagina=$a' class='btn btn-default' target='_self'>$a</a>"; 
																} 
															} 
															
															if($pc == 1){
																echo "<a type='button' href='#' class='btn btn-default active'>1</a>"; 
															}else{
																echo "<a type='button' href='#' class='btn btn-default active'>$pc</a>";
															}

															for($a = $pagina+1; $a <= $pagina+$max_links; $a++) {
																if($a > $pags) {
																}else{ 
																	echo "<a type='button' href='?pg=Meus_Tickets&pagina=$a' class='btn btn-default' target='_self'>$a</a>"; 
																} 
															} 
														echo "</div>";
														
														echo "<div class='btn-group'>";
															if($pc == $pags){
																echo "<a type='button' href='#' class='btn btn-default' target='_self'>></a>";
															}else{
																echo "<a type='button' href='?pg=Meus_Tickets&pagina=".($pc+1)."' class='btn btn-default' target='_self'>></a>";
															}
														echo "</div>";
													?>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
							</section>
						</div>
					</div>
				</section>
			</section>
		</section>
	</section>
</section>