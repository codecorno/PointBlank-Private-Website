<?php

$phpVersion = phpversion();
if (version_compare($phpVersion, '5.6.0', '<'))
{
	die("PHP 5.6.0 or newer is required. $phpVersion does not meet this requirement. Please ask your host to upgrade PHP.");
}

$rootDir = realpath(__DIR__ . '/..');
chdir($rootDir);

require($rootDir . '/src/XF/Install/_upgrader/core.php');
$upgrader = XFUpgraderWeb::create($rootDir);
$upgrader->run();