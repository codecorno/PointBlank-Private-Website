<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Setup administrator');
?>

<form method="post" action="index.php?install/step3b" class="block">
	<div class="block-container">
		<div class="block-body">
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_username">User name</label></dt>
				<dd>
					<input type="text" name="username" value="" class="input" id="ctrl_username" />
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_password">Password</label></dt>
				<dd>
					<input type="password" name="password" value="" class="input" id="ctrl_password" />
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_password_confirm">Confirm password</label></dt>
				<dd>
					<input type="password" name="password_confirm" value="" class="input" id="ctrl_password_confirm" />
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_email">Email</label></dt>
				<dd>
					<input type="email" name="email" value="" class="input" id="ctrl_email" />
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
							<span class="button-text">Create administrator</span>
						</button>
					</div>
				</div>
			</dd>
		</dl>
	</div>

	<?php echo $templater->fnCsrfInput($templater, $null); ?>
</form>
