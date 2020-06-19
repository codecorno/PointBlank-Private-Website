<?php
	class_exists('XF\Install\App', false) || die('Invalid');

	$templater->setTitle('Verify configuration');
?>

<div class="block">
	<div class="block-container">
		<div class="block-body">
			<div class="block-row">
				A configuration file already exists. Would you like to use the existing values?
			</div>
			<div class="block-row">
				<dl class="pairs pairs--columns pairs--fixedSmall">
					<dt>MySQL server</dt>
					<dd>
						<?php echo htmlspecialchars($config['db']['host']); ?>
					</dd>
				</dl>

				<dl class="pairs pairs--columns pairs--fixedSmall">
					<dt>MySQL user name</dt>
					<dd>
						<?php echo htmlspecialchars($config['db']['username']); ?>
					</dd>
				</dl>

				<dl class="pairs pairs--columns pairs--fixedSmall">
					<dt>MySQL password</dt>
					<dd>
						<?php echo str_repeat("&bull;", 8); ?>
					</dd>
				</dl>

				<dl class="pairs pairs--columns pairs--fixedSmall">
					<dt>MySQL database name</dt>
					<dd>
						<?php echo htmlspecialchars($config['db']['dbname']); ?>
					</dd>
				</dl>
			</div>
		</div>
		<div class="block-footer">
			<a href="index.php?install/step/1b" class="button button--primary">Use these values</a>
			<a href="index.php?install/build-config" class="button">Edit configuration</a>
		</div>
	</div>
</div>
