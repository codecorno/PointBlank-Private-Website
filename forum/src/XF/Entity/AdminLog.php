<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null admin_log_id
 * @property int user_id
 * @property int request_date
 * @property string request_url_
 * @property array request_data
 * @property string ip_address
 *
 * GETTERS
 * @property string request_url
 * @property string request_url_short
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class AdminLog extends Entity
{
	/**
	 * @return string
	 */
	public function getRequestUrl()
	{
		$url = rawurldecode($this->request_url_);
		if (!preg_match('/./su', $url))
		{
			$url = $this->request_url_;
		}

		return $url;
	}

	/**
	 * @return string
	 */
	public function getRequestUrlShort()
	{
		$url = $this->request_url;

		$length = utf8_strlen($url);
		if ($length > 80)
		{
			$zwsp = chr(0xE2) . chr(0x80) . chr(0x8B);
			$url = utf8_substr_replace($url, '...' . $zwsp, 25, $length - 25 - 35);
		}

		return $url;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_admin_log';
		$structure->shortName = 'XF:Adminlog';
		$structure->primaryKey = 'admin_log_id';
		$structure->columns = [
			'admin_log_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'request_url' => ['type' => self::STR, 'required' => true],
			'request_data' => ['type' => self::JSON_ARRAY, 'default' => []],
			'ip_address' => ['type' => self::BINARY, 'maxLength' => 16, 'default' => '']
		];
		$structure->getters = [
			'request_url' => true,
			'request_url_short' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
		];

		return $structure;
	}
}