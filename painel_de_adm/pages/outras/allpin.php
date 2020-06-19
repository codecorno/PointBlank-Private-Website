<?php
if (!isset($_SESSION['username'])) {
echo "<script>alert('Por Favor, Faça o login primeiro!');</script><script>window.location = '../index.php';</script>";
exit;
}

$sql = pg_query("SELECT * FROM accounts WHERE login='$_SESSION[username]';");
while( $row = pg_fetch_assoc($sql) )
	if ($row[access_level] != 5){
		echo "<script>alert('Você nao tem privilegios sulficientes.');</script>";
		header('Location: index.php');
	}
?>
<div id="main">
	<div class="full_w">
	<div class="h_title">Painel de ADM</div>
	<h2>Todos os Pin-codes</h2>
	<div class="sep"></div>
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
			    $inicio = $inicio * 10;
			?>
			<center>
			<table border=0 style="border-collapse:collapse;width:75%;">
				<tr>
					<td style="text-align: center;" width="150">Pin code</td>
					<td style="text-align: center;" width="40">Valor</td>
				</tr>
				<?php 
					for($b = 0; $b < 10; $b++){
						echo "<tr style='height: 10px;'>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->Pins($inicio)[$b]['pin']."</span></td>";
						echo "<td style='text-align: center;'><span style='font-size: 12px;'>".$rank->Pins($inicio)[$b]['valor']."</span></td>";
						$atual = count($rank->Pins($inicio));
						if($b > $atual - 2){ break; }
						echo "</tr>";
					} 
				?>
			</table>
			</center>
		</p>
	</div>
	<p style="font-size: 13px;margin-top: -18px;text-align: center;">
		<?php		
			$pags = ceil($rank->Totalpins()); 
			$max_links = 5;
				
				
			for($a = $pagina-$max_links; $a <= $pagina-1; $a++) {
				if($a <=0) { 
				}else { 
					echo "<a href='?pg=allpins&pagina=$a' target='_self'>$a </a>"; 
				} 
			} 
			echo $pagina." "; 
				
			for($a = $pagina+1; $a <= $pagina+$max_links; $a++) {
				if($a > $pags) {} 
				else { 
					echo "<a href='?pg=allpins&pagina=$a' target='_self'>$a </a>"; 
				} 
			} 
			echo "...";
			echo "  (<a href='?pg=allpins&pagina=".$pags."' target='_self'>".$pags."</a>)";
		?>
	</p>
</div>