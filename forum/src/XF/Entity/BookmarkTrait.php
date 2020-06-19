<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int bookmark_count
 */
trait BookmarkTrait
{
	abstract protected function canBookmarkContent(&$error = null);

	public function canBookmark(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if (!$this->canBookmarkContent($error))
		{
			return false;
		}

		return (
			$visitor->hasPermission('bookmark', 'view')
			&& $visitor->hasPermission('bookmark', 'create')
		);
	}

	public function isBookmarked()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		return (isset($this->Bookmarks[$visitor->user_id]) ? true : false);
	}

	/**
	 * @return null|BookmarkItem
	 */
	public function getBookmark()
	{
		if (!$this->isBookmarked())
		{
			return null;
		}

		return $this->Bookmarks[\XF::visitor()->user_id];
	}

	/**
	 * @return Entity|BookmarkItem
	 */
	public function getNewBookmark()
	{
		$bookmark = $this->em()->create('XF:BookmarkItem');
		$bookmark->content_type = $this->getEntityContentType();
		$bookmark->content_id = $this->getEntityId();

		return $bookmark;
	}

	protected function _postSaveBookmarks() {}

	protected function _postDeleteBookmarks()
	{
		$this->getBookmarkRepo()->fastDeleteBookmarksForContent(
			$this->getEntityContentType(),
			$this->getEntityId()
		);
	}

	public static function addBookmarkableStructureElements(Structure $structure)
	{
		$structure->relations['Bookmarks'] = [
			'entity' => 'XF:BookmarkItem',
			'type' => self::TO_MANY,
			'conditions' => [
				['content_type', '=', $structure->contentType],
				['content_id', '=', '$' . $structure->primaryKey]
			],
			'key' => 'user_id',
			'order' => 'bookmark_date'
		];
	}

	/**
	 * @return \XF\Mvc\Entity\Repository|\XF\Repository\Bookmark
	 */
	protected function getBookmarkRepo()
	{
		return $this->repository('XF:Bookmark');
	}
}