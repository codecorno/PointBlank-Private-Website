<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class CaptchaQuestion extends Repository
{
	/**
	 * @return Finder
	 */
	public function findCaptchaQuestionsForList()
	{
		return $this->finder('XF:CaptchaQuestion')->order(['captcha_question_id']);
	}

	/**
	 * Removes all CAPTCHAs that are older than the specified expiry length.
	 *
	 * @param integer $expiry Delete CAPTCHAs older than this (in seconds)
	 */
	public function cleanUpCaptchaLog($expiry = 86400)
	{
		$this->db()->delete('xf_captcha_log', 'captcha_date < ?', time() - $expiry);
	}
}