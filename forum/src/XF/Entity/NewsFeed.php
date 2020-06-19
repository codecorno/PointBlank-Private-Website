<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null news_feed_id
 * @property int user_id
 * @property string username
 * @property string content_type
 * @property int content_id
 * @property string action
 * @property int event_date
 * @property array extra_data
 *
 * GETTERS
 * @property Entity|null Content
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class NewsFeed extends Entity
{
	public function canView(&$error = null)
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return ($handler->canViewContent($content, $error) && $handler->canViewEntry($this, $content, $error));
		}
		else
		{
			return false;
		}
	}

	public function isVisible(&$error = null)
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->contentIsVisible($content, $error);
		}
		else
		{
			return true;
		}
	}

	public function getHandler()
	{
		return $this->getNewsFeedRepo()->getNewsFeedHandler($this->content_type);
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

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_news_feed';
		$structure->shortName = 'XF:NewsFeed';
		$structure->primaryKey = 'news_feed_id';
		$structure->columns = [
			'news_feed_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'username' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'content_type' => ['type' => self::STR, 'maxLength' => 25],
			'content_id' => ['type' => self::UINT],
			'action' => ['type' => self::STR, 'maxLength' => 25],
			'event_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'extra_data' => ['type' => self::JSON_ARRAY, 'default' => []]
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
			]
		];
		$structure->defaultWith = 'User';

		return $structure;
	}

	/**
	 * @return \XF\Repository\NewsFeed
	 */
	protected function getNewsFeedRepo()
	{
		return $this->repository('XF:NewsFeed');
	}
}