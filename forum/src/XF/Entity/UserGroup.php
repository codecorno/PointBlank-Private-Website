<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null user_group_id
 * @property string title
 * @property int display_style_priority
 * @property string username_css
 * @property string user_title
 * @property string banner_css_class
 * @property string banner_text
 */
class UserGroup extends Entity
{
	protected function verifyUsernameCss(&$css)
	{
		$css = trim($css);
		if (!strlen($css))
		{
			return true;
		}

		$parser = new \Less_Parser();
		try
		{
			$parser->parse('.example { ' . $css . '}')->getCss();
		}
		catch (\Exception $e)
		{
			$this->error(\XF::phrase('please_enter_valid_user_name_css_rules'), 'username_css');
			return false;
		}

		return true;
	}

	protected function _postSave()
	{
		if ($this->isUpdate() && $this->isChanged('display_style_priority'))
		{
			$this->rebuildUserGroupDisplayPriority();
		}

		if ($this->isChanged('username_css') || $this->isChanged('user_title'))
		{
			$this->rebuildDisplayStyleCache();
		}

		if ($this->isChanged('banner_css_class') || $this->isChanged('banner_text') || $this->isChanged('display_style_priority'))
		{
			$this->rebuildUserBannerCache();
		}
	}

	protected function _preDelete()
	{
		if ($this->user_group_id <= 4)
		{
			$this->error(\XF::phrase('you_may_not_delete_important_default_user_groups'));
		}
	}

	protected function _postDelete()
	{
		$this->db()->delete('xf_permission_entry', 'user_group_id = ?', $this->user_group_id);
		$this->db()->delete('xf_permission_entry_content', 'user_group_id = ?', $this->user_group_id);

		$this->rebuildDisplayStyleCache();
		$this->rebuildUserBannerCache();

		$this->app()->jobManager()->enqueueUnique(
			'deleteUserGroup' . $this->user_group_id,
			'XF:UserGroupDelete',
			['user_group_id' => $this->user_group_id]
		);
	}

	protected function rebuildUserGroupDisplayPriority()
	{
		$repo = $this->getUserGroupRepo();

		\XF::runOnce('displayPriorityRebuild', function() use ($repo)
		{
			$repo->rebuildDisplayPriority(
				$this->get('user_group_id'),
				$this->getPreviousValue('display_style_priority'),
				$this->get('display_style_priority')
			);
		});
	}

	protected function rebuildDisplayStyleCache()
	{
		$repo = $this->getUserGroupRepo();

		\XF::runOnce('displayStyleCacheRebuild', function() use ($repo)
		{
			$repo->rebuildDisplayStyleCache();
		});
	}

	protected function rebuildUserBannerCache()
	{
		$repo = $this->getUserGroupRepo();

		\XF::runOnce('userBannerCacheRebuild', function() use ($repo)
		{
			$repo->rebuildUserBannerCache();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_group';
		$structure->shortName = 'XF:UserGroup';
		$structure->primaryKey = 'user_group_id';
		$structure->columns = [
			'user_group_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title'
			],
			'display_style_priority' => ['type' => self::UINT, 'default' => 0],
			'username_css' => ['type' => self::STR, 'default' => ''],
			'user_title' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'banner_css_class' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'banner_text' => ['type' => self::STR, 'maxLength' => 100, 'default' => '']
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\UserGroup
	 */
	protected function getUserGroupRepo()
	{
		return $this->_em->getRepository('XF:UserGroup');
	}
}