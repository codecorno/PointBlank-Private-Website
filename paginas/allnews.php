<div id="left_wrapper">
	<div class="header">
           <h2><span>PB Troll //</span> Todas as Noticias</h2>
	</div>
	<br/>
        <div id="idxPageCnt1" class="areashow" style="width: 580px;margin-left: 23px;border: 0px;">
		    <?php
			  	$rank = new Ranking();
			  	
				$pagina=$_GET['pagina']; 
				if ($pagina == null) {
					$pc = "1"; 
					$pagina = "1";
				} else { 
					$pc = $pagina; 
				}
			
			    $inicio = $pc - 1; 
			    $inicio = $inicio * 15;
                
                for($b = 0; $b < 15; $b++){
					echo "<ul class='cntBbsRow'>";
					echo "<li>";
					echo "<div class='info'>";
					echo "<div class='cell1'><div class='ico".$rank->allnews($inicio)[$b]['tipo']."'>&nbsp;</div></div>";
					echo "<a href='?pg=ler_noticia&id=".$rank->allnews($inicio)[$b]['id']."' target='_self' style='text-decoration:none;color:white;'>".$rank->allnews($inicio)[$b]['titulo']."</a>";
					echo "<div class='cell2'><a href='?pg=ler_noticia&id=".$rank->allnews($inicio)[$b]['id']."' target='_self' style='text-decoration:none;color:white;'>".date("d-m-Y", strtotime($rank->allnews($inicio)[$b]['data']))."</a></div>";
					echo "</li>";
                                     
                    $atual = count($rank->allnews($inicio));
                    if($b > $atual - 2){ break; }
                    echo "</ul>";
//                
                } 
            ?>
		</div>
		<p style="font-size: 13px;text-align: center;">
				<?php		

				$pags = ceil($rank->Totalnews()); 
				$max_links = 5;
				
				
				for($a = $pagina-$max_links; $a <= $pagina-1; $a++) {
					if($a <=0) { } 
					else { 
					echo "<a href='?pg=allnews&pagina=$a' target='_self' style='text-decoration:none;color:white;'>$a </a>"; 
					} 
				} 
				echo "<span>$pagina</span>"; 
				
				for($a = $pagina+1; $a <= $pagina+$max_links; $a++) {
					if($a > $pags) {} 
					else { 
					echo "<a href='?pg=allnews&pagina=$a' target='_self'>$a </a>"; 
					} 
				} 
				echo "...";
				echo "  (<a href='?pg=allnews&pagina=".$pags."' target='_self' style='text-decoration:none;color:white;'>".$pags."</a>)";
				?>
		</p>
</div>