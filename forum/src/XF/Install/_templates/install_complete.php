<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Installation complete');
?>

<div class="block">
	<div class="block-container">
		<div class="block-body block-row">
			<div class="block-rowMessage block-rowMessage--success">
				XenForo <?php echo \XF::$version; ?> has been installed successfully!
			</div>
		</div>
		<div class="block-footer">
			<a href="../admin.php" class="button">Enter your control panel</a>
		</div>
	</div>
</div>
