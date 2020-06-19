<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	if ($errors)
	{
		$templater->setTitle('XenForo ' . \XF::$version . ' - Errors');
	}
	else
	{
		$templater->setTitle('XenForo ' . \XF::$version . ' - Welcome');
	}

?>

<?php if ($errors) { ?>
	<div class="blockMessage blockMessage--error">
		The following errors occurred while verifying that your server can run XenForo:
		<ul>
			<?php foreach ($errors AS $error) { ?>
				<li><?php echo $error; ?></li>
			<?php } ?>
		</ul>
		Please correct these errors and try again.
	</div>
<?php } else { ?>
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				<div class="block-row">
					<?php if ($warnings) { ?>
						<div class="block-rowMessage block-rowMessage--warning">
							The following warnings were detected when verifying that your server can run XenForo:
							<ul>
								<?php foreach ($warnings AS $warning) { ?>
									<li><?php echo $warning; ?></li>
								<?php } ?>
							</ul>
							These will not prevent you from using XenForo, but they should be resolved if possible to ensure optimal functioning.
							However, you may still continue with the installation.
						</div>
					<?php } else { ?>
						Your server meets all of XenForo requirements and you're now ready to begin installation.
					<?php } ?>
				</div>
			</div>
			<div class="block-footer">
				<a href="index.php?install/step/1" class="button button--primary">Begin installation</a>
			</div>
		</div>
	</div>
<?php } ?>