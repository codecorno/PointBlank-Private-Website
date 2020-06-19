<?php
	function pos1($posiao){
		if ($posiao <= 3){
			return "<img src=\"img/user{$posiao}.png\" title=\"{$posiao}\"/>";
		}else{
			Return "".$posiao."º";
		}
	}
	?>

<div id="left_wrapper">
    <div class="header">
        <h2><span>SPACESHOOT //</span> Ranking Clan</h2>
    </div>
	<p>
		<?php
			$rank = new Ranking();
			  	
			$pagina=$_GET['pagina']; 
			if ($pagina == null) {
				$pc = "1"; 
			} else { 
				$pc = $pagina; 
			}
			
			$inicio = $pc - 1; 
			$inicio = $inicio * 20;
		?>
		<center>
        <table border=1 style="border-collapse: collapse; border-color:white; width:95%;">
            <tr>
                <td style="text-align: center;color:white;" width="10">Pos</td>
                <td style="text-align: center;color:white;" width="100">Nome do clan</td>
                <td style="text-align: center;color:white;" width="10">Rank</td>
                <td style="text-align: center;color:white;" width="130">EXP</td>
                <td style="text-align: center;color:white;" width="50">Partidas</td>
                <td style="text-align: center;color:white;" width="150">Vitorias/Derrotas</td>
                <td style="text-align: center;color:white;" width="50">KD%</td>
            </tr>
                <?php 
                for($b = 0; $b < 20; $b++){
                   echo "<tr style='height: 30px;'>";
                    $posicao = $b+1+$inicio;
                    $kdclan = ($rank->RankingGeralClan($inicio)[$b]['vitorias'] * 100) / $rank->RankingGeralClan($inicio)[$b]['partidas'];
                    echo "<td style='text-align: center;color:white;'><span style='width:100px;'>".(pos1($posicao))."</span></td>";
                    echo "<td style='text-align: center;color:white;'>".$rank->RankingGeralClan($inicio)[$b]['clan_name']."</td>";
                    echo "<td style='text-align: center;color:white;'><img src='Ranking/CLAN/".$rank->RankingGeralClan($inicio)[$b]['clan_rank'].".jpg' width='20' /></td>";
                    echo "<td style='text-align: center;color:white;'>".$rank->RankingGeralClan($inicio)[$b]['clan_exp']."</td>";
                    echo "<td style='text-align: center;color:white;'>".$rank->RankingGeralClan($inicio)[$b]['partidas']."</td>";
                    echo "<td style='text-align: center;color:white;'>".$rank->RankingGeralClan($inicio)[$b]['vitorias']." / ".$rank->RankingGeralClan($inicio)[$b]['derrotas']."</td>";
                    echo "<td style='text-align: center;color:white;'>".round($kdclan,2)." %</td>";

                    $atual = count($rank->RankingGeralClan($inicio));
                    if($b > $atual - 2){ break; }
						echo "</tr>";
                } 
            ?>
        </table>
		</center><br/>
	</p>
	<p style="font-size: 13px;margin-top: -18px;text-align: center;">
		<?php		
			$pags = ceil($rank->TotalPaginasClan()); 
			$max_links = 5;
				
				
			for($a = $pagina-$max_links; $a <= $pagina-1; $a++) {
				if($a <=0) { 
				}else { 
					echo "<a style='color:#49FFF0;' href='?pg=clan&pagina=$a' target='_self'>$a </a>"; 
				} 
			} 
			echo $pagina." "; 
				
			for($a = $pagina+1; $a <= $pagina+$max_links; $a++) {
				if($a > $pags) {
				}else { 
					echo "<a style='color:#49FFF0;' href='?pg=clan&pagina=$a' target='_self'>$a </a>"; 
				} 
			} 
			echo "...";
			echo "  (<a style='color:#49FFF0;' href='?pg=clan&pagina=".$pags."' target='_self'>".$pags."</a>)";
		?>
	</p>
</div>