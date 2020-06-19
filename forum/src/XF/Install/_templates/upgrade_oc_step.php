<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrading...');
?>

<form action="oc-upgrader.php" method="post" class="blockMessage" data-xf-init="auto-submit">

	<div>
		Upgrading...
		Verifying and copying files
		<?php if ($params['percent']) { echo ' - ' . $params['title'] . ' (' . round($params['percent'], 0) . '%)'; } ?>
		<?php echo ($ticks ? str_repeat(' . ', $ticks) : ''); ?>
	</div>

	<div class="u-noJsOnly">
		<button accesskey="s" class="button">Proceed...</button>
	</div>

	<input type="hidden" name="key" value="<?php echo htmlspecialchars($key); ?>" />
	<input type="hidden" name="step" value="<?php echo htmlspecialchars($step); ?>" />
	<input type="hidden" name="ticks" value="<?php echo htmlspecialchars($ticks); ?>" />
	<?php if (!empty($params)) { ?><input type="hidden" name="params" value="<?php echo htmlspecialchars(json_encode($params)); ?>" /><?php } ?>
	<?php if (!empty($state)) { ?><input type="hidden" name="state" value="<?php echo htmlspecialchars(json_encode($state)); ?>" /><?php } ?>
</form>