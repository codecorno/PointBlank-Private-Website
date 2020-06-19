<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrading...');
?>

<form action="index.php?upgrade/run" method="post" class="blockMessage" data-xf-init="auto-submit">

	<div>Upgrading... <?php echo $versionName; ?>,  Step <?php echo htmlspecialchars($step); ?><?php if (!empty($stepMessage)) { echo " ($stepMessage)" ; } ?></div>

	<div class="u-noJsOnly">
		<button accesskey="s" class="button">Proceed...</button>
	</div>

	<input type="hidden" name="run_version" value="<?php echo htmlspecialchars($newRunVersion); ?>" />
	<input type="hidden" name="step" value="<?php echo htmlspecialchars($newStep); ?>" />
	<?php if (isset($position)) { ?>
		<input type="hidden" name="position" value="<?php echo htmlspecialchars($position); ?>" />
	<?php } ?>
	<?php if (!empty($stepData)) { ?><input type="hidden" name="step_data" value="<?php echo htmlspecialchars(json_encode($stepData)); ?>" /><?php } ?>
	<?php echo $templater->fnCsrfInput($templater, $null); ?>

</form>