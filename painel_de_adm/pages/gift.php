	<script src="./assets/js/gift.js"></script>	
	<div class="col-md-12">
		<ul class="nav nav-tabs nav-tabs-justified">
			<li class="active">
				<a href="#cash" data-toggle="tab">
					<span class="hidden-xs">Cash</span>
				</a>
			</li>
			<li class="">
				<a href="#gold" data-toggle="tab">
					<span class="hidden-xs">Gold</span>
				</a>
			</li>
			<li class="">
				<a href="#arma" data-toggle="tab">
					<span class="hidden-xs">Pacote de armas</span>
				</a>
			</li>
		</ul>
				
		<div class="tab-content">
			<div class="tab-pane active" id="cash">
				<div><center>
					<form id="cash" name="cash" onsubmit="return false;">
						<div class="input-group" style="width: 1px;">
							<span class="input-group-addon"><i class="linecons-user"></i></span>
							<input name="asdasd" type="text" class="form-control" placeholder="User ID" style="width: 150px;">
						</div><br/>
						<div class="input-group" style="width: 1px;">
							<span class="input-group-addon"><i class="linecons-money"></i></span>
							<input name="number" type="text" class="form-control" data-validate="number" placeholder="Cash" aria-invalid="false" aria-describedby="number-error" style="width: 150px;">
						</div>
						<br/>
						<input type="submit" class="btn btn-blue btn-sm" value="Enviar" style="margin-top: 1px;">
					</form>
				</center></div>
			</div>
			
			<div class="tab-pane" id="gold">
				<div><center>
					<form id="gold" name="gold" onsubmit="return false;">
						<div class="input-group" style="width: 1px;">
							<span class="input-group-addon"><i class="linecons-user"></i></span>
							<input name="asdasd" type="text" class="form-control" placeholder="User ID" style="width: 150px;">
						</div><br/>
						<div class="input-group" style="width: 1px;">
							<span class="input-group-addon"><i class="linecons-money"></i></span>
							<input name="number" type="text" class="form-control" data-validate="number" placeholder="Gold" aria-invalid="false" aria-describedby="number-error" style="width: 150px;">
						</div>
						<br/>
						<input type="submit" class="btn btn-blue btn-sm" value="Enviar" style="margin-top: 1px;">
					</form>
				</center></div>
			</div>
			
			<div class="tab-pane" id="arma">
				<div align="left" style="height: 0;">
					<form role="form" id="form1" method="post" class="validate" novalidate="novalidate" style="width: 250px;">
						<div class="input-group" style="margin-bottom:5px;">
							<span class="input-group-addon"><i class="linecons-user"></i></span>
							<input id="login" name="login" type="text" class="form-control" placeholder="" style="width: 206px;" data-validate="required" data-message-required="<?php echo "<span style='color:red;margin-left:108px;'>É necessario inserir o ID</span>"; ?>" aria-required="true" aria-describedby="name-error" />
						</div>
							<script type="text/javascript">
								jQuery(document).ready(function($){
									$("#pacote").selectBoxIt({
										showEffect: 'fadeIn',
										hideEffect: 'fadeOut'
									});
								});
							</script>
							<select name='pacote' id="pacote" class="form-control">
								<option>Selecione um pacote</option>
								<option value="tr">Pacote 1</option>
								<option value="uk">Pacote 2</option>
								<option value="us">Pacote 3</option>
							</select>	
						<div class="input-group spinner" data-step="1" data-min="1" data-max="30" style="width: 250px;margin-top:5px;">
							<span class="input-group-btn">
								<button class="btn btn-danger btn-single" data-type="decrement">- 1 Dia</button>
							</span>
							<input type="text" class="form-control text-center" readonly="1" value="7" id="dias" name="dias"/>
							<span class="input-group-btn">
								<button class="btn btn-success btn-single" data-type="increment">+ 1 Dia</button>
							</span>
						</div><br/>
						<center><input type='submit' name='ewfewf' class="btn btn-blue btn-sm" value="Enviar" style="width: 100px;"/></center>
					</form>
				</div>
				
				<div align="right">
					<table class="table table-model-2 table-hover" style="width:520px;">
						<thead>
							<tr>
								<th style="text-align: center;">Pacote</th>
								<th style="text-align: center;">Primaria 1</th>
								<th style="text-align: center;">Primaria 2</th>
								<th style="text-align: center;">Secundaria</th>
								<th style="text-align: center;">Faca</th>
							</tr>
						</thead>
							
						<tbody>
							<tr style="text-align: center;">
								<td>1</td>
								<td>Arlind</td>
								<td>Nushi</td>
								<td>outro</td>
								<td>outro</td>
							</tr>
								
							<tr style="text-align: center;">
								<td>2</td>
								<td>Art</td>
								<td>Ramadani</td>
								<td>outro</td>
								<td>outro</td>
							</tr>
								
							<tr style="text-align: center;">
								<td>3</td>
								<td>Filan</td>
								<td>Fisteku</td>
								<td>outro</td>
								<td>outro</td>
							</tr>
							
							<tr style="text-align: center;">
								<td>4</td>
								<td>Filan</td>
								<td>Fisteku</td>
								<td>outro</td>
								<td>outro</td>
							</tr>
							
							<tr style="text-align: center;">
								<td>5</td>
								<td>Filan</td>
								<td>Fisteku</td>
								<td>outro</td>
								<td>outro</td>
							</tr>
						</tbody>
					</table><br/>
				</div>
					<?php
					if($_POST['ewfewf']){
						$login = $_POST['dias'];
						try{ 
							echo "<script>alert('$login');</script>";
						}catch(PDOException $e){
							echo "Erro: ".$e->getMessage();
						}
					}	
					?>
			</div>
		</div>
	</div>