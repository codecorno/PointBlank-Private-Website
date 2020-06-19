<?php

// Note: much of the code in this file is intentionally independent and possibly repeated. This is to reduce
// dependencies which may cause problems when upgrading.

/**
 * Class XFUpgrader
 *
 * Provides the primary logic for handling the upgrade.
 */
class XFUpgrader
{
	// set this to true to make debugging the upgrader simpler
	const DEBUGGING = false;

	protected $upgradeKey;

	/**
	 * @var ZipArchive|null
	 */
	protected $zip;
	
	protected $zipVersionId;

	public function canAttempt(&$error = null)
	{
		if (!class_exists('ZipArchive'))
		{
			$error = 'ZipArchive class does not exist.';
			return false;
		}

		$config = \XF::app()->config();
		if (!$config['enableOneClickUpgrade'])
		{
			$error = 'One-click upgrades have not been enabled.';
			return false;
		}

		if (!self::DEBUGGING)
		{
			if ($config['development']['enabled'])
			{
				$error = 'This is a development install (via dev mode).';
				return false;
			}

			$xfHashes = \XF::getAddOnDirectory() . '/XF/hashes.json';
			if (!file_exists($xfHashes))
			{
				$error = 'This is a development install (via missing hashes).';
				return false;
			}
		}

		if (!is_writable(__FILE__))
		{
			$error = 'The files are not writable.';
			return false;
		}

		$isWindows = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
		if (!$isWindows)
		{
			// Only allow the upgrade to run if we're not likely to cause mixed file ownership.
			// This is possibly over restrictive. (If relaxed in the future, we should special case
			// to prevent using root unless the files are owned by root.)

			$uid = function_exists('posix_getuid') ? posix_getuid() : fileowner(\XF\Util\File::getTempFile());
			if ($uid !== fileowner(__FILE__))
			{
				$error = 'The files are owned by a different user than the upgrade is running as.';
				return false;
			}
		}

		return true;
	}

	public function setUpgradeKey($upgradeKey, &$error = null, $fullVerification = true)
	{
		$error = null;

		if (!class_exists('ZipArchive'))
		{
			$error = 'ZipArchive class does not exist. Please upgrade manually.';
			return false;
		}
		
		$upgradeKey = preg_replace('#[^a-z0-9_\-]#i', '', $upgradeKey);
		if (!$upgradeKey)
		{
			return false;
		}

		$upgradeFile = $this->getUpgradeFile($upgradeKey);
		if (!file_exists($upgradeFile))
		{
			return false;
		}

		$zip = new ZipArchive();
		if ($zip->open($upgradeFile) !== true)
		{
			return false;
		}
		
		if (!$this->validateZipFile($zip, $error, $zipVersionId, $fullVerification))
		{
			return false;
		}

		$this->upgradeKey = $upgradeKey;
		$this->zip = $zip;
		$this->zipVersionId = $zipVersionId;

		return true;
	}

	public function validateFile($file, &$error = null, $fullVerification = true)
	{
		$zip = new ZipArchive();
		if ($zip->open($file) !== true)
		{
			$error = 'Error opening file.';
			return false;
		}

		return $this->validateZipFile($zip, $error, $zipVersionid, $fullVerification);
	}

	protected function validateZipFile(ZipArchive $zip, &$error = null, &$zipVersionId = null, $fullVerification = true)
	{
		$xfClass = $zip->getFromName('upload/src/XF.php');
		if (!$xfClass)
		{
			$error = 'The zip file does not appear to be a valid XenForo release.';
			return false;
		}

		if (!self::DEBUGGING)
		{
			if ($zip->locateName('upload/src/addons/XF/hashes.json') === false)
			{
				$error = 'The zip file does not appear to be a valid XenForo release.';
				return false;
			}
		}
		
		if (!preg_match('#public\s+static\s+\$versionId\s*=\s*(\d+)\s*;#', $xfClass, $match))
		{
			$error = 'The zip file does not appear to contain the expected contents.';
			return false;
		}
		
		$zipVersionId = intval($match[1]);

		if ($zipVersionId < \XF::$versionId)
		{
			$error = 'The zip file contains a version older than the version currently in use. Cannot continue.';
			return false;
		}

		if ($zipVersionId >= 3000000)
		{
			// assume that 3.0 won't be supported with this unless we decide otherwise
			$error = 'The zip file contains a version that cannot be upgraded to automatically.';
			return false;
		}

		if ($fullVerification)
		{
			$requirements = $zip->getFromName('upload/src/XF/Install/_upgrader/requirements.json');
			if ($requirements)
			{
				$reqJson = json_decode($requirements, true);
				if (!$this->checkRequirements($reqJson, $errors))
				{
					$error = 'The following requirements were not met for this upgrade: ' . implode(' ', $errors);
					return false;
				}
			}
		}
		
		return true;
	}

	public function checkRequirements(array $requirements, &$errors = [])
	{
		$errors = [];

		foreach ($requirements AS $productKey => $requirement)
		{
			if (is_array($requirement))
			{
				list($version, $printable) = $requirement;
			}
			else
			{
				$version = $requirement;
				$printable = null;
			}

			$enabled = false;
			$versionValid = false;

			if (strpos($productKey, 'php-ext/') === 0)
			{
				$parts = explode('/', $productKey, 2);
				$enabled = extension_loaded($parts[1]);

				if ($version === '*')
				{
					$versionValid = true;
				}
				else
				{
					$versionValid = (version_compare(phpversion($parts[1]), $version) === 1);
				}

				if ($printable === null)
				{
					$printable = "PHP extension $parts[1]";
					if ($version !== '*')
					{
						$printable .= " $version+";
					}
				}
			}
			else if ($productKey === 'php')
			{
				$enabled = true;
				$versionValid = (version_compare(phpversion(), $version) === 1);

				if ($printable === null)
				{
					$printable = "PHP $version+";
				}
			}
			else if ($productKey === 'mysql')
			{
				$mySqlVersion = \XF::db()->getServerVersion();
				if ($mySqlVersion)
				{
					$enabled = true;
					$versionValid = (version_compare(strtolower($mySqlVersion), $version) === 1);
				}

				if ($printable === null)
				{
					$printable = "MySQL $version+";
				}
			}
			else
			{
				throw new \LogicException("Unknown requirement check $productKey");
			}
			// TODO: expand to PHP function checks?

			if (!$enabled || !$versionValid)
			{
				$errors[] = "$printable is required.";
			}
		}

		return $errors ? false : true;
	}

	public function getHashes()
	{
		$file = \XF::getAddOnDirectory() . '/XF/hashes.json';
		if (!file_exists($file))
		{
			return null;
		}

		return json_decode(file_get_contents($file), true);
	}
	
	public function compareHashes()
	{
		$existingHashes = $this->getHashes();
		if ($existingHashes)
		{
			return $this->getExtractor()->compareHashes($existingHashes);
		}
		else
		{
			return null;
		}
	}
	
	public function checkWritable(array $changeset = null, &$failures = [])
	{
		return $this->getExtractor()->checkWritable($changeset, $failures);
	}

	public function copyFiles(XFUpgraderExtractAction $action, &$error)
	{
		return $this->getExtractor()->copyFiles($action, $error);
	}

	/**
	 * @return XFUpgraderExtractor
	 */
	protected function getExtractor()
	{
		if (!$this->zip)
		{
			throw new \LogicException("Zip not opened yet");
		}

		return new XFUpgraderExtractor($this->zip);
	}

	/**
	 * @param array|null $hashChanges
	 * @param int $start
	 * @param int|null $maxTime
	 *
	 * @return XFUpgraderExtractAction
	 */
	public function getExtractAction(array $hashChanges = null, $start = 0, $maxTime = null)
	{
		return new XFUpgraderExtractAction($hashChanges, $start, $maxTime);
	}

	public function cleanUp()
	{
		if (!$this->upgradeKey)
		{
			return;
		}

		if ($this->zip)
		{
			$this->zip->close();
			$this->zip = null;
		}

		if (!self::DEBUGGING)
		{
			@unlink($this->getUpgradeFile());
		}
	}

	protected function getUpgradeFile($upgradeKey = null)
	{
		$upgradeKey = $upgradeKey ?: $this->upgradeKey;

		$dir = \XF\Util\File::getTempDir();
		return "$dir/upgrade-{$upgradeKey}.zip";
	}

	public function getZipVersionId()
	{
		return $this->zipVersionId;
	}
}

class XFUpgraderExtractAction
{
	protected $hashChanges = null;

	protected $matchFiles = [];
	protected $skipFiles = [];
	protected $start = 0;
	protected $maxTime = null;
	protected $forceUpdateHashFile = false;

	/**
	 * @var \Closure|null
	 */
	protected $tickHandler = null;

	protected $matchRegex = null;
	protected $skipRegex = null;

	public function __construct(array $hashChanges = null, $start = 0, $maxTime = null)
	{
		$this->hashChanges = $hashChanges;
		$this->start = max(0, intval($start));
		$this->maxTime = $maxTime;
	}

	public function setMatchFiles(array $files)
	{
		$this->matchFiles = $files;
		$this->matchRegex = $this->getFileRegex($files);

		return $this;
	}

	public function setSkipFiles(array $files)
	{
		$this->skipFiles = $files;
		$this->skipRegex = $this->getFileRegex($files);

		return $this;
	}

	protected function getFileRegex(array $files)
	{
		if (!$files)
		{
			return null;
		}

		$regexParts = [];
		foreach ($files AS $file)
		{
			$part = preg_quote($file, '#');
			$part = str_replace('\\*', '.*', $part);
			$regexParts[] = $part;
		}
		return '#^(' . implode('|', $regexParts) . ')$#';
	}

	public function setForceUpdateHashFile()
	{
		$this->forceUpdateHashFile = true;

		return $this;
	}

	public function getForceUpdateHashFile()
	{
		return $this->forceUpdateHashFile;
	}

	public function setTickHandler(\Closure $tick)
	{
		$this->tickHandler = $tick;
	}

	public function getHashChanges()
	{
		return $this->hashChanges;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getMaxTime()
	{
		return $this->maxTime;
	}

	public function isFileMatched($fsFileName)
	{
		if (is_array($this->hashChanges) && !isset($this->hashChanges[$fsFileName]))
		{
			// file hasn't been updated
			return false;
		}

		if ($this->skipRegex && preg_match($this->skipRegex, $fsFileName))
		{
			// file matches the skip regex
			return false;
		}

		if ($this->matchRegex && !preg_match($this->matchRegex, $fsFileName))
		{
			// file does not match match regex
			return false;
		}

		return true;
	}

	public function onTick($i, $totalFiles, $zipFileName, XFUpgraderExtractor $extractor)
	{
		if ($this->tickHandler)
		{
			$tick = $this->tickHandler;
			$tick($zipFileName, $i, $totalFiles, $extractor, $this);
		}
	}
}

/**
 * Class XFUpgraderExtractor
 *
 * Manages extracting files from the upgrade zip.
 *
 * Striking similarity to src/XF/Service/AddOnArchive/Extractor.php. Changes should be mirrored as necessary.
 */
class XFUpgraderExtractor
{
	/**
	 * @var ZipArchive
	 */
	protected $zip;

	public function __construct(ZipArchive $zip)
	{
		$this->zip = $zip;
	}

	public function compareHashes(array $existingHashes)
	{
		$newHashes = $this->getNewHashes();
		if (!$newHashes)
		{
			return null;
		}

		$changes = [];
		foreach ($newHashes AS $file => $newHash)
		{
			if (!isset($existingHashes[$file]))
			{
				$changes[$file] = 'create';
			}
			else if ($newHash !== $existingHashes[$file])
			{
				$changes[$file] = 'update';
			}
		}

		$changes[preg_replace('#^upload/#', '', $this->getHashFileName())] = 'update';

		foreach ($existingHashes AS $oldFile => $null)
		{
			if (!isset($newHashes[$oldFile]))
			{
				$changes[$oldFile] = 'delete';
			}
		}

		return $changes;
	}

	public function checkWritable(array $changeset = null, &$failures = [])
	{
		$zip = $this->zip;
		$failures = [];

		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$zipFileName = $zip->getNameIndex($i);
			$fsFileName = $this->getFsFileNameFromZipName($zipFileName);
			if ($fsFileName === null)
			{
				continue;
			}

			if (is_array($changeset) && !isset($changeset[$fsFileName]))
			{
				// we're not changing this file
				continue;
			}

			if (!\XF\Util\File::isWritable($this->getFinalFsFileName($fsFileName)))
			{
				$failures[] = $fsFileName;
			}
		}

		return $failures ? false : true;
	}

	public function copyFiles(XFUpgraderExtractAction $action, &$error)
	{
		$zip = $this->zip;
		$start = $action->getStart();
		$maxTime = $action->getMaxTime();

		$lastComplete = $start;
		$totalFiles = $zip->numFiles;

		$s = microtime(true);

		for ($i = $start; $i < $totalFiles; $i++)
		{
			$lastComplete = $i;

			$zipFileName = $zip->getNameIndex($i);
			$targetWritten = $this->writeFileFromZip(
				$zipFileName,
				function($fsFileName) use ($action)
				{
					return $action->isFileMatched($fsFileName);
				}
			);

			if (!$targetWritten)
			{
				$error = "Failed write to {$zipFileName}";
				return false;
			}

			$action->onTick($zipFileName, $i, $totalFiles, $this);

			if ($maxTime !== null && (microtime(true) - $s) > $maxTime)
			{
				break;
			}
		}

		$complete = ($i >= $zip->numFiles);

		if ($complete && $action->getForceUpdateHashFile())
		{
			// if we don't have a new hashes file, we need to remove the old one if it exists as it will be wrong
			$hashZipFileName = $this->getHashFileName();
			if ($zip->locateName($hashZipFileName) === false)
			{
				$fsFileName = $this->getFsFileNameFromZipName($hashZipFileName);
				$finalFileName = $this->getFinalFsFileName($fsFileName);
				if (file_exists($finalFileName) && !@unlink($finalFileName))
				{
					$error = "Failed write to {$fsFileName}";
					return false;
				}
			}
		}

		return [
			'status' => ($complete ? 'complete' : 'incomplete'),
			'last' => $lastComplete,
			'percent' => ($complete || !$zip->numFiles) ? 100 : 100 * ($lastComplete / $zip->numFiles)
		];
	}

	protected function writeFileFromZip($zipFileName, \Closure $checkWriteNeeded = null)
	{
		$fsFileName = $this->getFsFileNameFromZipName($zipFileName);
		if ($fsFileName === null)
		{
			// not a writable file - consider fine
			return true;
		}

		$finalFileName = $this->getFinalFsFileName($fsFileName);

		if ($checkWriteNeeded)
		{
			$isWriteNeeded = $checkWriteNeeded($fsFileName, $finalFileName, $zipFileName);
			if (!$isWriteNeeded)
			{
				// no action required, so consider fine
				return true;
			}
		}

		$dataStream = $this->zip->getStream($zipFileName);
		return @\XF\Util\File::writeFile($finalFileName, $dataStream, false);
	}

	protected function getNewHashes()
	{
		$newHashesJson = $this->zip->getFromName($this->getHashFileName());
		if (!$newHashesJson)
		{
			return null;
		}

		return json_decode($newHashesJson, true);
	}

	protected function getFsFileNameFromZipName($fileName)
	{
		if (substr($fileName, -1) === '/')
		{
			// this is a directory we can just skip this
			return null;
		}

		if (!preg_match("#^upload/.#", $fileName))
		{
			// file outside of "upload" so we can just skip this
			return null;
		}

		return substr($fileName, 7); // remove "upload/"
	}

	protected function getFinalFsFileName($fileName)
	{
		return \XF::getRootDirectory() . \XF::$DS . $fileName;
	}

	protected function getHashFileName()
	{
		return "upload/src/addons/XF/hashes.json";
	}
}

/**
 * Class XFUpgraderWeb
 *
 * Provides the logic for triggering the upgrade via the web (with page refreshes, etc).
 */
class XFUpgraderWeb
{
	/**
	 * @var \XF\Http\Request
	 */
	protected $request;

	/**
	 * @var \XF\Template\Templater
	 */
	protected $templater;

	/**
	 * @var XFUpgrader
	 */
	protected $upgrader;

	protected $key;
	protected $state = [];

	public function __construct(\XF\App $app)
	{
		$this->request = $app->request();
		$this->templater = $app->templater();
		$this->upgrader = new XFUpgrader();
	}

	public function run()
	{
		$request = $this->request;
		if (!$request->isPost())
		{
			header('Location: index.php?upgrade/');
			return;
		}

		$key = $request->filter('key', 'string');
		if (!$this->upgrader->setUpgradeKey($key, $error))
		{
			if ($error)
			{
				$this->outputError($error . ' Please upgrade manually.');
			}
			else
			{
				$this->outputError('Invalid key. Please upgrade manually.');
			}
			return;
		}

		$this->key = $key;

		\XF::app()->error()->setForceShowTrace(true);

		if (!$this->upgrader->canAttempt($error))
		{
			$this->outputError("Cannot attempt: $error Please upgrade manually.");
			$this->cleanUp();
			return;
		}

		$step = $request->filter('step', 'string');
		if (!$step)
		{
			$step = 'init';
		}
		else if ($step === 'copy' || $step === 'reinit')
		{
			// these are old upgrader steps that selfupdate use to point to -- need to repoint to the new
			// method to ensure full updates from older versions
			$step = 'postselfupdate';
		}
		$stepMethod = 'step' . $step;

		if (!method_exists($this, $stepMethod))
		{
			$this->outputError("Failed to find step $step. Please upgrade manually.");
			return;
		}

		$this->state = $request->filter('state', 'json-array');
		$params = $request->filter('params', 'json-array');

		$result = $this->$stepMethod($params);

		if (is_array($result))
		{
			$newStep = $step;
			$newParams = $result;
		}
		else if (is_string($result))
		{
			$newStep = $result;
			$newParams = [];
		}
		else if ($result === false)
		{
			// indicates we're already generated the page so stop
			return;
		}
		else
		{
			throw new \LogicException("{$stepMethod} didn't return the expected data");
		}

		$ticks = $this->request->filter('ticks', 'uint');

		$this->outputResult($newStep, $newParams, $ticks);
	}

	// Setup and ensure that we can continue
	protected function stepInit(array $params)
	{
		// note that this is redone after the self-update
		$hashChanges = $this->upgrader->compareHashes();
		if (!$this->upgrader->checkWritable($hashChanges, $failures))
		{
			$this->outputError('Not all files are writable. Please upgrade manually.');
			$this->cleanUp();
			return false;
		}

		$this->state['changes'] = $hashChanges;

		return 'selfupdate';
	}

	// Update the updater first to benefit from bug fixes
	protected function stepSelfUpdate(array $params)
	{
		$files = [
			'src/XF/Install/_upgrader/*',
			'src/XF/Install/_templates/*',
			'install/oc-upgrader.php'
		];
		if (!$this->copyNamedFilesOrError($files))
		{
			return false;
		}

		// This step should always go here and, if necessary, the target of the post self-update step should be changed.
		// Otherwise, the self-update that happens here won't pick up changes that re-point the next step.
		return 'postselfupdate';
	}

	// Placeholder step to allow changes to what we do after the self update
	protected function stepPostSelfUpdate(array $params)
	{
		// This step should always be empty and simply redirect to the "real" step to take after the self update.
		return 'composerdeps';
	}

	// update all of our composer dependencies, except for composer itself
	protected function stepComposerDeps(array $params)
	{
		$params = array_replace([
			'start' => 0
		], $params);

		$action = $this->upgrader->getExtractAction(
			$this->getHashChanges(), $params['start'], \XF::app()->config('jobMaxRunTime')
		);
		$action->setMatchFiles(['src/vendor/*']);
		$action->setSkipFiles(['src/vendor/composer/*']);

		return $this->copyActionPaginated($action, 'composercore', 'Dependencies');
	}

	// now update composer itself
	protected function stepComposerCore(array $params)
	{
		$files = ['src/vendor/composer/*'];
		if (!$this->copyNamedFilesOrError($files))
		{
			return false;
		}

		return 'reinitxf';
	}

	// don't name a step "reinit" -- it's a legacy step name that old versions of the upgrader may
	// redirect to, so there's code to handle it.
	// reinit our hashes to be safe
	protected function stepReInitXf(array $params)
	{
		// redo this after the partial updates to avoid bugs
		$this->state['changes'] = $this->upgrader->compareHashes();

		return 'copyxf';
	}

	// don't name a step "copy" -- it's a legacy step name that old versions of the upgrader may
	// redirect to, so there's code to handle it.
	// copy over the non-composer files
	protected function stepCopyXf(array $params)
	{
		$params = array_replace([
			'start' => 0
		], $params);

		$action = $this->upgrader->getExtractAction(
			$this->getHashChanges(), $params['start'], \XF::app()->config('jobMaxRunTime')
		);
		$action->setSkipFiles(['src/vendor/*']);

		return $this->copyActionPaginated($action, 'complete', 'Core files');
	}

	// clean up and redirect to the upgrader
	protected function stepComplete(array $params)
	{
		$this->cleanUp();

		$app = \XF::app();
		$basicUpgradeRedirect = true;

		if ($app instanceof \XF\Install\App)
		{
			$app->setupUpgradeSession();
			if (\XF::visitor()->is_admin)
			{
				// we have an install session
				$basicUpgradeRedirect = false;
			}
		}

		$upgrader = new \XF\Install\Upgrader($app);
		if ($upgrader->isCliRecommended())
		{
			// if we recommend CLI upgrades, force this
			$basicUpgradeRedirect = true;
		}

		if ($basicUpgradeRedirect)
		{
			header('Location: index.php?upgrade/');
		}
		else
		{
			// output a page to post into the upgrade system
			$content = $this->templater->renderTemplate('upgrade_oc_complete');
			$this->outputContainer($content);
		}

		return false;
	}

	protected function copyActionPaginated(XFUpgraderExtractAction $action, $nextStepName, $stepTitle = 'Files')
	{
		$result = $this->copyActionOrError($action);
		if (!$result)
		{
			return false;
		}

		switch ($result['status'])
		{
			case 'incomplete':
				$params['start'] = $result['last'] + 1;
				$params['percent'] = $result['percent'];
				$params['title'] = $stepTitle;
				return $params;

			case 'complete':
				\XF\Util\Php::resetOpcache();
				return $nextStepName;

			default:
				throw new \LogicException("Unknown result from copy '$result[status]'");
		}
	}

	protected function copyActionOrError(XFUpgraderExtractAction $action)
	{
		$result = $this->upgrader->copyFiles($action, $error);
		if (!$result)
		{
			$this->outputError('One or more files failed to copy. Please upgrade manually.');
			$this->cleanUp();
			return false;
		}

		return $result;
	}

	protected function copyNamedFilesOrError(array $files, $resetOpcache = true)
	{
		if (!$files)
		{
			return true;
		}

		$action = $this->upgrader->getExtractAction($this->getHashChanges());
		$action->setMatchFiles($files);

		if (!$this->copyActionOrError($action))
		{
			return false;
		}

		if ($resetOpcache)
		{
			\XF\Util\Php::resetOpcache();
		}

		return true;
	}

	/**
	 * @return array|null
	 */
	protected function getHashChanges()
	{
		if (isset($this->state['changes']) && is_array($this->state['changes']))
		{
			return $this->state['changes'];
		}
		else
		{
			return null;
		}
	}

	protected function outputResult($step, array $params, $lastTicks)
	{
		$state = $this->state;
		$ticks = max(0, intval($lastTicks)) + 1;

		$content = $this->templater->renderTemplate('upgrade_oc_step', [
			'key' => $this->key,
			'step' => $step,
			'ticks' => $ticks,
			'state' => $state,
			'params' => $params
		]);
		$this->outputContainer($content);
	}

	protected function outputError($message, $code = 400)
	{
		$content = $this->templater->renderTemplate('error', [
			'error' => $message
		]);
		$this->outputContainer($content, $code);
	}

	protected function outputContainer($content, $code = 200)
	{
		$pageParams = $this->templater->pageParams;
		$pageParams['content'] = $content;

		header('Content-type: text/html; charset=utf-8', true, $code);
		echo $this->templater->renderTemplate('PAGE_CONTAINER', $pageParams);
	}

	protected function cleanUp()
	{
		$this->upgrader->cleanUp();
	}

	/**
	 * Factory creation method. This is to move as much code into this file as possible to allow flexibility
	 * with future changes.
	 *
	 * @param string $rootDir Root of XF install
	 *
	 * @return XFUpgraderWeb
	 */
	public static function create($rootDir)
	{
		require($rootDir . '/src/XF.php');
		XF::start($rootDir);

		$app = XF::setupApp('XF\Install\App');
		$app->start();

		return new XFUpgraderWeb($app);
	}
}