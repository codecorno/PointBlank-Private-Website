<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null alert_id
 * @property int alerted_user_id
 * @property int user_id
 * @property string username
 * @property string content_type
 * @property int content_id
 * @property string action
 * @property int event_date
 * @property int view_date
 * @property array extra_data
 * @property string depends_on_addon_id
 *
 * GETTERS
 * @property Entity|null Content
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User Receiver
 * @property \XF\Entity\AddOn AddOn
 */
class UserAlert extends Entity
{
	public function canView(&$error = null)
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->canViewContent($content, $error) && $handler->canViewAlert($this, $error);
		}
		else
		{
			return false;
		}
	}

	public function getHandler()
	{
		return $this->getAlertRepo()->getAlertHandler($this->content_type);
	}

	/**
	 * @return Entity|null
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	public function setContent(Entity $content = null)
	{
		$this->_getterCache['Content'] = $content;
	}

	public function render()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->render($this) : '';
	}

	public function isAlertRenderable()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->isAlertRenderable($this) : false;
	}

	public function isUnviewed()
	{
		return !$this->view_date;
	}

	public function isRecentlyViewed()
	{
		return ($this->view_date && $this->view_date >= \XF::$time - 900);
	}

	protected function _postSave()
	{
		if ($this->isChanged('view_date'))
		{
			if (!$this->view_date)
			{
				$this->db()->query("
					UPDATE IGNORE xf_user
					SET alerts_unread = alerts_unread + 1
					WHERE user_id = ?
				", $this->alerted_user_id);
			}
			else
			{
				$this->db()->query("
					UPDATE xf_user
					SET alerts_unread = GREATEST(0, CAST(alerts_unread AS SIGNED) - 1)
					WHERE user_id = ?
				", $this->alerted_user_id);
			}
		}
	}

	protected function _postDelete()
	{
		if (!$this->view_date)
		{
			$this->db()->query("
				UPDATE xf_user
				SET alerts_unread = GREATEST(0, CAST(alerts_unread AS SIGNED) - 1)
				WHERE user_id = ?
			", $this->alerted_user_id);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_alert';
		$structure->shortName = 'XF:UserAlert';
		$structure->primaryKey = 'alert_id';
		$structure->columns = [
			'alert_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'alerted_user_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'username' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'action' => ['type' => self::STR, 'maxLength' => 30, 'required' => true],
			'event_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'view_date' => ['type' => self::UINT, 'default' => 0],
			'extra_data' => ['type' => self::JSON_ARRAY, 'default' => []],
			'depends_on_addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->getters = [
			'Content' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Receiver' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' =>[['user_id', '=', '$alerted_user_id']],
				'primary' => true
			],
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => [['addon_id', '=', '$depends_on_addon_id']],
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\UserAlert
	 */
	protected function getAlertRepo()
	{
		return $this->repository('XF:UserAlert');
	}
}