<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null feed_id
 * @property string title
 * @property string url
 * @property int frequency
 * @property int node_id
 * @property int user_id
 * @property int prefix_id
 * @property string title_template
 * @property string message_template
 * @property bool discussion_visible
 * @property bool discussion_open
 * @property bool discussion_sticky
 * @property int last_fetch
 * @property bool active
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\Forum Forum
 */
class Feed extends Entity
{
	public function getEntryTitle(array $entry)
	{
		if (!$this->title_template)
		{
			$title = $entry['title'];
		}
		else
		{
			$title = $this->replaceTokens($this->title_template, $entry);
		}

		return $title;
	}

	public function getEntryMessage(array $entry)
	{
		if (!$this->message_template)
		{
			$message = $entry['content'];
		}
		else
		{
			$message = $this->replaceTokens($this->message_template, $entry);
		}

		$message = trim($message);
		if ($message === '')
		{
			$message = '[url]' . $entry['link'] . '[/url]';
		}

		return $message;
	}

	/**
	 * Searches the given template string for {token} and replaces it with $entry[token]
	 *
	 * @param string $template
	 * @param array $entry
	 */
	protected function replaceTokens($template, array $entry)
	{
		if (preg_match_all('/\{([a-z0-9_]+)\}/i', $template, $matches))
		{
			foreach ($matches[1] AS $token)
			{
				if (isset($entry[$token]))
				{
					$template = str_replace('{' . $token . '}', $entry[$token], $template);
				}
			}
		}

		return $template;
	}

	protected function verifyNodeId(&$nodeId)
	{
		$forum = $this->_em->find('XF:Forum', $nodeId);
		if (!$forum)
		{
			$this->error(\XF::phrase('please_select_valid_forum'), 'node_id');
			return false;
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->url
			&& (!$this->title
				|| ($this->isChanged('url') && !$this->isChanged('title'))
			)
		)
		{
			/** @var \XF\Service\Feed\Reader $reader */
			$reader = $this->app()->service('XF:Feed\Reader', $this->url);
			$title = $reader->getTitle();

			$this->title = $title ?: $this->url;
		}
	}

	protected function _postDelete()
	{
		$this->db()->delete('xf_feed_log', 'feed_id = ?', $this->feed_id);
	}

	protected function _setupDefaults()
	{
		$this->frequency = 1800;
		$this->message_template = '{content}' . "\n\n" . '[url="{link}"]' . \XF::phrase('continue_reading') . '[/url]';
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_feed';
		$structure->shortName = 'XF:Feed';
		$structure->primaryKey = 'feed_id';
		$structure->columns = [
			'feed_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 250, 'default' => ''],
			'url' => ['type' => self::STR, 'maxLength' => 2083, 'required' => true,
				'match' => 'url'
			],
			'frequency' => ['type' => self::UINT, 'required' => true],
			'node_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'prefix_id' => ['type' => self::UINT, 'default' => 0],
			'title_template' => ['type' => self::STR, 'maxLength' => 250, 'default' => ''],
			'message_template' => ['type' => self::STR,
				'required' => 'please_enter_message_template'
			],
			'discussion_visible' => ['type' => self::BOOL, 'default' => true],
			'discussion_open' => ['type' => self::BOOL, 'default' => true],
			'discussion_sticky' => ['type' => self::BOOL, 'default' => false],
			'last_fetch' => ['type' => self::UINT, 'default' => 0],
			'active' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Forum' => [
				'entity' => 'XF:Forum',
				'type' => self::TO_ONE,
				'conditions' => 'node_id',
				'primary' => true
			]
		];

		return $structure;
	}
}