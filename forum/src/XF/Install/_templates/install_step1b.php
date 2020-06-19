<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Verify configuration');
?>

<?php if ($warnings) { ?>
	<div class="blockMessage blockMessage--warning">
		The following warnings were detected when verifying that your server can run XenForo:
		<ul>
			<?php foreach ($warnings AS $warning) { ?>
				<li><?php echo $warning; ?></li>
			<?php } ?>
		</ul>
		These will not prevent you from using XenForo, but they should be resolved if possible to ensure optimal functioning.
		However, you may still continue with the installation.
	</div>
<?php } ?>

<?php if ($existingInstall) { ?>
	<form action="index.php?install/step/2" method="post" class="block">
		<div class="block-container">
			<div class="block-body">
				<div class="formInfoRow">
					<div>Your configuration has been verified. You are now ready to begin the installation.</div>

					<div class="block-rowMessage block-rowMessage--warning">
						XenForo is already installed in your database. Continuing will remove all XenForo-related data from your database!
					</div>
				</div>
				<dl class="formRow formRow--input">
					<dt></dt>
					<dd>
						<ul class="inputChoices">
							<li class="inputChoices-choice">
								<label class="iconic iconic--checkbox iconic--labelled"><input type="checkbox" name="remove" value="1"><i aria-hidden="true"></i>Remove all XenForo-related data, including posts and users, from <b><?php echo htmlspecialchars($config['db']['dbname']); ?></b></label>
							</li>
						</ul>
					</dd>
				</dl>
			</div>
			<dl class="formRow formSubmitRow">
				<dt></dt>
				<dd>
					<div class="formSubmitRow-main">
						<div class="formSubmitRow-bar"></div>
						<div class="formSubmitRow-controls">
							<button accesskey="s" class="button button--primary">
								<span class="button-text">Begin installation</span>
							</button>
						</div>
					</div>
				</dd>
			</dl>
		</div>

		<?php echo $templater->fnCsrfInput($templater, $null); ?>
	</form>
<?php } else { ?>
	<form action="index.php?install/step/2" method="post" class="block">
		<div class="block-container">
			<div class="block-body">
				<div class="block-row">
					Your configuration has been verified. You are now ready to begin the installation.
				</div>
			</div>
			<div class="block-footer">
				<button accesskey="s" class="button button--primary">Begin installation</button>
			</div>
		</div>
		<?php echo $templater->fnCsrfInput($templater, $null); ?>
	</form>
<?php }
