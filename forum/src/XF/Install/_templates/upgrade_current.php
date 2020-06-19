<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('No upgrade available');
?>

<div class="block">
	<div class="block-container">
		<div class="block-body block-row">
			You are already running the current version (<?php echo \XF::$version; ?>).
			To do a fresh install, <a href="index.php?install/">click here</a>.
		</div>
		<div class="block-footer">
			<a href="index.php?upgrade/rebuild" class="button button--primary">Rebuild master data</a>
		</div>
	</div>
</div>