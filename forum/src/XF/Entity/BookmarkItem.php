<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null bookmark_id
 * @property int user_id
 * @property string content_type
 * @property int content_id
 * @property int bookmark_date
 * @property string message
 * @property array labels
 *
 * GETTERS
 * @property BookmarkTrait|Entity|null Content
 * @property mixed content_title
 * @property mixed content_link
 * @property mixed content_user
 * @property mixed edit_link
 * @property mixed delete_link
 * @property mixed has_custom_icon
 * @property mixed label_ids
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class BookmarkItem extends Entity
{
	public function canView(&$error = null)
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->canViewContent($content, $error);
		}
		else
		{
			return false;
		}
	}

	/**
	 * @return \XF\Bookmark\AbstractHandler|null
	 */
	public function getHandler()
	{
		return $this->getBookmarkRepo()->getBookmarkHandler($this->content_type);
	}

	/**
	 * @return BookmarkTrait|Entity|null
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

	public function getContentTitle()
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->getContentTitle($content);
		}
		else
		{
			return '';
		}
	}

	public function getContentLink()
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->getContentLink($content);
		}
		else
		{
			return '';
		}
	}

	public function getContentUser()
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->getContentUser($content);
		}
		else
		{
			return null;
		}
	}

	public function getEditLink()
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->getEditLink($content);
		}
		else
		{
			return '';
		}
	}

	public function getDeleteLink()
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->getDeleteLink($content);
		}
		else
		{
			return '';
		}
	}

	public function hasCustomIcon()
	{
		$handler = $this->getHandler();
		return ($handler ? boolval($handler->getCustomIconTemplateName()) : false);
	}

	public function getLabelIds()
	{
		return $this->db()->fetchAllColumn('
			SELECT label_id
			FROM xf_bookmark_label_use
			WHERE bookmark_id = ?
		', $this->bookmark_id);
	}

	public function renderCustomIcon()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->renderCustomIcon($this) : '';
	}

	public function renderMessageFallback()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->renderMessageFallback($this) : '';
	}

	protected function _preSave()
	{
		if ($this->isInsert())
		{
			$exists = $this->db()->fetchOne('
				SELECT bookmark_id
				FROM xf_bookmark_item
				WHERE user_id = ?
				AND content_type = ?
				AND content_id = ?
			', [$this->user_id, $this->content_type, $this->content_id]);

			if (boolval($exists))
			{
				$this->error(\XF::phrase('you_have_already_bookmarked_this_content'));
			}
		}
	}

	protected function _postDelete()
	{
		$labelIds = $this->label_ids;
		if ($labelIds)
		{
			$this->db()->delete('xf_bookmark_label_use', 'bookmark_id = ?', $this->bookmark_id);
			$this->getBookmarkRepo()->recalculateLabelUsageCache($labelIds);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_bookmark_item';
		$structure->shortName = 'XF:BookmarkItem';
		$structure->contentType = 'bookmark_item';
		$structure->primaryKey = 'bookmark_id';
		$structure->columns = [
			'bookmark_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'bookmark_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'message' => ['type' => self::STR, 'default' => '', 'maxLength' => 280],
			'labels' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [
			'Content' => true,
			'content_title' => true,
			'content_link' => true,
			'content_user' => true,
			'edit_link' => true,
			'delete_link' => true,
			'has_custom_icon' => ['getter' => 'hasCustomIcon'],
			'label_ids' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Mvc\Entity\Repository|\XF\Repository\Bookmark
	 */
	protected function getBookmarkRepo()
	{
		return $this->repository('XF:Bookmark');
	}
}