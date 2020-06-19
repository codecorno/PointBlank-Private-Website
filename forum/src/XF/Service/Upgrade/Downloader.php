<?php

namespace XF\Service\Upgrade;

use XF\Service\AbstractService;

class Downloader extends AbstractService
{
	protected $addOnId;

	protected $targetFile;

	protected $validateFile = true;

	public function __construct(\XF\App $app, $addOnId)
	{
		parent::__construct($app);

		$this->addOnId = $addOnId;

		@ignore_user_abort(true);
		@set_time_limit(0);
	}

	public function setDownloadTarget($file)
	{
		$this->targetFile = $file;
	}

	public function setValidateFile($validateFile)
	{
		$this->validateFile = boolval($validateFile);
	}

	public function download($targetVersionId, $releaseDate, &$error = null)
	{
		$app = $this->app;
		$client = $app->http()->client();
		$targetFile = $this->targetFile ?: \XF\Util\File::getTempFile();

		try
		{
			$client->post(\XF::XF_API_URL . 'upgrade-download.json', [
				'headers' => [
					'XF-LICENSE-API-KEY' => \XF::XF_LICENSE_KEY
				],
				'sink' => $targetFile,
				'form_params' => [
					'addon_id' => $this->addOnId,
					'version_id' => $targetVersionId,
					'release_date' => $releaseDate,
					'stable_only' => $app->options()->upgradeCheckStableOnly ? 1 : 0
				]
			]);
		}
		catch (\GuzzleHttp\Exception\RequestException $e)
		{
			$responseJson = \GuzzleHttp\json_decode(
				$e->getResponse()->getBody()->getContents(), true
			);
			if (is_array($responseJson['error']))
			{
				$error = reset($responseJson['error']);
			}
			else
			{
				$error = $responseJson['error'];
			}

			@unlink($targetFile);

			return false;
		}

		if ($this->validateFile)
		{
			if (!$this->validateUpgradeFile($targetFile, $error))
			{
				@unlink($targetFile);
				return false;
			}
		}
		
		return $targetFile;
	}

	protected function validateUpgradeFile($file, &$error)
	{
		require_once(\XF::getRootDirectory() . '/src/XF/Install/_upgrader/core.php');

		$upgrader = new \XFUpgrader();

		return $upgrader->validateFile($file, $error);
	}
}