<?php
$inicio = $_SESSION['username'];
$rank = pg_query("SELECT * FROM accounts WHERE login = '$inicio'");
$ranking = pg_fetch_assoc($rank);
			
$exp = $ranking['exp'];
$posiçao = pg_query("SELECT * FROM accounts WHERE exp > '$exp' AND rank<'52'");
$posiçao = pg_num_rows($posiçao);
$pos = $posiçao+1;

$rank = new Ranking();

$kill = $ranking['kills_count'];
$dead = $ranking['deaths_count'];

$ganhar = $ranking['fights_win'];
$perder = $ranking['fights_lost'];
$total = $ranking['fights'];
@$kdp = round(($ganhar * 100) / $total, 1);
?>
<link type="text/css" href="stylesheets/perfil.css" rel="stylesheet">

<div id="left_wrapper">
    <div class="header">
        <h2><span>PB Troll //</span> Ranking Pessoal</h2>
    </div>
	
	<div class="areashow1">
		<p class="sbj">Dados</p>
		<div class="box1">
			<dl class="dlA config">
				<dt> <img src="images/patentes/<?php echo nome($ranking['rank']);?>.png" alt="Redbulls"></dt>
				<dd>
					<ul class="ulA config">
						<li class="bdr1 config">
							<span class="txtBlock1 config"><?php echo $ranking['player_name']; ?></span>
						</li>
						<li>
							<span class="txtBlock1 config">Pos : <?php echo $pos; ?></span>
							<span class="txtBlock1 config">Clan : <?php $rank->clan($ranking['clan_id']); ?></span>
						</li>
					</ul>
				</dd>
			</dl>
		</div>

		<p class="sbj">Pessoal</p>
		<div class="box1">
			<ul class="ulB config">
				<li class="bdr1"><p class="txtRed1 config">Forças de Combate</p></li>
				<li>
					<ul class="ulC config">
						<li class="cell1 config">
							<span class="cell1">Ganhados/Perdidos</span>
							<span class="cell2"><?php echo $ganhar; ?>/<?php echo $perder; ?> (<?php echo $kdp; ?>%)</span>
						</li>
						<li class="config">
							<span class="cell1 config">HeadShot</span>
							<span class="cell2 config"><?php echo $ranking['headshots_count']; ?></span>
						</li>
					</ul>
				</li>
				<li>
					<ul class="ulC config">
						<li class="cell1">
							<span class="cell1">Kill/Death</span>
							<span class="cell2"><?php echo $kill; ?>/<?php echo $dead; ?></span>
						</li>
						<li>
							<span class="cell1">Experiencia</span>
							<span class="cell2"><?php echo str_replace(",", ".", number_format($ranking['exp'])); ?></span>
						</li>
					</ul>
				</li>
				<li>
					<p class="txtRed1 config">Medalha de Honra / Medalha</p>
				</li>
			</ul>
			<ul class="ulD config">
				<li class="sect1">
					<span>Medalha de Honra<strong><?php echo $ranking['blue_order']; ?></strong></span>
				</li>
				<li class="sect2">
					<span>Medalha<strong><?php echo $ranking['medal']; ?></strong></span>
				</li>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
</div>