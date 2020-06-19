<?php

namespace XF\Api\Controller;

use XF\Mvc\ParameterBag;

/**
 * @api-group Index
 */
class Index extends AbstractController
{
	/**
	 * @api-desc Gets general information about the site and the API
	 *
	 * @api-out int $version_id XenForo version ID
	 * @api-out str $site_title Title of the site this API relates to
	 * @api-out str $base_url The base URL of the XenForo install this API relates to
	 * @api-out str $api_url The base API URL
	 * @api-out str $key[type] Type of the API key accessing the API (guest, user or super)
	 * @api-out int|null $key[user_id] If a user key, the ID of the user the key is for; null otherwise
	 * @api-out bool $key[allow_all_scopes] If true, all scopes can be accessed
	 * @api-out str[] $key[scopes] A list of scopes this key can access (if not allowed to access all scopes)
	 */
	public function actionGet()
	{
		$options = $this->options();

		$key = \XF::apiKey();

		return $this->apiResult([
			'version_id' => \XF::$versionId,
			'site_title' => $options->boardTitle,
			'base_url' => $options->boardUrl,
			'api_url' => $this->buildLink('canonical:index'),
			'key' => [
				'type' => $key->key_type,
				'user_id' => $key->key_type == 'user' ? $key->user_id : null,
				'allow_all_scopes' => $key->allow_all_scopes,
				'scopes' => $key->scopes
			]
		]);
	}
}