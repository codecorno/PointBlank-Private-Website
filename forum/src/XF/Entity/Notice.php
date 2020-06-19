<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null notice_id
 * @property string title
 * @property string message
 * @property bool dismissible
 * @property bool active
 * @property int display_order
 * @property array user_criteria
 * @property array page_criteria
 * @property string display_image
 * @property string image_url
 * @property string visibility
 * @property string notice_type
 * @property string display_style
 * @property string css_class
 * @property int display_duration
 * @property int delay_duration
 * @property bool auto_dismiss
 */
class Notice extends Entity
{
	public function canDismissNotice(&$error = null, \XF\Entity\User $user = null)
	{
		$user = $user ?: \XF::visitor();

		if (!$user->user_id || !$this->dismissible)
		{
			$error = \XF::phraseDeferred('you_may_not_dismiss_this_notice');
			return false;
		}

		return true;
	}

	protected function verifyUserCriteria(&$criteria)
	{
		$userCriteria = $this->app()->criteria('XF:User', $criteria);
		$criteria = $userCriteria->getCriteria();
		return true;
	}

	protected function verifyPageCriteria(&$criteria)
	{
		$pageCriteria = $this->app()->criteria('XF:Page', $criteria);
		$criteria = $pageCriteria->getCriteria();
		return true;		
	}

	protected function _postSave()
	{
		$this->rebuildNoticeCache();
	}

	protected function _postDelete()
	{
		$this->db()->delete('xf_notice_dismissed', 'notice_id = ?', $this->notice_id);
		$this->rebuildNoticeCache();
	}

	protected function rebuildNoticeCache()
	{
		\XF::runOnce('noticeCacheRebuild', function()
		{
			$this->getNoticeRepo()->rebuildNoticeCache();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_notice';
		$structure->shortName = 'XF:Notice';
		$structure->primaryKey = 'notice_id';
		$structure->columns = [
			'notice_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 150,
				'required' => 'please_enter_valid_title'
			],
			'message' => ['type' => self::STR,
				'required' => 'please_enter_valid_message'
			],
			'dismissible' => ['type' => self::BOOL, 'default' => true],
			'active' => ['type' => self::BOOL, 'default' => true],
			'display_order' => ['type' => self::UINT, 'default' => 1],
			'user_criteria' => ['type' => self::JSON_ARRAY, 'default' => []],
			'page_criteria' => ['type' => self::JSON_ARRAY, 'default' => []],
			'display_image'	=> ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'avatar', 'image']
			],
			'image_url' => ['type' => self::STR, 'default' => '', 'maxLength' => 200],
			'visibility' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['','wide','medium','narrow']
			],
			'notice_type' => ['type' => self::STR, 'default' => 'block',
				'allowedValues' => ['block', 'scrolling', 'floating', 'bottom_fixer']
			],
			'display_style' => ['type' => self::STR, 'default' => 'primary',
				'allowedValues' => ['', 'primary', 'accent', 'dark', 'light', 'custom']],
			'css_class' => ['type' => self::STR, 'default' => '', 'maxLength' => 50],
			'display_duration' => ['type' => self::UINT, 'default' => 0],
			'delay_duration' => ['type' => self::UINT, 'default' => 0],
			'auto_dismiss' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Notice
	 */
	protected function getNoticeRepo()
	{
		return $this->repository('XF:Notice');
	}
}