<?php

namespace XF\Entity;

use XF\CustomField\Set;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int dob_day
 * @property int dob_month
 * @property int dob_year
 * @property string signature
 * @property string website
 * @property string location
 * @property array following
 * @property array ignored
 * @property int avatar_crop_x
 * @property int avatar_crop_y
 * @property string about
 * @property array custom_fields_
 * @property array connected_accounts
 * @property int password_date
 *
 * GETTERS
 * @property bool|int age
 * @property array birthday
 * @property Set custom_fields
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserFieldValue[] CustomFields
 */
class UserProfile extends Entity
{
	public function isFollowing($user)
	{
		if ($user instanceof User)
		{
			$userId = $user->user_id;
		}
		else
		{
			$userId = $user;
		}

		return in_array($userId, $this->following);
	}

	protected function verifyLocation($location)
	{
		if ($this->getOption('admin_edit'))
		{
			return true;
		}

		if ($this->isUpdate() && $location == $this->getExistingValue('location'))
		{
			return true;
		}

		if ($this->getOption('location_required') && $location === '')
		{
			$this->error(\XF::phrase('please_enter_valid_location'), 'location');
			return false;
		}

		return true;
	}

	protected function verifyLongStringField($value, $key)
	{
		$maxLength = $this->getOption('max_long_string_length');
		if ($maxLength && utf8_strlen($value) > $maxLength)
		{
			$this->error(\XF::phrase('please_enter_message_with_no_more_than_x_characters', ['count' => $maxLength]), $key);
			return false;
		}

		return true;
	}

	public function setDob($day, $month, $year = 0)
	{
		$day = intval($day);
		$month = intval($month);
		$year = intval($year);

		if (!$day || !$month)
		{
			$this->dob_day = 0;
			$this->dob_month = 0;
			$this->dob_year = 0;
			return true;
		}

		if ($year && $year < 100)
		{
			$year += $year < 30 ? 2000 : 1900;
		}

		$testYear = $year ? $year : 2008; // leap year

		if ($testYear < 1900
			|| !checkdate($month, $day, $testYear)
			|| gmmktime(0, 0, 0, $month, $day, $testYear) > \XF::$time + 86400 // +1 day to be careful with TZs ahead of GMT
		)
		{
			$this->dob_day = 0;
			$this->dob_month = 0;
			$this->dob_year = 0;

			$this->error(\XF::phrase('please_enter_valid_date_of_birth'), 'dob');

			return false;
		}

		$this->dob_day = $day;
		$this->dob_month = $month;
		$this->dob_year = $year;
		return true;
	}

	public function calculateAge($year, $month, $day)
	{
		list($cYear, $cMonth, $cDay) = explode('-', $this->app()->language()->date(\XF::$time, 'Y-m-d'));
		$age = $cYear - $year;
		if ($cMonth < $month || ($cMonth == $month && $cDay < $day))
		{
			$age--;
		}

		return max(0, $age);
	}

	/**
	 * @param bool $bypassPrivacy
	 * @return bool|int
	 */
	public function getAge($bypassPrivacy = false)
	{
		if (empty($this->dob_year) || empty($this->dob_month) || empty($this->dob_day))
		{
			return false;
		}

		if ($this->dob_year && ($bypassPrivacy || ($this->User->Option->show_dob_date && $this->User->Option->show_dob_year)))
		{
			return $this->calculateAge($this->dob_year, $this->dob_month, $this->dob_day);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param bool $bypassPrivacy
	 *
	 * @return array
	 */
	public function getBirthday($bypassPrivacy = false)
	{
		if ($this->dob_day && ($bypassPrivacy || $this->User->Option->show_dob_date))
		{
			if ($this->dob_year && ($bypassPrivacy || $this->User->Option->show_dob_year))
			{
				return [
					'age' => $this->getAge($bypassPrivacy),
					'timeStamp' => new \DateTime("$this->dob_year-$this->dob_month-$this->dob_day"),
					'format' => 'absolute'
				];
			}
			else
			{
				return [
					'age' => false,
					'timeStamp' => new \DateTime("2000-$this->dob_month-$this->dob_day"),
					'format' => 'monthDay'
				];
			}
		}
		else
		{
			return [];
		}
	}

	/**
	 * @return Set
	 */
	public function getCustomFields()
	{
		$fieldDefinitions = $this->app()->container('customFields.users');

		return new Set($fieldDefinitions, $this);
	}

	public function getNewProfilePost()
	{
		$profilePost = $this->_em->create('XF:ProfilePost');
		$profilePost->profile_user_id = $this->user_id;

		return $profilePost;
	}

	public function rebuildUserFieldValuesCache()
	{
		$this->repository('XF:UserField')->rebuildUserFieldValuesCache($this->user_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_profile';
		$structure->shortName = 'XF:UserProfile';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true, 'changeLog' => false],
			'dob_day' => ['type' => self::UINT, 'max' => 31, 'default' => 0],
			'dob_month' => ['type' => self::UINT, 'max' => 12, 'default' => 0],
			'dob_year' => ['type' => self::UINT, 'max' => 2100, 'default' => 0],
			'signature' => ['type' => self::STR, 'maxLength' => 20000, 'default' => '',
				'verify' => 'verifyLongStringField',
				'censor' => true
			],
			'website' => ['type' => self::STR, 'default' => '',
				'censor' => true,
				'match' => 'url_empty'
			],
			'location' => ['type' => self::STR, 'maxLength' => 50, 'default' => '',
				'censor' => true
			],
			'following' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC],
				'changeLog' => false
			],
			'ignored' => ['type' => self::JSON_ARRAY, 'default' => [], 'changeLog' => false],
			'avatar_crop_x' => ['type' => self::UINT, 'default' => 0, 'changeLog' => false],
			'avatar_crop_y' => ['type' => self::UINT, 'default' => 0, 'changeLog' => false],
			'about' => ['type' => self::STR, 'maxLength' => 20000, 'default' => '',
				'verify' => 'verifyLongStringField',
				'censor' => true
			],
			'custom_fields' => ['type' => self::JSON_ARRAY, 'default' => [], 'changeLog' => 'customFields'],
			'connected_accounts' => ['type' => self::JSON_ARRAY, 'default' => [], 'changeLog' => false],
			'password_date' => ['type' => self::UINT, 'default' => 1, 'changeLog' => false]
		];
		$structure->behaviors = [
			'XF:ChangeLoggable' => ['contentType' => 'user'],
			'XF:CustomFieldsHolder' => ['valueTable' => 'xf_user_field_value']
		];
		$structure->getters = [
			'age' => true,
			'birthday' => true,
			'custom_fields' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'CustomFields' => [
				'entity' => 'XF:UserFieldValue',
				'type' => self::TO_MANY,
				'conditions' => 'user_id',
				'key' => 'field_id'
			]
		];

		$options = \XF::options();

		$structure->options = [
			'max_long_string_length' => !empty($options->messageMaxLength) ? $options->messageMaxLength : 10000,
			'location_required' => !empty($options->registrationSetup['requireLocation']),
			'admin_edit' => false
		];

		return $structure;
	}
}