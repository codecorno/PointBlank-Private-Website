<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrade system login');
?>

<?php if (!empty($error)) { ?>
	<div class="blockMessage blockMessage--error"><?php echo htmlspecialchars($error); ?></div>
<?php } ?>

<form method="post" action="index.php?upgrade/login" class="block">
	<div class="block-container">
		<div class="block-body">
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_login">Name or email</label></dt>
				<dd>
					<input type="text" name="login" value="" class="input" id="ctrl_login" />
				</dd>
			</dl>
			<dl class="formRow formRow--input">
				<dt><label class="formRow-label" for="ctrl_password">Password</label></dt>
				<dd>
					<input type="password" name="password" value="" class="input" id="ctrl_password" />
				</dd>
			</dl>
		</div>
		<dl class="formRow formSubmitRow">
			<dt></dt>
			<dd>
				<div class="formSubmitRow-main">
					<div class="formSubmitRow-bar"></div>
					<div class="formSubmitRow-controls">
						<button accesskey="s" class="button button--primary button--icon button--icon--login">
							<span class="button-text">Log in</span>
						</button>
					</div>
				</div>
			</dd>
		</dl>
	</div>

	<?php echo $templater->fnCsrfInput($templater, $null); ?>
</form>
