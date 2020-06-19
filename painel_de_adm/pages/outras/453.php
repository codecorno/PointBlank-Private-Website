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
		<h2>Adicionar Pin-code</h2>
		<div class="sep"></div>
		<?php
               try{
					$result = pg_query("SELECT * FROM pin");
					$num_rows = pg_num_rows($result);
					$idit = $num_rows + 1;
					?>
					<script language="javascript">
					function altera() {
						<?php $serial = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',9)),0,9);?>
						document.getElementById("pin").value="PBTROLL<?php echo $serial?>";
					}
					</script>
					<center>
					<form method="POST">
						<h3>Pin</h3>
						<input type="text" name="pin" id="pin" value="" maxlength="16" style="margin-left: 39px;width: 171px;">
						<input style="margin-left:3px;" type="button" onclick="altera()" value="Gerar"><br />
						<h3>Valor</h3>
						<select name="valor">
							<option value="5000">5000</option>
							<option value="10000">10000</option>
							<option value="15000">15000</option>
							<option value="20000">20000</option>
							<option value="40000">40000</option>
							<option value="60000">60000</option>
						</select><br /><br/>
						<input type="submit" name="go" value="Inserir"/><br />
						<br/>
					</form>
					</center>
					<?php
						if($_POST['go']){
							try{  
								$strSQL1 = "INSERT INTO pin (id,pin,valor) VALUES ('".$idit."','".$_POST["pin"]."','".$_POST["valor"]."')"; 
								$objQuery1 = pg_query($strSQL1);
								if(pg_affected_rows($objQuery1)>0){
									echo "<script>alert('Pin Code adicionado com sucesso!!');</script><script>window.location='?pg=allpins';</script>";
								}					
								}catch(PDOException $e){
									echo "Erro: ".$e->getMessage();
								}
						}
					}catch(PDOException $e){
						echo "Erro: ".$e->getMessage();
					}	
				?>
	</div>
</div>