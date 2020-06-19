<?php

namespace XF\Service\Upgrade;

use XF\Service\AbstractService;
use XF\Util\File;

class Checker extends AbstractService
{
	protected $apiKey;

	protected $stableOnly;

	protected $boardUrl;

	protected $usingBranding;

	protected $addOnVersions = [];

	protected function setup()
	{
		$options = $this->app->options();

		$this->apiKey = \XF::XF_LICENSE_KEY;
		$this->boardUrl = $options->boardUrl;
		$this->stableOnly = $options->upgradeCheckStableOnly;
		$this->usingBranding = trim(\XF::getCopyrightHtml()) ? true : false;

		$addOns = $this->app->finder('XF:AddOn')->fetch()
			->pluckNamed('version_id', 'addon_id');
		$addOns['XF'] = \XF::$versionId; // trust the file version more than the DB version

		// only pass XF add-ons
		$this->addOnVersions = array_filter($addOns, function($key)
		{
			return (strpos($key, 'XF') === 0);
		}, ARRAY_FILTER_USE_KEY);
	}

	public function setStableOnly($stable)
	{
		$this->stableOnly = $stable;
	}

	public function setApiKey($key)
	{
		$this->apiKey = $key;
	}

	public function check(&$detailedError = null)
	{
		if (!$this->apiKey)
		{
			return null;
		}

		$client = $this->app->http()->client();
		$errorMessage = null;
		$errorCode = null;
		$checkData = [];

		try
		{
			$response = $client->post(\XF::XF_API_URL . 'upgrade-check.json', [
				'exceptions' => false,
				'headers' => [
					'XF-LICENSE-API-KEY' => $this->apiKey
				],
				'form_params' => [
					'board_url' => $this->boardUrl,
					'addons' => $this->addOnVersions,
					'using_branding' => $this->usingBranding ? 1 : 0,
					'stable_only' => $this->stableOnly ? 1 : 0
				]
			]);

			$contents = $response->getBody()->getContents();

			try
			{
				$responseJson = \GuzzleHttp\json_decode($contents, true);
			}
			catch (\InvalidArgumentException $e)
			{
				$responseJson = null;
			}

			if (isset($responseJson['error']))
			{
				$errorCode = $responseJson['error'];

				if (isset($responseJson['error_message']))
				{
					$errorMessage = $responseJson['error_message'];
				}
				else
				{
					$errorMessage = 'An unexpected error occurred.';
				}
			}
			else if ($response->getStatusCode() === 200 && isset($responseJson['boardUrlValid']))
			{
				$checkData = [
					'board_url_valid' => $responseJson['boardUrlValid'],
					'branding_valid' => $responseJson['brandingValid'],
					'license_expired' => $responseJson['licenseExpired'],
					'invalid_add_ons' => $responseJson['invalidAddOns'],
					'available_updates' => $responseJson['availableUpdates']
				];
			}
			else
			{
				$this->logCheckFailure($e, "Unexpected result, starting '" . substr($contents, 0, 100) . "' // ");
				return null;
			}
		}
		catch (\GuzzleHttp\Exception\RequestException $e)
		{
			$this->logCheckFailure($e);
			return null;
		}

		if ($errorCode)
		{
			\XF::logError('XenForo upgrade check failed: ' . $errorMessage);
		}

		$upgradeCheck = $this->app->em()->create('XF:UpgradeCheck');
		$upgradeCheck->bulkSet($checkData);
		$upgradeCheck->error_code = $errorCode ?: null;
		$upgradeCheck->save();

		return $upgradeCheck;
	}

	protected function logCheckFailure(\Exception $e, $extraMessage = '')
	{
		\XF::logException($e, false, "XenForo upgrade check failed: $extraMessage ");
	}
}