<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Upgrade system');
?>

<?php if ($errors) { ?>
	<div class="blockMessage blockMessage--error">
		The following errors occurred while verifying that your server can run XenForo:
		<ul>
			<?php foreach ($errors AS $error) { ?>
				<li><?php echo $error; ?></li>
			<?php } ?>
		</ul>
		Please correct these errors and try again.
	</div>
<?php } else { ?>
	<?php if ($fileErrors) { ?>
		<div class="blockMessage blockMessage--error">
			There are at least <?php echo count($fileErrors); ?> file(s) that do not appear to have the expected contents.
			Reupload the XenForo files and refresh this page.
			Only continue if you are sure all files have been uploaded properly.
		</div>
	<?php } else if (!$hasHashes) { ?>
		<div class="blockMessage blockMessage--error">
			One or more files appears to be missing. Please reupload the XenForo files and refresh this page.
			Only continue if you are sure all files have been uploaded properly.
		</div>
	<?php } ?>

	<?php if ($warnings) { ?>
		<div class="blockMessage blockMessage--warning">
			The following warnings were detected when verifying that your server can run XenForo:
			<ul>
				<?php foreach ($warnings AS $warning) { ?>
					<li><?php echo $warning; ?></li>
				<?php } ?>
			</ul>
			These will not prevent you from using XenForo, but they should be resolved if possible to ensure optimal functioning.
			However, you may still continue with the upgrade.
		</div>
	<?php } ?>

	<?php if ($addOnConflicts) { ?>
		<div class="blockMessage blockMessage--warning">
			Known conflicts with the following third-party add-ons have been detected:
			<ul>
				<?php foreach ($addOnConflicts AS $conflict) { ?>
					<li><?php echo $conflict; ?></li>
				<?php } ?>
			</ul>
			Conflicts will be resolved during the upgrade process by renaming affected database tables and columns.
			If you do not intend to use the add-ons after upgrading, you may prefer to restore the XenForo files from
			your previous version and uninstall the conflicting add-ons. The add-on authors may have more specific
			recommendations for resolving these conflicts.
		</div>
	<?php } ?>

	<?php if (!$needsConfigMigration && $isSignificantUpgrade) { ?>
		<div class="blockMessage blockMessage--important">
			<?php if ($currentVersion < 2000010) { ?>
				Upgrading to <?php echo \XF::$version ?> is a significant upgrade. No add-ons and customizations made for XenForo 1.x
				will be compatible. New versions of add-ons will need to be installed if available. Style customizations
				will need to be redone. Any previous template customizations will not be maintained after the upgrade.<br />
				<br />
				We strongly recommend you <strong>make a backup</strong> before continuing. This
				backup must include both the database and all XenForo-related files. Once you upgrade, the only way to
				revert to the previous version will be to restore a backup.
			<?php } else { ?>
				Upgrading to <?php echo \XF::$version ?> is a significant upgrade. Existing add-ons and customizations may no longer be compatible
				with the new version. We strongly recommend you <strong>make a backup</strong> before continuing. This
				backup must include both the database and all XenForo-related files. Once you upgrade, the only way to
				revert to the previous version will be to restore a backup.
			<?php } ?>
		</div>
	<?php } ?>

	<?php if (!$needsConfigMigration && $isCliRecommended) { ?>
		<div class="blockMessage blockMessage--important">
			Your XenForo installation is large. You may wish to upgrade via the command line.
			Simply run this command from within the root XenForo directory and follow the on-screen instructions:
			<pre style="margin: 1em 2em">php cmd.php xf:upgrade</pre>
			You can continue with the browser-based upgrade, but large queries may cause browser timeouts
			that will force you to reload the page.
		</div>
	<?php } ?>

	<?php if ($needsConfigMigration) { ?>
		<form action="index.php?upgrade/migrate-config" method="post" class="block">
			<div class="block-container">
				<div class="block-body block-row">
					We have detected a XenForo 1.x configuration file and before we can upgrade, this must be migrated.
					Please click the button below to begin this process.
				</div>
				<div class="block-footer">
					<button accesskey="s" class="button button--primary button--icon button--icon--save">Migrate config</button>
				</div>
			</div>
			<?php echo $templater->fnCsrfInput($templater, $null); ?>
		</form>
	<?php } else { ?>
		<form action="index.php?upgrade/run" method="post" class="block">
			<div class="block-container">
				<div class="block-body block-row">
					Click the button below to begin the upgrade to <b><?php echo \XF::$version; ?></b>.
				</div>
				<div class="block-footer">
					<button accesskey="s" class="button button--primary">Begin upgrade</button>
				</div>
			</div>
			<?php echo $templater->fnCsrfInput($templater, $null); ?>
		</form>
	<?php } ?>
<?php } ?>
