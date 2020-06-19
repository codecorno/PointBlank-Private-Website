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
				<h2>Responder Ticket</h2>
				<div class="sep"></div>
				<?php
                try{
					if (!@$_GET['id']){
						$query = pg_query("SELECT * FROM suporte WHERE status='0' ORDER BY id DESC") or die(mysql_error());
						echo "<table width=\"375\" border=\"0\">";
						echo "<tr>";
						echo "<p><td width=\"350\" style='text-align: center;'>Titulo</td></p>";
						echo "<p><td width=\"25\"></td></p>";
						echo "</tr>";
						while($array = pg_fetch_object($query)) {
							echo "<tr>";
							echo sprintf("<td style='text-align: center;'>%s</td>", $array->titulo);
							echo sprintf("<td><a href='?pg=responder&id=".$array->id."'/>[<span style='color:#F00;'>Responder</span>]</a></td>");
							} 
						echo "</table>";
					}else{
						$id = @$_GET['id'];
						$query = pg_query("SELECT * FROM suporte WHERE id = '$id';") or die(mysql_error());
						while($row = pg_fetch_assoc($query)){ ?>
							<H1 style="font-size: 16px; text-align: left;margin-left: 16px;color:red;"><?php echo $row[titulo]; ?></h1>
							<span style="margin-left: 16px; color:#000;">Autor: <?php echo $row[nickname]; ?></span><Br/>
							<div class="sep"></div>
							<div class="com_wrap"> 
							<span style="left: 0px; position: relative;"><?php echo $row[mensagem];?></div><br/>
							<div class="sep"></div><br/>
							<H1 style="font-size: 16px; text-align: left;margin-left: 16px;">Responder</h1><br/>
							<form method="POST">
							<textarea style="width: 500px; height: 200px;" name="noticia"><?php echo $mensagem;?></textarea><br /><br />
							<input type="submit" name="go" value="Atualizar" /><br />
							<br/>
							</form>
					<?PHP
						if($_POST['go']){
							try{  
								$strSQL1 = "UPDATE suporte SET status='1', resposta='".$_POST["noticia"]."', gm='".$_SESSION["username"]."' WHERE id='$id'"; 
								$objQuery1 = pg_query($strSQL1);
								if(pg_affected_rows($objQuery1)>0){
									echo "<script>alert('Noticia Publicada com sucesso!');</script><script>window.location='?pg=paineladm';</script>";
								}					
								}catch(PDOException $e){
									echo "Erro: ".$e->getMessage();
								}
						}
						}
					}
					}catch(PDOException $e){
						echo "Erro: ".$e->getMessage();
					}				
				?>
			</div>
		</div>