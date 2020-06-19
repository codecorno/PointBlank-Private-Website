	<META Charset="UTF-8" />
	<div id="left_wrapper">
		<div id="da-slider" class="da-slider">
			<div class="da-slide">
				<h3><a href="?pg=ler_noticia&id=1" class="da-link">Servidor em BETA</a></h3>
				<p>Estamos em fase BETA<br />
				</p>
				<div class="da-img"><img alt="alt_example" src="https://cdnx2.kincir.com/production/2019-08/original_1366/9d4703741a6309e5c31c770bdfe54aab1a39638c.jpg" style="width: 624px;margin-left: -402px;height: 252px;" /></div>
			</div>
			<div class="da-slide">
				<h3><a href="?pg=ler_noticia&id=1" class="da-link">Entre já no nosso Discord!</a></h3>
				<p>Entre agora em nosso servidor no Discord!<br />
					</p>
				<div class="da-img"><img alt="alt_example" src="https://i.imgur.com/ovoQrDa.png" style="width: 624px;margin-left: -402px;height: 252px;" /></div>
			</div>
			<div class="da-arrows"> <span class="da-arrows-prev"></span> <span class="da-arrows-next"></span> </div>
		</div>

		<div class="header">
			<h2><span>AzurePB //</span> Últimas Notícias</h2>
		</div>
		<div class="content33">
			<br />
			<ul class="tabType1">
				<li><a id="idxBtn1" type="buttom" onclick="showhideObj('idxPageCnt',1,6,'idxBtn','read_more3','read_more4');return false;" class="read_more4" target="_self"><span>Todas</span></a></li>
				<li><a id="idxBtn2" type="buttom" onclick="showhideObj('idxPageCnt',2,6,'idxBtn','read_more3','read_more4');return false;" class="read_more4" target="_self"><span>Notícias</span></a></li>
				<li><a id="idxBtn3" type="buttom" onclick="showhideObj('idxPageCnt',3,6,'idxBtn','read_more3','read_more4');return false;" class="read_more4" target="_self"><span>Eventos</span></a></li>
				<li><a id="idxBtn6" type="buttom" onclick="showhideObj('idxPageCnt',6,6,'idxBtn','read_more3','read_more4');return false;" class="read_more4" target="_self"><span>Avisos</span></a></li>
				<li><a id="idxBtn4" type="buttom" onclick="showhideObj('idxPageCnt',4,6,'idxBtn','read_more3','read_more4');return false;" class="read_more4" target="_self"><span>Atualizações</span></a></li>
				<li><a id="idxBtn5" type="buttom" onclick="showhideObj('idxPageCnt',5,6,'idxBtn','read_more3','read_more4');return false;" class="read_more3" target="_self"><span>Punidos</span></a></li>
                <li><a href="?pg=allnews" style="margin-left: auto; margin:0px 0px 0px 10px; text-decoration: none;color: white;font-size: 12px;"><span>Ver Todas...</span></a></li>

			</ul>
			<div id="idxPageCnt1" class="areashow">
				<?php
				$query1 = pg_query("SELECT * FROM noticias ORDER BY id DESC LIMIT 6") or die();
				while ($array = pg_fetch_object($query1)) {
					echo "<ul class='cntBbsRow'>";
					echo "<li>";
					echo "<div class='cell1'><div class='ico" . $array->tipo . "'>&nbsp;</div></div>";
					echo "<a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . $array->titulo . "</a>";
					echo "<div class='cell2'><a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . date("d-m-Y", strtotime($array->data)) . "</a></div>";
					echo "</li>";
					echo "</ul>";
				}
				?>
			</div>

			<div id="idxPageCnt2" class="areahide">
				<?php
				$query1 = pg_query("SELECT * FROM noticias WHERE tipo='Noticias' ORDER BY id DESC LIMIT 6") or die();
				while ($array = pg_fetch_object($query1)) {
					echo "<ul class='cntBbsRow'>";
					echo "<li>";
					echo "<div class='cell1'><div class='ico" . $array->tipo . "'>&nbsp;</div></div>";
					echo "<a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . $array->titulo . "</a>";
					echo "<div class='cell2'><a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . date("d-m-Y", strtotime($array->data)) . "</a></div>";
					echo "</li>";
					echo "</ul>";
				}
				?>
			</div>

			<div id="idxPageCnt3" class="areahide">
				<?php
				$query1 = pg_query("SELECT * FROM noticias WHERE tipo='Eventos' ORDER BY id DESC LIMIT 6") or die();
				while ($array = pg_fetch_object($query1)) {
					echo "<ul class='cntBbsRow'>";
					echo "<li>";
					echo "<div class='cell1'><div class='ico" . $array->tipo . "'>&nbsp;</div></div>";
					echo "<a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . $array->titulo . "</a>";
					echo "<div class='cell2'><a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . date("d-m-Y", strtotime($array->data)) . "</a></div>";
					echo "</li>";
					echo "</ul>";
				}
				?>
			</div>

			<div id="idxPageCnt4" class="areahide">
				<?php
				$query1 = pg_query("SELECT * FROM noticias WHERE tipo='Atualizacao' ORDER BY id DESC LIMIT 6") or die();
				while ($array = pg_fetch_object($query1)) {
					echo "<ul class='cntBbsRow'>";
					echo "<li>";
					echo "<div class='cell1'><div class='ico" . $array->tipo . "'></div></div>";
					echo "<a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . $array->titulo . "</a>";
					echo "<div class='cell2'><a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . date("d-m-Y", strtotime($array->data)) . "</a></div>";
					echo "</li>";
					echo "</ul>";
				}
				?>
			</div>
			<div id="idxPageCnt5" class="areahide">
					<?php
					$query1 = pg_query("SELECT * FROM noticias WHERE tipo='Punicoes' ORDER BY id DESC LIMIT 6") or die();
					while ($array = pg_fetch_object($query1)) {
						echo "<ul class='cntBbsRow'>";
						echo "<li>";
						echo "<div class='cell1'><div class='ico" . $array->tipo . "'></div></div>";
						echo "<a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . $array->titulo . "</a>";
						echo "<div class='cell2'><a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . date("d-m-Y", strtotime($array->data)) . "</a></div>";
						echo "</li>";
						echo "</ul>";
					}
					?>
					
			</div>
			<div id="idxPageCnt6" class="areahide">
					<?php
					$query1 = pg_query("SELECT * FROM noticias WHERE tipo='Avisos' ORDER BY id DESC LIMIT 6") or die();
					while ($array = pg_fetch_object($query1)) {
						echo "<ul class='cntBbsRow'>";
						echo "<li>";
						echo "<div class='cell1'><div class='ico" . $array->tipo . "'></div></div>";
						echo "<a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . $array->titulo . "</a>";
						echo "<div class='cell2'><a href='?pg=ler_noticia&id=" . $array->id . "' target='_self' style='text-decoration:none;color:white;'>" . date("d-m-Y", strtotime($array->data)) . "</a></div>";
						echo "</li>";
						echo "</ul>";
					}
					?>
					
			</div>
			<div class="clear"></div>
			</div>
				</div>


