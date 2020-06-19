<?php
$host        = "host=localhost";
$port        = "port=5432";
$dbname      = "dbname=localhost";
$credentials = "user=postgres password=09012009";

$conn = pg_connect( "$host $port $dbname $credentials" );
$inicio = $_POST['pin'];
$rank = pg_query("SELECT * FROM accounts WHERE player_name = '$inicio'");
$ranking = pg_fetch_assoc($rank);

if(pg_num_rows($rank)==0){
	echo "
		<form class='form-horizontal'>
			<div style='background-color:#FF3B3B; padding:20px'><span style='color:#000;'>Usuário não encontrado</span></div>	
		</form>";
}else{ ?>
	<div><br/>
		<script src="./assets/js/rec.js"></script>
		<form id="rec" name="rec" onsubmit="return false;" class="form-horizontal">
			<div class="input-group" style="width: 330px;margin-bottom:5px;">
				<span class="input-group-addon" style="width:56px;">Login</span>
				<input id="gold" name="gold" type="text" class="form-control" value="<?php echo $ranking['login']; ?>" style="width: 180px;">
				<img src="../Ranking/PAT/<?php echo $ranking['rank']; ?>.gif" width="30" style="margin-left: 10px;margin-top: 1px;"/>
			</div>
			
			<div class="input-group" style="width: 330px;margin-bottom:5px;">
				<span class="input-group-addon" style="width:56px;">Nick</span>
				<input id="gold" name="gold" type="text" class="form-control" value="<?php echo $ranking['player_name']; ?>" style="width: 220px;">
			</div>
			
			<div class="input-group" style="width: 330px;margin-bottom:5px;">		
				<span class="input-group-addon" style="width:56px;">Gold</span>
				<input id="gold" name="gold" type="text" class="form-control" value="<?php echo $ranking['gp']; ?>" style="width: 220px;">
			</div>
			
			<div class="input-group" style="width: 330px;margin-bottom:5px;">		
				<span class="input-group-addon" style="width:56px;">Cash</span>
				<input id="gold" name="gold" type="text" class="form-control" value="<?php echo $ranking['money']; ?>" style="width: 220px;">
			</div>
			
			<div class="input-group" style="width: 330px;margin-bottom:5px;">		
				<span class="input-group-addon" style="width:56px;">Email</span>
				<input id="gold" name="gold" type="text" class="form-control" value="<?php echo $ranking['email']; ?>" style="width: 220px;">
			</div>
			<button class="read_more2" style="padding: 3px;margin-left: 0px;width: 63px;border-radius: 2px;margin-top: 8px;">Ativar</button>
		</form>
	</div>
<?php
}
?>