<?php

namespace XF\Captcha;

use XF\Template\Templater;

class TextCaptcha extends AbstractCaptcha
{
	public function renderInternal(Templater $templater)
	{
		$extraKeys = $this->app->options()->extraCaptchaKeys;
		$apiKey = !empty($extraKeys['textCaptchaApiKey']) ? $extraKeys['textCaptchaApiKey'] : null;

		try
		{
			$client = $this->app->http()->client();
			$response = \GuzzleHttp\json_decode(
				$client->get("http://api.textcaptcha.com/$apiKey.json")->getBody()->getContents(), true
			);
		}
		catch (\GuzzleHttp\Exception\RequestException $e)
		{
			// this is an exception with the underlying request, so let it go through
			\XF::logException($e, false, 'Error fetching textCAPTCHA: ');
			$response = null;
		}
		if (!$response)
		{
			$response = [
				'q' => '',
				'a' => ['failed']
			];
		}
		$response['hash'] = md5($response['q'] . uniqid() . memory_get_usage());

		$captchaLog = $this->app->em()->create('XF:CaptchaLog');
		$captchaLog->bulkSet([
			'hash' => $response['hash'],
			'captcha_type' => 'TextCaptcha',
			'captcha_data' => implode(',', $response['a']),
			'captcha_date' => time()
		]);
		$captchaLog->save();

		return $templater->renderTemplate('public:captcha_textcaptcha', [
			'question' => $response
		]);
	}

	public function isValid()
	{
		$request = $this->app->request();

		$answer = $request->filter('captcha_question_answer', 'str');
		$hash = $request->filter('captcha_question_hash', 'str');

		$isCorrect = false;

		$captchaLog = $this->app->em()->find('XF:CaptchaLog', $hash);
		if ($captchaLog)
		{
			if ($captchaLog->captcha_type == 'TextCaptcha')
			{
				if ($captchaLog->captcha_data == 'failed')
				{
					// request failed, we need to pass this every time
					$isCorrect = true;
				}
				else
				{
					$answerMd5 = md5(strtolower(trim($answer)));
					$correct = explode(',', $captchaLog->captcha_data);

					$isCorrect = in_array($answerMd5, $correct, true);
				}
			}
			$captchaLog->delete();
		}

		return $isCorrect;
	}
}