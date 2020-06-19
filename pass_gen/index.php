<?php
if (isset($_POST['senha'])){
	$senha = $_POST['senha'];
	$salt = '/x!a@r-$r%an¨.&e&+f*f(f(a)';
	$output = hash_hmac('md5', $senha, $salt);
}
?>

<form action="#" method="POST">
<input type="text" placeholder="<?php echo @$_POST['senha'] ?>" name="senha">
<input type="submit" value="Enviar">
</form>
<p style="font-size:32px;"><?php echo @$output ?></p>