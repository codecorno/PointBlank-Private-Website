<?php
$rank =  pg_query("SELECT * FROM noticias");$total = pg_num_rows($rank);

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
$pags = $rank->Totalnews();
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
				<strong>Todas as Noticias</strong>
			</li>
		</ol>
	</div>
</div>

<section class="mailbox-env">
	<div class="row">					
		<div class="col-sm-3 mailbox-left">
			<div class="mailbox-sidebar">
				<ul class="list-unstyled mailbox-list" style="margin-top: 13px;">
					<li class="active">
						<a href="#">Todas as noticias
						<span class="badge badge-blue pull-right"><?php echo $total; ?></span></a>
					</li>
					<li>
						<a href="?pg=addnew">Adicionar noticia</a>
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
				<table class="table mail-table" style="font-size: 13px;">
					<thead>
						<tr>
							<th class="col-cb"></th>
							<th colspan="4" class="col-header-options">
								<div class="mail-select-options"></div>
								<div class="mail-pagination" style="margin-right: -30px;">	
									Mostrando de <strong><?php echo $inicio+1; ?> até <?php if($pc*12 > $total){echo $total;}else{echo $pc*12;} ?></strong>
									de <strong><?php echo $total; ?></strong> Noticias
									<div class="next-prev">
									<?php
										if ($pc>1){ 
											echo "<a href='?pg=alltickets&pagina=$anterior'><i class='fa-angle-left'></i></a>"; 
										}else{
											echo "<a><i class='fa-angle-left'></i></a>"; 
										}
										if ($pc<$pags){ 
											echo "<a href='?pg=alltickets&pagina=$proximo'><i class='fa-angle-right'></i></a>"; 
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
							echo "<tr>";
							if ($rank->allnews($inicio)[$b]['tipo'] == "Eventos"){
								echo "<td style='padding: 0px 0px 1px 30px;'><span style='padding: 2px 12px 2px 12px;' class='label label-secondary'>Eventos</span></td>";
							}else if ($rank->allnews($inicio)[$b]['tipo'] == "Atualizacao"){
								echo "<td style='padding: 0 0 1px 29px;'><span class='label label-warning'>Atualizaçao</span></td>";
							}else{
								echo "<td style='padding: 0 0 1px 29px;'><span style='padding: 2px 15px 2px 15px;' class='label label-red'>Noticia</span></td>";
							}
							echo "<td class='col-name' style='width: 130px;text-align: center;'><a href='?pg=ler_noticia&id=".$rank->allnews($inicio)[$b]['id']."' class='col-name' style='color: #797979;font-weight: bold;'>".$rank->allnews($inicio)[$b]['autor']."</a></td>";
							echo "<td class='col-subject'><a href='?pg=ler_noticia&id=".$rank->allnews($inicio)[$b]['id']."' class='col-name'>".$rank->allnews($inicio)[$b]['titulo']."</a></td>";
                            echo "<td></td>";
							echo "<td style='width:14%;'>".date("d-m-Y", strtotime($rank->allnews($inicio)[$b]['data']))."</td>";
							
							$atual = count($rank->allnews($inicio));
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