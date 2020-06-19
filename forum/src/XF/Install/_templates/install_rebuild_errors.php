<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Installation errors');
?>

<div class="blockMessage blockMessage--error">
	<p>Uh oh, your installation of <?php echo htmlspecialchars(\XF::$version); ?> has failed!
		The build processes did not complete successfully.</p>

	<p>Use the button below to try again. If you receive this error again, please contact support.</p>

	<a href="index.php?install/step2b" class="button">Retry now</a>
</div>