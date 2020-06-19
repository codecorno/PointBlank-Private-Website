<meta http-equiv="refresh" content="5">
<?php
	error_reporting(0);
	require_once('../include.php');
	include "../class/Ranking.class.php";
	$rank = new Ranking();
					
	$inicio = 0; 

	$result77 = pg_query("SELECT * FROM vip");
	$num_rows77 = pg_num_rows($result77);
?>
<center>
	<?php echo "Numero de VIPs: ".$num_rows77.""; ?>
	<table border=1 style="border-collapse: collapse">
		<tr>
			<td style="text-align: center;" width="50" height="30">ID</td>
			<td style="text-align: center;" width="150" height="30">Nick</td>
			<td style="text-align: center;" width="200" height="30">Tempo restante</td>
		</tr>
            
        <?php 
            function VerificIsPassTwoHours($user){
				$resulthour = pg_query("SELECT * FROM vip WHERE usuario = '".$user."'");
				while ($rowhour = pg_fetch_assoc($resulthour)){
					$horaFinal = $rowhour[tempo];
					date_default_timezone_set('America/Sao_Paulo');		
					$hoje = date("Y-m-d");
					
					if (strtotime($horaFinal) <= strtotime($hoje)){
						$query32 = pg_query("DELETE FROM vip WHERE usuario = '".$user."'");
						//$query1 = pg_query("UPDATE accounts SET pc_cafe=0 WHERE login='".$user."'");
					}else{
						$diferenca = strtotime($horaFinal) - strtotime($hoje);
						$dias = intval($diferenca / 86400);
						return "".$dias." Dia(s)";
					}
				}
			}
				
            for($b = 0; $b < 9999; $b++){
				echo "<tr style='height: 15px;'>";
				echo "<td style='text-align: center;padding: 4px;'>".$rank->vip($inicio)[$b]['id']."</td>";
                echo "<td style='text-align: center;padding: 4px;color: red;'>".$rank->vip($inicio)[$b]['usuario']."</td>";
                echo "<td style='text-align: center;padding: 4px 15px;color: green;'>".VerificIsPassTwoHours($rank->vip($inicio)[$b]['usuario'])."</td>";
                if($b > $num_rows77 - 2){ break; }
				echo "</tr>";
			} 
        ?>
    </table>
</center>