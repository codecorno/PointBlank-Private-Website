<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrade errors');
?>

<div class="blockMessage blockMessage--error">
	Uh oh, your upgrade to <?php echo htmlspecialchars(\XF::$version); ?> has failed! The following elements of the database are incorrect:
	<ul>
		<?php foreach ($errors AS $error) { ?>
			<li><?php echo $error; ?></li>
		<?php } ?>
	</ul>
	This is likely caused by an add-on conflict. You may need to restore a backup, remove the offending add-on data from the database, and retry the upgrade. Contact support if you are not sure how to proceed.
</div>