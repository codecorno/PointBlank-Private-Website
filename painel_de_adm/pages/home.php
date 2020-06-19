<?php
$result = pg_query("SELECT * FROM accounts WHERE rank<53");
$num_rows = pg_num_rows($result);

$result21 = pg_query("SELECT * FROM clan_data");
$num_rows21 = pg_num_rows($result21);

$result2 = pg_query("SELECT * FROM accounts WHERE rank = 53");
$num_rows3 = pg_num_rows($result2);

$result1 = pg_query("SELECT * FROM accounts WHERE online = 't'");
$num_rows1 = pg_num_rows($result1);

$pct_users = ($num_rows1 * 100) / $num_rows;
?>
	<div class="row">
		<div class="col-sm-3">
			<div class="xe-widget xe-progress-counter xe-progress-counter-green" data-count=".num" data-from="0" data-to="<?php echo $num_rows1; ?>" data-duration="2">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-user"></i>
					</div>
					<div class="xe-label">
						<span>Users online</span>
						<strong class="num"><?php echo $num_rows1; ?></strong>
					</div>
				</div>
						
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="0" data-fill-to="<?php echo $pct_users; ?>" data-fill-unit="%" data-fill-property="width" data-fill-duration="1" data-fill-easing="true"></span>
				</div>
				<br/>
			</div>
		</div>
				
		<div class="col-sm-3">
			<div class="xe-widget xe-progress-counter xe-progress-counter-blue" data-count=".num" data-from="0" data-to="<?php echo $num_rows; ?>" data-duration="1">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-user"></i>
					</div>
					<div class="xe-label">
						<span>Total Users</span>
						<strong class="num"><?php echo $num_rows; ?></strong>
					</div>
				</div>
						
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="100" data-fill-to="100" data-fill-unit="%" data-fill-property="width" data-fill-duration="1" data-fill-easing="true"></span>
				</div>
				<br/>
			</div>	
		</div>		
		
		<div class="col-sm-3">
			<div class="xe-widget xe-progress-counter xe-progress-counter-purple" data-count=".num" data-from="0" data-to="<?php echo $num_rows21; ?>" data-duration="1">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="el-group"></i>
					</div>
					<div class="xe-label">
						<span>Total Clans</span>
						<strong class="num"><?php echo $num_rows21; ?></strong>
					</div>
				</div>
						
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="100" data-fill-to="100" data-fill-unit="%" data-fill-property="width" data-fill-duration="1" data-fill-easing="true"></span>
				</div>
				<br/>
			</div>	
		</div>	
		
		<div class="col-sm-3">
			<div class="xe-widget xe-progress-counter xe-progress-counter-red" data-count=".num" data-from="0" data-to="<?php echo $num_rows3; ?>" data-duration="0">
				<div class="xe-upper">
					<div class="xe-icon">
						<i><img src="../Ranking/PAT/53.gif" width="25"/></i>
					</div>
					<div class="xe-label">
						<span>Total Gms</span>
						<strong class="num"><?php echo $num_rows3; ?></strong>
					</div>
				</div>
						
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="100" data-fill-to="100" data-fill-unit="%" data-fill-property="width" data-fill-duration="0" data-fill-easing="true"></span>
				</div>
				<br/>
			</div>	
		</div>
	</div>