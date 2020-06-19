<?php 
    $id = @$_GET['id'];
    $query = pg_query("SELECT * FROM noticias WHERE id = '$id';");
	$num_rows = pg_num_rows($query);
	if ($num_rows <= 0){
		echo "<script>alert('Noticia nao existente.');</script><script>window.location = 'index.php';</script>";
	}
	while($row = pg_fetch_assoc($query)){
?>
<div id="left_wrapper">
    <div class="header">
        <h2 style="padding-bottom: 1px;"><span>SPACESHOOT //</span> <?php echo $row[titulo]; ?></h2>
		<span style="color:white;margin-left: 25px;">Data: <?php echo date("d-m-Y", strtotime($row[data]));?></span>
    </div>		
	<script type="text/javascript">
		function MostraTudo($texto){
			$Mostra=str_replace(chr(10),"<br>",$texto);
			return $Mostra;
		} 
	</script>
	<div id="post_wrapper">
		<div id="body"><br/>
			<span><?php echo $row[noticia]; ?></span><br/><br/><br/>
			<div class="sep"></div>
			<span>
				<p style="color:#FFF;font-size: 12px;margin-left: 5px;">Atenciosamente,<p style="font-size: 13px;color:#FFF;margin-left: 5px;">Equipe - AzurePB</p></p>
			</span>
		</div>
	</div>
</div> 
<?php }?>