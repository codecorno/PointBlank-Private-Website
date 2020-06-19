<?php
$host        = "host=localhost";
   $port        = "port=5432";
   $dbname      = "dbname=postgres";
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
}else{
echo "
<form class='form-horizontal'>
	<div class='form-group'>
		<div class='col-sm-10' style='width: 298px;'>
			<div class='input-group' style='width: 260px;'>
				<span class='input-group-addon'><i class='linecons-user'></i></span>
				<input type='text' class='form-control' value='".$ranking['login']."' disabled>
			</div>
		</div>
		<img src='../Ranking/PAT/".$ranking['rank'].".gif' width='30' style='margin-top: 1px;'/>
	</div>

	<div class='form-group-separator'></div>
	<div class='form-group'>
		<div class='col-sm-10'>
			<div class='input-group' style='width: 326px;'>
				<span class='input-group-addon'><i class='linecons-mail'></i></span>
				<input type='text' class='form-control' value='".$ranking['email']."' style='width: 270px;' disabled>
			</div>
		</div>
	</div>

	<div class='form-group-separator'></div>
	<div class='form-group'>
		<div class='col-sm-10'>
			<div class='input-group' style='width: 326px;'>
				<span class='input-group-addon'><i class='linecons-money'></i></span>
				<input type='text' class='form-control' value='".$ranking['gp']."' style='width: 270px;' disabled>
			</div>
		</div>
	</div>

	<div class='form-group-separator'></div>
	<div class='form-group'>
		<div class='col-sm-10'>
			<div class='input-group' style='width: 326px;'>
				<span class='input-group-addon'><i class='fa fa-usd'></i></span>
				<input type='text' class='form-control' value='".$ranking['money']."' style='width: 270px;' disabled>
			</div>
		</div>
	</div>	

	
</form>
"; 
}
?>