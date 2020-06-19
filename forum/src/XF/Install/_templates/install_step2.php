<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Install');
?>

<div class="blockMessage">
	<ul>
		<?php if ($removed) { ?>
			<li>Removed old tables...</li>
		<?php } ?>
		<li><?php echo ($endOffset) ? "Created tables ($endOffset)..." : 'Created tables...'; ?></li>
		<?php if ($endOffset === false) { ?>
			<li>Inserted default data...</li>
		<?php } ?>
	</ul>
</div>

<?php if ($endOffset === false) { ?>
	<form action="index.php?install/step/2b" method="post" class="blockMessage u-noJsOnly" id="js-continueForm">
		<button accesskey="s" class="button button--primary">Continue...</button>
		<?php echo $templater->fnCsrfInput($templater, $null); ?>
	</form>
<?php } else { ?>
	<form action="index.php?install/step/2" method="post" class="blockMessage u-noJsOnly" id="js-continueForm">
		<input type="hidden" name="start" value="<?php echo htmlspecialchars($endOffset); ?>" />
		<button accesskey="s" class="button button--primary">Continue...</button>
		<?php echo $templater->fnCsrfInput($templater, $null); ?>
	</form>
<?php }

$templater->inlineJs('$(\'#js-continueForm\').submit();');

?>