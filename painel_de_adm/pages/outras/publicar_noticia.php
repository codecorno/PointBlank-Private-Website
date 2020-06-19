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
				<form method="POST">
                Titulo<br/>
                <input type="text" name="titulo" style="width: 250px;">
					<select name="tipo">
						<option value="Noticias">Noticias</option>
						<option value="Eventos">Eventos</option>
						<option value="Atualizaçao">Atualizaçao</option>
					</select>
				<br /><br />
                Noticia<br/>
                <textarea style="width: 500px; height: 200px;" name="noticia" ></textarea><br /><br />
                <input type="submit" name="go" value="Publicar" /><br />
                <br />
				</form>     
                <?php
                if($_POST['go']){
                    try{  
						$result = pg_query("SELECT * FROM noticias");
						$num_rows = pg_num_rows($result);
						$id2 = $num_rows+1;
						$strSQL1 = "INSERT INTO noticias (titulo, noticia, data, autor, id, tipo) VALUES ('".$_POST["titulo"]."','".$_POST["noticia"]."','".date('Y-m-d')."','".$_SESSION["username"]."', '$id2', '".$_POST["tipo"]."')"; 
						$objQuery1 = pg_query($strSQL1);
						if(pg_affected_rows($objQuery1)>0){
							echo "<script>alert('Noticia Publicada com sucesso!');</script><script>window.location='?pg=paineladm';</script>";
						}					
					}catch(PDOException $e){
						echo "Erro: ".$e->getMessage();
					}      
                }
				?>
			</div>
		</div>