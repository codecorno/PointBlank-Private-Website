<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrading...');
?>

<form action="index.php?upgrade/run" method="post" class="blockMessage" data-xf-init="auto-submit">

	<div>
		Upgrading...
	</div>

	<div class="u-noJsOnly">
		<button accesskey="s" class="button">Proceed...</button>
	</div>

	<?php echo $templater->fnCsrfInput($templater, $null); ?>
</form>