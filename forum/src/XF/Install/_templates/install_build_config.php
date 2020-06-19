<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Configuration builder');
?>

<form method="post" action="index.php?install/build-config" class="block">
	<div class="block-container">
		<div class="block-body">
			<div class="formInfoRow">
				To install XenForo, you must know how to connect to your MySQL server. If your hosting comes with cPanel or Plesk, you can find this information there.<br />
				<br />
				If you are unsure what to enter here, please contact your host. These values are specific to you.
			</div>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_config_db_host">MySQL server</label></dt>
				<dd>
					<input type="text" name="config[db][host]" value="localhost" class="input" id="ctrl_config_db_host" />
					<div class="formRow-explain">Do not change this if you are unsure.</div>
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_config_db_port">MySQL port</label></dt>
				<dd>
					<input type="text" name="config[db][port]" value="3306" class="input" id="ctrl_config_db_port" />
					<div class="formRow-explain">Do not change this if you are unsure.</div>
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_config_db_username">MySQL user name</label></dt>
				<dd>
					<input type="text" name="config[db][username]" value="" class="input" id="ctrl_config_db_username" />
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_config_db_password">MySQL password</label></dt>
				<dd>
					<input type="text" name="config[db][password]" value="" autocomplete="off" class="input" id="ctrl_config_db_password" />
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_config_db_dbname">MySQL database name</label></dt>
				<dd>
					<input type="text" name="config[db][dbname]" value="" class="input" id="ctrl_config_db_dbname" />
					<div class="formRow-explain">This database must already exist.</div>
				</dd>
			</dl>
		</div>
		<dl class="formRow formSubmitRow">
			<dt></dt>
			<dd>
				<div class="formSubmitRow-main">
					<div class="formSubmitRow-bar"></div>
					<div class="formSubmitRow-controls">
						<button accesskey="s" class="button button--primary button--icon button--icon--save">
							<span class="button-text">Save config</span>
						</button>
					</div>
				</div>
			</dd>
		</dl>
	</div>

	<input type="hidden" name="config[fullUnicode]" value="1" />
	<?php echo $templater->fnCsrfInput($templater, $null); ?>
</form>
