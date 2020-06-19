<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrade complete');
?>

<div class="block">
	<div class="block-container">
		<div class="block-body">
			<div class="block-row">
				<div class="block-rowMessage block-rowMessage--success">
					Your upgrade to <?php echo \XF::$version; ?> has completed successfully!
				</div>
			</div>
			<?php if ($params['outdatedTemplates']) { ?>
				<div class="block-row">
					<div class="block-rowMessage">
						Note: outdated templates have been detected. This is normal after upgrading.
						This can be resolved by visiting the <a href="../admin.php?templates/outdated" style="font-weight: bold">outdated templates</a> section of the control panel.
						Incorporating template changes is important to ensure new features work properly and bug fixes take effect.
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="block-footer">
			<a href="../admin.php" class="button">Enter your control panel</a>
		</div>
	</div>
</div>
