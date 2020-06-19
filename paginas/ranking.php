    <?php
	$inicio = $_SESSION['username'];
	$rank2 = pg_query("SELECT * FROM accounts WHERE login = '$inicio'");
    $ranking2 = pg_fetch_assoc($rank2);
		
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
           <h2><span>AzurePB //</span> Ranking Pessoal</h2>
        </div>
		<?php if (!isset($_SESSION['username'])) { ?>
			<div style="padding: 15px 150px;border: 1px solid;"><span style="color: Red;">Você precisa está logado para ver o ranking.</span></div>
		<?php }elseif($ranking2['rank'] <= 4){
			echo "<div style='padding: 15px 150px;border: 1px solid;'><span style='margin-left: 100px;color: white;'>Seu rank é inferior a 5.</span></div>";
		}else{
		$inicio = $_SESSION['username'];
		$rank = pg_query("SELECT * FROM accounts WHERE login = '$inicio'");
        $ranking = pg_fetch_assoc($rank);
		
		$exp = $ranking['exp'];
		$posiçao = pg_query("SELECT * FROM accounts WHERE exp>'$exp' AND rank<'53' AND player_name<>'' AND rank>'5'");
		$posiçao = pg_num_rows($posiçao);
		$pos = $posiçao+1;
		?>
		<center>
        <table border=2 style="border-radius:5px;border: white;border-collapse: collapse;width:95%;">
            <tr style="height: 20px;">
                <td style="text-align: center;color: white;min-width: 25px;">Pos</td>
                <td style="text-align: center;color: white;min-width: 120px;">Nick</td>
                <td style="text-align: center;color: white;min-width: 35px;">Rank</td>
                <td style="text-align: center;color: white;min-width: 100px;">EXP</td>
                <td style="text-align: center;color: white;min-width: 75px;">Kills</td>
                <td style="text-align: center;color: white;min-width: 75px;">Deaths</td>
                <td style="text-align: center;color: white;min-width: 35px;">K/D%</td>
                <td style="text-align: center;color: white;min-width: 40px;">HS%</td>
                <td style="text-align: center;color: white;min-width: 30px;">Dest.</td>
            </tr>
                <?php 
                   echo "<tr style='height: 30px;'>";
                    $kill = $ranking['kills_count'];
					$kill2 = $ranking['totalkills_count'];
                    $dead = $ranking['deaths_count'];
                    @$kd = round($kill / ($kill+$dead) * 100);
                    echo "<td style='text-align: center;color: white;'><span style='width:100px;'>".(pos1($pos))."</span></td>";
                    echo "<td style='text-align: center;color: white;'>".$ranking['player_name']."</td>";
                    echo "<td style='text-align: center;color: white;'><img src='Ranking/PAT/".$ranking['rank'].".gif' width='20' /></td>";
                    echo "<td style='text-align: center;color: white;'>".str_replace(",", ".", number_format($ranking['exp']))."</td>";
                    echo "<td style='text-align: center;color: white;'>".$kill."</td>";
                    echo "<td style='text-align: center;color: white;'>".$dead."</td>";
                    echo "<td style='text-align: center;color: white;'>".$kd."%</td>";
                    @$hs = ($ranking['headshots_count'] * 100) / $kill2;
                    
                    echo "<td style='text-align: center;color: white;'>".round($hs)."%</td>";
                    echo "<td style='text-align: center;color: white;'>".$ranking['escapes']."</td>";
                    echo "</tr>";
            ?>
        </table>
		</center> 
		<?php } ?>
        <div class="header">
           <h2><span>AzurePB //</span> Ranking Total</h2>
        </div>
        <?php
          

                function RankingGeral($inicio){
                    try{
                        $sql = "SELECT * FROM accounts WHERE rank < '53' AND player_name<>'' AND rank>'5' ORDER BY exp DESC LIMIT 20 OFFSET '$inicio'";
                        $rank = pg_query($sql);
                        $ranking = pg_fetch_all($rank);
                        return $ranking;
                    }catch(PDOException $e){
                        echo "Erro: ".$e->getMessage();
                    }
                };
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
		<center>
        <table border=2 style="border-radius:5px;border: white;border-collapse: collapse;width:95%;">
            <tr style="height: 20px;">
                <td style="text-align: center;color: white;min-width: 25px;">Pos</td>
                <td style="text-align: center;color: white;min-width: 120px;">Nick</td>
                <td style="text-align: center;color: white;min-width: 35px;">Rank</td>
                <td style="text-align: center;color: white;min-width: 100px;">EXP</td>
                <td style="text-align: center;color: white;min-width: 75px;">Kills</td>
                <td style="text-align: center;color: white;min-width: 75px;">Deaths</td>
                <td style="text-align: center;color: white;min-width: 35px;">K/D%</td>
                <td style="text-align: center;color: white;min-width: 40px;">HS%</td>
                <td style="text-align: center;color: white;min-width: 30px;">Dest.</td>
            </tr>
                <?php 				
                for($b = 0; $b < 20; $b++){
                   echo "<tr style='height: 30px;'>";
                    $kill = RankingGeral($inicio)[$b]['kills_count'];
					$kill2 = RankingGeral($inicio)[$b]['totalkills_count'];
                    $dead = RankingGeral($inicio)[$b]['deaths_count'];
                    @$kd = round($kill / ($kill+$dead) * 100);
                    $posicao = $b+1+$inicio;
                    echo "<td style='text-align: center;color: white;'><span style='width:100px;'>".(pos1($posicao))."</span></td>";
                    echo "<td style='text-align: center;color: white;'>".RankingGeral($inicio)[$b]['player_name']."</td>";
                    echo "<td style='text-align: center;color: white;'><img src='Ranking/PAT/".RankingGeral($inicio)[$b]['rank'].".gif' width='20' /></td>";
                    echo "<td style='text-align: center;color: white;'>".str_replace(",", ".", number_format(RankingGeral($inicio)[$b]['exp']))."</td>";
                    echo "<td style='text-align: center;color: white;'>".$kill."</td>";
                    echo "<td style='text-align: center;color: white;'>".$dead."</td>";
                    echo "<td style='text-align: center;color: white;'>".$kd."%</td>";
                    @$hs = (RankingGeral($inicio)[$b]['headshots_count'] * 100) / $kill2;
                    
                    echo "<td style='text-align: center;color: white;'>".round($hs)."%</td>";
                    echo "<td style='text-align: center;color: white;'>".RankingGeral($inicio)[$b]['escapes']."</td>";
                    $atual = count(RankingGeral($inicio));
                    if($b > $atual - 2){ break; }
                     echo "</tr>";
                } 
            ?>
        </table>
		</center>
		<br/><br/>
        <p style="font-size: 14px;margin-top: -18px;text-align: center;"><?php
       
						

				$pags = ceil($rank->TotalPaginas()); 
				$max_links = 5;
				
				
				for($a = $pagina-$max_links; $a <= $pagina-1; $a++) {
					if($a <=0) { } 
					else { 
					echo "<a style='text-decoration: none;color:#49FFF0;' href='?pg=ranking&pagina=$a'>$a </a>| "; 
					} 
				} 
				 
				if($pagina == 1){ 
					echo "<span style='color:#f90000;'>1</span> | ";
				}else{
					echo "<span style='color:#f90000;'>".$pagina."</span> | ";
				}
				
				for($a = $pagina+1; $a <= $pagina+$max_links; $a++) {
					if($a > $pags) {} 
					else { 
					echo "<a style='color:#49FFF0;text-decoration: none;' href='?pg=ranking&pagina=$a'>$a </a>| "; 
					} 
				} 
				echo "...";
				echo "  (<a style='color:#49FFF0;text-decoration: none;' href='?pg=ranking&pagina=".$pags."'>".$pags."</a>)";
				?>
		</p>
	</center>                
	<div class="clear"></div></div>