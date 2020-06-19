<?php
$sql = pg_query("SELECT * FROM accounts WHERE login='$_SESSION[username]';");
$row = pg_fetch_assoc($sql);
?>

<div class="row">
	<select name="type" id="type">
		<option value='1'>Editar minha conta</option>
		<option value='2'>Editar outra conta</option>
	</select>
	<br/>
	<br/>
	
	<div id="arma" style="display:block;">
		<input id="item2" type="text" class="form-control" value="<?php echo $row[money]; ?>" />
	</div>
	
	<div id="item" style="display:none;">
		<form id="checkform" action="useredit.php" method="post">
			<div class="form-group" style="width: 265px;">
				<div class="col-sm-10" style="margin-left: -16px;">
					<div class="input-group" style="width: 150px;">
						<span class="input-group-addon"><i class="linecons-search"></i></span>
						<input name="pin" type="text" class="form-control" placeholder="Nickname" style="width: 150px;">
					</div>
				</div>
			</div>
			<input type="submit" class="btn btn-danger btn-sm" value="Procurar" style="margin-top: 1px;">
		</form>
	</div>
	
	<div id="htmlExampleTarget">
	</div>
</div>
<script>
$("select").change(function () {
    var str = "";
    $("select option:selected").each(function() {
    var str = $(this).val();
	if (str == "1"){
        $('#item').fadeOut(0);
		$('#arma').fadeIn(200);
	}else{
        $('#arma').fadeOut(0);
		$('#item').fadeIn(200);
	}  
    });
  }).change();
</script>