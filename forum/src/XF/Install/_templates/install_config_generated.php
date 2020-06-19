<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Configuration builder');
?>

<div class="block">
	<div class="block-container">
		<div class="block-body">
			<?php if ($written) { ?>
				<div class="block-row">
					The configuration information you entered is valid and has been written out to <?php echo htmlspecialchars($configFile); ?> automatically.
				</div>
			<?php } else { ?>
				<div class="block-row">
					The configuration information you entered is valid.
				</div>
				<div class="block-row">
					<form action="index.php?install/config/download" method="post">
						<input type="hidden" name="config" value="<?php echo htmlspecialchars(json_encode($config)); ?>" />
						<button accesskey="s" class="button button--icon button--icon--download">
							<span class="button-text">Download config</span>
						</button>
						<?php echo $templater->fnCsrfInput($templater, $null); ?>
					</form>
				</div>
				<div class="block-row">
					Please download the configuration using the button above and upload it to <?php echo htmlspecialchars($configFile); ?>.
					Once this is completed, use the button below to continue.
				</div>
			<?php } ?>
		</div>
		<div class="block-footer">
			<a href="index.php?install/step/1b" class="button button--primary">Continue</a>
		</div>
	</div>
</div>