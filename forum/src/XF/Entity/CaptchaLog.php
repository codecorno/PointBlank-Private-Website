<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string|null hash
 * @property string captcha_type
 * @property string captcha_data
 * @property int captcha_date
 *
 * RELATIONS
 * @property \XF\Entity\CaptchaQuestion Question
 */
class CaptchaLog extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_captcha_log';
		$structure->shortName = 'XF:CaptchaLog';
		$structure->primaryKey = 'hash';
		$structure->columns = [
			'hash' => ['type' => self::STR, 'nullable' => true],
			'captcha_type' => ['type' => self::STR, 'maxLength' => 250, 'required' => true],
			'captcha_data' => ['type' => self::STR, 'maxLength' => 250, 'required' => true],
			'captcha_date' => ['type' => self::UINT, 'default' => time()]
		];
		$structure->getters = [];
		$structure->relations = [
			'Question' => [
				'entity' => 'XF:CaptchaQuestion',
				'type' => self::TO_ONE,
				'conditions' => [
					['$captcha_type', '=', 'Question'],
					['captcha_question_id', '=', '$captcha_data']
				],
				'primary' => false
			]
		];

		return $structure;
	}
}