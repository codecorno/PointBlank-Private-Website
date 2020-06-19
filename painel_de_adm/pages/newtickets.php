<?php
$rank =  pg_query("SELECT * FROM suporte");$total = pg_num_rows($rank);
$rank1 =  pg_query("SELECT * FROM suporte WHERE status='0'");$news = pg_num_rows($rank1);
$rank2 =  pg_query("SELECT * FROM suporte WHERE status='1'");$resps = pg_num_rows($rank2);

$rank = new Ranking();
			  	
$pagina=$_GET['pagina']; 
if ($pagina == null) {
	$pc = "1"; 
}else{ 
	$pc = $pagina; 
}
			
$inicio = $pc - 1; 
$inicio = $inicio * 12;

$anterior = $pc - 1; 
$proximo = $pc + 1;
$pags = $rank->Totalnewtickets();
?>

<div class="page-title">
	<div class="title-env">
		<h1 class="title">Tickets</h1>
		<p class="description">Sempre ultilize de educação ao responder um ticket.</p>
	</div>
	
	<div class="breadcrumb-env">
		<ol class="breadcrumb bc-1">
			<li>
				<a href="index.php"><i class="fa-home"></i>Home</a>
			</li>
			<li class="active">
				<strong>Tickets</strong>
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
						<a href="?pg=alltickets">Todos os Tickets
						<span class="badge badge-blue pull-right"><?php echo $total; ?></span></a>
					</li>
					<li class="active">
						<a href="#">Novos Tickets
						<span class="badge badge-red pull-right"><?php echo $news; ?></span></a>
					</li>
					<li>
						<a href="?pg=resptickets">Tickets respondidos
						<span class="badge badge-green pull-right"><?php echo $resps; ?></span></a>
					</li>
				</ul>			
			</div>
		</div>
		
		<div class="col-sm-9 mailbox-right">
			<div class="mail-env">
				<table class="table mail-table" style="font-size: 13px;"> 
					<thead>
						<tr>
							<th class="col-cb"></th>
							<th colspan="4" class="col-header-options">
								<div class="mail-select-options">Mostrando de <strong><?php echo $inicio+1; ?> até <?php if($pc*12 > $news){echo $news;}else{echo $pc*12;} ?></strong>
								de <strong><?php echo $news; ?></strong> Tickets</div>
						
								<div class="mail-pagination">	
									<div class="next-prev">
									<?php
										if ($pc>1){ 
											echo "<a href='?pg=newtickets&pagina=$anterior'><i class='fa-angle-left'></i></a>"; 
										}else{
											echo "<a><i class='fa-angle-left'></i></a>"; 
										}
										if ($pc<$pags){ 
											echo "<a href='?pg=newtickets&pagina=$proximo'><i class='fa-angle-right'></i></a>"; 
										}else{
											echo "<a><i class='fa-angle-right'></i></a>"; 
										}
										?>
									</div>
								</div>
							</th>
						</tr>
					</thead>
					
					<tbody>
					<?php
						for($b = 0; $b < 12; $b++){
							echo "<tr class='unread'>";
							echo "<td style='padding: 0 0 1px 35px;'><span class='label label-red'>Pendente</span></td>";
							echo "<td class='col-name'><a href='?pg=responder&id=".$rank->newtickets($inicio)[$b]['id']."' class='col-name' style='display: block;max-width: 80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;'>".$rank->newtickets($inicio)[$b]['titulo']."</a></td>";
							echo "<td class='col-subject'><a style='display: block;max-width: 255px;max-height: 16px;' href='?pg=responder&id=".$rank->newtickets($inicio)[$b]['id']."' class='col-name'>".$rank->newtickets($inicio)[$b]['mensagem']."</a></td>";
							echo "<td class='col-options hidden-sm hidden-xs'></td>";
							echo "<td style='width:22%; text-align:center;'>".$rank->newtickets($inicio)[$b]['nickname']."</td>";
                                   
							$atual = count($rank->newtickets($inicio));
							if($b > $atual - 2){ break; }
							echo "</tr>";				
						}
					?>
					</tbody>
				</table>				
			</div>
		</div>
		
	</div>		
</section>