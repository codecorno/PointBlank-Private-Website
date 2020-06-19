<?php
if (!isset($_SESSION['username'])) {
echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = '../index.php';</script>";
exit;
}

$sql = pg_query("SELECT * FROM accounts WHERE login='$_SESSION[username]';");
while( $row = pg_fetch_assoc($sql) )
	if ($row[access_level] == 6){
		echo "<script>alert('Você nao tem privilegios suficientes.');</script>";
		header('Location: index.php');
	}
?>

<div class="page-title">
	<div class="title-env">
		<h1 class="title">Todos as Contas</h1>
		<p class="description">Aqui você pode verificar quantas contas existem com o mesmo IP.<?php echo $row[access_level] ?></p>
	</div>
	
	<div class="breadcrumb-env">
		<ol class="breadcrumb bc-1">
			<li>
				<a href="index.php"><i class="fa-home"></i>Home</a>
			</li>
			<li class="active">
				<strong>Tocas as Contas</strong>
			</li>
		</ol>
	</div>
</div>

<div class="page-body">	
	<div class="row">
		<div class="col-sm-12">
		<p>
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
			    $inicio = $inicio * 20;
			?>
			<center>
			<table border=1 style="border-collapse:collapse;width:75%;">
				<tr>
					<td style="text-align: center;" width="100">ID Login</td>
					<td style="text-align: center;" width="100">Nickname</td>
					<td style="text-align: center;" width="100">Email</td>
					<td style="text-align: center;" width="100">Last IP</td>
					<td style="text-align: center;" width="100">IP Cadastro</td>
				</tr>
				<?php 
					for($b = 0; $b < 20; $b++){
						echo "<tr style='height: 25px;'>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->RankingGeralIP($inicio)[$b]['login']."</span></td>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->RankingGeralIP($inicio)[$b]['player_name']."</span></td>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->RankingGeralIP($inicio)[$b]['email']."</span></td>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->RankingGeralIP($inicio)[$b]['lastip']."</span></td>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->RankingGeralIP($inicio)[$b]['cad_ip']."</span></td>";
						$atual = count($rank->RankingGeralIP($inicio));
						if($b > $atual - 2){ break; }
						echo "</tr>";
					} 
				?>
			</table>
			</center>
		</p>
		</div>
	</div><br>
	<p style="font-size: 15px;margin-top: -18px;text-align: center;">
		<?php		
			$pags = ceil($rank->TotalPaginasIP()); 
			$max_links = 5;
				
				
			for($a = $pagina-$max_links; $a <= $pagina-1; $a++) {
				if($a <=0) { 
				}else { 
					echo "<a style='color:blue;' href='?pg=contas_ip&pagina=$a' target='_self'>$a </a>"; 
				} 
			}
			
			echo $pagina." "; 
				
			for($a = $pagina+1; $a <= $pagina+$max_links; $a++) {
				if($a > $pags) {} 
				else { 
					echo "<a style='color:blue;' href='?pg=contas_ip&pagina=$a' target='_self'>$a </a>"; 
				} 
			} 
			echo "...";
			echo "  (<a style='color:blue;' href='?pg=contas_ip&pagina=".$pags."' target='_self'>".$pags."</a>)";
		?>
	</p>
</div>