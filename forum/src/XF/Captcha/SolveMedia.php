<?php

namespace XF\Captcha;

use XF\Template\Templater;

class SolveMedia extends AbstractCaptcha
{
	/**
	 * Challenge key
	 *
	 * @var null|string
	 */
	protected $cKey = null;

	/**
	 * Verification key
	 *
	 * @var null|string
	 */
	protected $vKey = null;
	
	public function __construct(\XF\App $app)
	{
		parent::__construct($app);
		$extraKeys = $app->options()->extraCaptchaKeys;
		if (!empty($extraKeys['solveMediaCKey']) && !empty($extraKeys['solveMediaVKey']))
		{
			$this->cKey = $extraKeys['solveMediaCKey'];
			$this->vKey = $extraKeys['solveMediaVKey'];
		}
	}
	
	public function renderInternal(Templater $templater)
	{
		if (!$this->cKey)
		{
			return '';
		}

		return $templater->renderTemplate('public:captcha_solve_media', [
			'cKey' => $this->cKey
		]);
	}

	public function isValid()
	{
		if (!$this->cKey || !$this->vKey)
		{
			return true; // if not configured, always pass
		}

		$request = $this->app->request();

		$challenge = substr($request->filter('adcopy_challenge', 'str'), 0, 2000);
		$captchaResponse = substr($request->filter('adcopy_response', 'str'), 0, 2000);
		if (!$challenge || !$captchaResponse)
		{
			return false;
		}

		try
		{
			$client = $this->app->http()->client();

			$params = [
				'form_params' => [
					'privatekey' => $this->vKey,
					'challenge' => $challenge,
					'response' => $captchaResponse,
					'remoteip' => $request->getIp()
				]
			];
			$response = $client->post('http://verify.solvemedia.com/papi/verify', $params);
			$contents = trim($response->getBody()->getContents());
			$parts = explode("\n", $contents, 3);
			$result = trim($parts[0]);
			$error = isset($parts[1]) ? trim($parts[1]) : null;

			if ($result == 'true')
			{
				return true;
			}

			switch ($error)
			{
				case 'wrong answer':
				case 'invalid remoteip':
					// generally end user mistakes
					return false;

				case 'invalid challenge':
					// actually indicates the challenge key has been tampered with
					// these should definitely not pass
					return false;

				default:
					// this is likely a configuration error, log and let it through
					$this->app->error()->logError("Solve Media CAPTCHA error: $error");
					return true;
			}
		}
		catch(\GuzzleHttp\Exception\RequestException $e)
		{
			// this is an exception with the underlying request, so let it go through
			\XF::logException($e, false, 'Solve Media connection error: ');
			return true;
		}
	}
}