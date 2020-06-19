<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrade errors');
?>

<div class="blockMessage blockMessage--error">
	<p>Uh oh, your upgrade to <?php echo htmlspecialchars(\XF::$version); ?> has failed!
		The upgrade rebuild processes did not complete successfully.</p>

	<p>Use the button below to try again. If you receive this error again, please contact support.</p>

	<a href="index.php?upgrade/rebuild" class="button">Retry now</a>
</div>