<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null captcha_question_id
 * @property string question
 * @property array answers
 * @property bool active
 *
 * GETTERS
 * @property string hash
 */
class CaptchaQuestion extends Entity
{
	public function isCorrect($answer)
	{
		return in_array(strtolower($answer), array_map('strtolower', $this->answers));
	}

	protected function insertHash()
	{
		$hash = sha1(
			'Question'
			. $this->captcha_question_id
			. $this->app()->config('globalSalt')
			. uniqid(microtime(), true)
		);

		$captchaLog = $this->_em->create('XF:CaptchaLog');
		$captchaLog->bulkSet([
			'hash' => $hash,
			'captcha_type' => 'Question',
			'captcha_data' => $this->captcha_question_id,
			'captcha_date' => time()
		]);
		$captchaLog->save();

		return $captchaLog;
	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		return $this->insertHash()->hash;
	}

	public function verifyAnswers(&$answers)
	{
		$answers = array_filter($answers, function($answer)
		{
			return trim(strval($answer)) !== '';
		});
		if (!$answers)
		{
			$this->error(\XF::phrase('please_provide_at_least_one_answer'), 'answers');
			return false;
		}
		return true;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_captcha_question';
		$structure->shortName = 'XF:CaptchaQuestion';
		$structure->primaryKey = 'captcha_question_id';
		$structure->columns = [
			'captcha_question_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'question' => ['type' => self::STR, 'maxLength' => 250, 'required' => true],
			'answers' => ['type' => self::JSON_ARRAY, 'required' => true],
			'active' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [
			'hash' => true
		];
		$structure->relations = [];

		return $structure;
	}
}