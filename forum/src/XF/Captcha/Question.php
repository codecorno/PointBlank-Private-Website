<?php

namespace XF\Captcha;

use XF\Template\Templater;

class Question extends AbstractCaptcha
{
	public function renderInternal(Templater $templater)
	{
		$finder = $this->app->finder('XF:CaptchaQuestion');

		$question = $finder->where('active', 1)
			->order($finder->expression('RAND()'))
			->fetchOne();

		return $templater->renderTemplate('public:captcha_question', [
			'question' => $question
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
			if ($captchaLog->Question)
			{
				$isCorrect = $captchaLog->Question->isCorrect($answer);
			}
			$captchaLog->delete();
		}

		return $isCorrect;
	}
}