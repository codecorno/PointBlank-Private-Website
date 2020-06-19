<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string unique_key
 * @property string ip
 * @property int view_date
 * @property string controller_name
 * @property string controller_action
 * @property string view_state
 * @property string params_
 * @property string robot_key
 *
 * GETTERS
 * @property array params
 * @property string|null robot_link
 * @property string|null robot_title
 * @property \XF\Phrase|null description
 * @property string|null item_title
 * @property string|null item_url
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class SessionActivity extends Entity
{
	/**
	 * @return array
	 */
	public function getParams()
	{
		$params = $this->params_;
		if ($params)
		{
			parse_str($params, $output);
			return $output;
		}
		else
		{
			return [];
		}
	}

	public function pluckParam($key)
	{
		$params = $this->get('params');
		return isset($params[$key]) ? $params[$key] : null;
	}

	/**
	 * @return string|null
	 */
	public function getRobotLink()
	{
		if ($this->robot_key)
		{
			$robot = $this->app()->data('XF:Robot')->getRobotInfo($this->robot_key);
			if ($robot && isset($robot['link']))
			{
				return $robot['link'];
			}
		}

		return null;
	}

	/**
	 * @return string|null
	 */
	public function getRobotTitle()
	{
		if ($this->robot_key)
		{
			$robot = $this->app()->data('XF:Robot')->getRobotInfo($this->robot_key);
			if ($robot)
			{
				return $robot['title'];
			}
		}

		return null;
	}

	/**
	 * @return \XF\Phrase|null
	 */
	public function getDescription()
	{
		$this->applyActivityDetails();
		return $this->_getterCache['description'];
	}

	/**
	 * @return string|null
	 */
	public function getItemTitle()
	{
		$this->applyActivityDetails();
		return $this->_getterCache['item_title'];
	}

	/**
	 * @return string|null
	 */
	public function getItemUrl()
	{
		$this->applyActivityDetails();
		return $this->_getterCache['item_url'];
	}

	public function setItemDetails($description, $title = false, $url = false)
	{
		$this->_getterCache['description'] = $description;
		$this->_getterCache['item_title'] = $title;
		$this->_getterCache['item_url'] = $url;
	}

	protected function applyActivityDetails()
	{
		/** @var \XF\Repository\SessionActivity $repo */
		$repo = $this->repository('XF:SessionActivity');
		$repo->applyActivityDetails($this);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_session_activity';
		$structure->shortName = 'XF:SessionActivity';
		$structure->primaryKey = ['user_id', 'unique_key'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'unique_key' => ['type' => self::STR, 'maxLength' => 16, 'required' => true],
			'ip' => ['type' => self::BINARY, 'maxLength' => 16, 'required' => true],
			'view_date' => ['type' => self::UINT, 'required' => true],
			'controller_name' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'controller_action' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'view_state' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['valid', 'error']
			],
			'params' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'robot_key' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
		];
		$structure->getters = [
			'params' => true,
			'robot_link' => true,
			'robot_title' => true,
			'description' => true,
			'item_title' => true,
			'item_url' => true
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