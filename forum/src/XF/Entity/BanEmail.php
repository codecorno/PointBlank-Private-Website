<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string banned_email
 * @property int create_user_id
 * @property int create_date
 * @property string reason
 * @property int last_triggered_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class BanEmail extends Entity
{
	protected function verifyBannedEmail(&$email)
	{
		if ($email == '*' || $email === '')
		{
			$this->error(\XF::phrase('you_must_enter_at_least_one_non_wildcard_character')	, 'banned_email');
			return false;
		}

		if (strpos($email, '*') === false)
		{
			if (strpos($email, '@') === false)
			{
				$email = '*' . $email;
			}
			if (strpos($email, '.') === false)
			{
				$email .= '*';
			}
		}

		if ($email[0] == '@')
		{
			$email = '*' . $email;
		}

		$lastChar = substr($email, -1);
		if ($lastChar == '.' || $lastChar == '@')
		{
			$email .= '*';
		}

		$atPos = strpos($email, '@');
		if ($atPos !== false && strpos($email, '.', $atPos) === false && strpos($email, '*', $atPos) === false)
		{
			$email .= '*';
		}

		if ($email == '*@*' || $email == '*.*')
		{
			$this->error(\XF::phrase('this_would_ban_all_email_addresses'), 'banned_email');
			return false;
		}

		$email = preg_replace('/\*{2,}/', '*', $email);
		return true;
	}

	protected function _postSave()
	{
		$this->rebuildBannedEmailCache();
	}

	protected function _postDelete()
	{
		$this->rebuildBannedEmailCache();
	}

	protected function rebuildBannedEmailCache()
	{
		\XF::runOnce('bannedEmailCache', function()
		{
			$this->getBanningRepo()->rebuildBannedEmailCache();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_ban_email';
		$structure->shortName = 'XF:BanEmail';
		$structure->primaryKey = 'banned_email';
		$structure->columns = [
			'banned_email' => ['type' => self::STR, 'required' => true, 'maxLength' => 120,
				'unique' => 'banned_emails_must_be_unique',
			],
			'create_user_id' => ['type' => self::UINT, 'required' => true],
			'create_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'reason' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'last_triggered_date' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$create_user_id']
				],
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Banning
	 */
	protected function getBanningRepo()
	{
		return $this->repository('XF:Banning');
	}
}