<?php

namespace XF\Captcha;

use XF\Template\Templater;

class KeyCaptcha extends AbstractCaptcha
{
	/**
	 * KeyCAPTCHA user ID
	 *
	 * @var null|string
	 */
	protected $keyUserId = null;

	/**
	 * KeyCAPTCHA private key
	 *
	 * @var null|string
	 */
	protected $privateKey = null;
	
	public function __construct(\XF\App $app)
	{
		parent::__construct($app);
		$extraKeys = $app->options()->extraCaptchaKeys;
		if (!empty($extraKeys['keyCaptchaUserId']) && !empty($extraKeys['keyCaptchaPrivateKey']))
		{
			$this->keyUserId = $extraKeys['keyCaptchaUserId'];
			$this->privateKey = $extraKeys['keyCaptchaPrivateKey'];
		}
	}
	
	public function renderInternal(Templater $templater)
	{
		if (!$this->keyUserId || !$this->privateKey)
		{
			return '';
		}

		$ip = $this->app->request()->getIp();
		$sessionId = md5(uniqid('xfkeycaptcha'));
		$sign = md5($sessionId . $ip . $this->privateKey);
		$sign2 = md5($sessionId . $this->privateKey);

		return $templater->renderTemplate('public:captcha_keycaptcha', [
			'keyUserId' => $this->keyUserId,
			'sessionId' => $sessionId,
			'sign' => $sign,
			'sign2' => $sign2
		]);
	}

	public function isValid()
	{
		if (!$this->keyUserId || !$this->privateKey)
		{
			return true; // if not configured, always pass
		}

		$request = $this->app->request();

		$code = $request->get('keycaptcha_code');
		if (!$code || !is_string($code))
		{
			return false;
		}

		$parts = explode('|', $code);
		if (count($parts) < 4)
		{
			return false;
		}

		if ($parts[0] !== md5('accept' . $parts[1] . $this->privateKey . $parts[2]))
		{
			return false;
		}

		if (substr($parts[2], 0, 7) !== 'http://')
		{
			return false;
		}

		try
		{
			$client = $this->app->http()->client();
			$response = $client->get($parts[2]);
			$contents = trim($response->getBody()->getContents());

			return ($contents == '1');
		}
		catch(\GuzzleHttp\Exception\RequestException $e)
		{
			// this is an exception with the underlying request, so let it go through
			\XF::logException($e, false, 'KeyCAPTCHA connection error: ');
			return true;
		}
	}
}