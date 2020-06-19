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
		<div class="sep"></div><br/>
		<center>
			<img src="img/carregando.gif" width="30" height="30">
		</center>
		<br/><br/>
	</div>
</div>

<?php
try{
    $id = @$_GET['id'];
    $query = pg_query("DELETE FROM noticias WHERE id = '$id';");
	if(pg_affected_rows($query)>0){
		echo "<script>alert('Deletada com sucesso');</script><script>window.history.back()</script>";
	}else{
		echo "<script>window.location='?pg=paineladm';</script>";
	}
}catch(PDOException $e){
	echo "Erro: ".$e->getMessage();
}
?>