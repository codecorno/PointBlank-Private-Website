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
		<h2>Painel de Administrador</h2>
		<div class="sep"></div>
		<?php
                try{
                    $id = @$_GET['id'];
                    $query = pg_query("SELECT * FROM noticias WHERE id = '$id';");
						while($row = pg_fetch_assoc($query)){
							$titulo = $row[titulo];
							$tipo = $row[tipo];
							$mensagem = $row[noticia];?>
							<form method="POST">
							Titulo<br/>
							<input type="text" name="titulo" style="width: 250px;" value="<?php echo $titulo;?>">
							<select name="tipo">
								<option value="<?php echo $tipo;?>"><?php echo $tipo;?></option>
								<option value="Noticias">Noticias</option>
								<option value="Eventos">Eventos</option>
								<option value="Atualizaçao">Atualizaçao</option>
							</select>
							<br /><br />
							Noticia<br/>
							<textarea style="width: 500px; height: 200px;" name="noticia"><?php echo $mensagem;?></textarea><br /><br />
							<input type="submit" name="go" value="Atualizar" /><br />
							<br />
							</form>
							<?php
							if($_POST['go']){
							try{  
								$strSQL1 = "UPDATE noticias SET tipo='".$_POST["tipo"]."', titulo='".$_POST["titulo"]."', noticia='".$_POST["noticia"]."', data='".date('d-m-Y')."', autor='".$_SESSION["username"]."' WHERE id='$id'"; 
								$objQuery1 = pg_query($strSQL1);
								if(pg_affected_rows($objQuery1)>0){
									echo "<script>alert('Noticia Publicada com sucesso!');</script><script>window.location='?pg=paineladm';</script>";
								}					
								}catch(PDOException $e){
									echo "Erro: ".$e->getMessage();
								}      
							}
						}
					}catch(PDOException $e){
						echo "Erro: ".$e->getMessage();
					}	
				?>
	</div>
</div>