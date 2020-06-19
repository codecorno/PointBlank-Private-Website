<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int node_id
 * @property int discussion_count
 * @property int message_count
 * @property int last_post_id
 * @property int last_post_date
 * @property int last_post_user_id
 * @property string last_post_username
 * @property int last_thread_id
 * @property string last_thread_title
 * @property int last_thread_prefix_id
 * @property bool moderate_threads
 * @property bool moderate_replies
 * @property bool allow_posting
 * @property bool allow_poll
 * @property bool count_messages
 * @property bool find_new
 * @property bool require_prefix
 * @property string allowed_watch_notifications
 * @property array field_cache
 * @property array prefix_cache
 * @property array prompt_cache
 * @property int default_prefix_id
 * @property string default_sort_order
 * @property string default_sort_direction
 * @property int list_date_limit_days
 * @property int min_tags
 *
 * GETTERS
 * @property \XF\Draft draft_thread
 * @property \XF\Mvc\Entity\ArrayCollection|\XF\Entity\ThreadPrefix[] prefixes
 * @property \XF\Phrase thread_prompt
 * @property string|null node_name
 * @property string|null title
 * @property string|null description
 * @property int depth
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ForumRead[] Read
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ForumWatch[] Watch
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\Draft[] DraftThreads
 * @property \XF\Entity\Post LastPost
 * @property \XF\Entity\User LastPostUser
 * @property \XF\Entity\Thread LastThread
 * @property \XF\Entity\Node Node
 */
class Forum extends AbstractNode
{
	public function canCreateThread(&$error = null)
	{
		if (!$this->allow_posting)
		{
			$error = \XF::phraseDeferred('you_may_not_perform_this_action_because_forum_does_not_allow_posting');

			return false;
		}

		$visitor = \XF::visitor();

		return $visitor->hasNodePermission($this->node_id, 'postThread');
	}

	public function canCreatePoll(&$error = null)
	{
		return $this->allow_poll;
	}

	public function canViewDeletedThreads()
	{
		return \XF::visitor()->hasNodePermission($this->node_id, 'viewDeleted');
	}

	public function canViewModeratedThreads()
	{
		return \XF::visitor()->hasNodePermission($this->node_id, 'viewModerated');
	}

	public function canUploadAndManageAttachments()
	{
		$visitor = \XF::visitor();

		return ($visitor->user_id && $visitor->hasNodePermission($this->node_id, 'uploadAttachment'));
	}

	public function canUploadVideos()
	{
		$options = $this->app()->options();

		if (empty($options->allowVideoUploads['enabled']))
		{
			return false;
		}

		$visitor = \XF::visitor();

		return $visitor->hasNodePermission($this->node_id, 'uploadVideo');
	}

	public function canEditTags(Thread $thread = null, &$error = null)
	{
		if (!$this->app()->options()->enableTagging)
		{
			return false;
		}

		if ($thread)
		{
			if (!$thread->discussion_open && !$thread->canLockUnlock())
			{
				$error = \XF::phraseDeferred('you_may_not_perform_this_action_because_discussion_is_closed');

				return false;
			}
		}

		$visitor = \XF::visitor();

		// if no thread, assume the thread will be owned by this person
		if (!$thread || $thread->user_id == $visitor->user_id)
		{
			if ($visitor->hasNodePermission($this->node_id, 'tagOwnThread'))
			{
				return true;
			}
		}

		if (
			$visitor->hasNodePermission($this->node_id, 'tagAnyThread')
			|| $visitor->hasNodePermission($this->node_id, 'manageAnyTag')
		)
		{
			return true;
		}

		return false;
	}

	public function canWatch(&$error = null)
	{
		return \XF::visitor()->user_id ? true : false;
	}

	public function isUnread()
	{
		if (!$this->discussion_count)
		{
			return false;
		}

		$cutOff = \XF::$time - $this->app()->options()->readMarkingDataLifetime * 86400;
		if ($this->last_post_date < $cutOff)
		{
			return false;
		}

		$visitor = \XF::visitor();
		if ($visitor->user_id)
		{
			$read = $this->Read[$visitor->user_id];

			return (!$read || $read->forum_read_date < $this->last_post_date);
		}
		else
		{
			return true;
		}
	}

	/**
	 * @return \XF\Draft
	 */
	public function getDraftThread()
	{
		return \XF\Draft::createFromEntity($this, 'DraftThreads');
	}

	public function getUsablePrefixes(ThreadPrefix $forcePrefix = null)
	{
		$prefixes = $this->prefixes;

		$prefixes = $prefixes->filter(function ($prefix) use ($forcePrefix)
		{
			if ($forcePrefix && $forcePrefix->prefix_id == $prefix->prefix_id)
			{
				return true;
			}

			return $this->isPrefixUsable($prefix);
		});

		return $prefixes->groupBy('prefix_group_id');
	}

	public function getPrefixesGrouped()
	{
		return $this->prefixes->groupBy('prefix_group_id');
	}

	/**
	 * @return \XF\Mvc\Entity\ArrayCollection|\XF\Entity\ThreadPrefix[]
	 */
	public function getPrefixes()
	{
		if (!$this->prefix_cache)
		{
			return $this->_em->getEmptyCollection();
		}

		$prefixes = $this->finder('XF:ThreadPrefix')
			->where('prefix_id', $this->prefix_cache)
			->order('materialized_order')
			->fetch();

		return $prefixes;
	}

	public function isPrefixUsable($prefix, User $user = null)
	{
		if (!$this->isPrefixValid($prefix))
		{
			return false;
		}

		if (!($prefix instanceof ThreadPrefix))
		{
			$prefix = $this->em()->find('XF:ThreadPrefix', $prefix);
			if (!$prefix)
			{
				return false;
			}
		}

		return $prefix->isUsableByUser($user);
	}

	public function isPrefixValid($prefix)
	{
		if ($prefix instanceof ThreadPrefix)
		{
			$prefix = $prefix->prefix_id;
		}

		return (!$prefix || isset($this->prefix_cache[$prefix]));
	}

	public function getNodeListExtras()
	{
		if (\XF::visitor()->hasNodePermission($this->node_id, 'viewOthers'))
		{
			$output = [
				'discussion_count' => $this->discussion_count,
				'message_count'    => $this->message_count,
				'hasNew'           => $this->isUnread()
			];

			if ($this->last_post_date)
			{
				$output['last_post_id'] = $this->last_post_id;
				$output['last_post_date'] = $this->last_post_date;
				$output['last_post_user_id'] = $this->last_post_user_id;
				$output['last_post_username'] = $this->last_post_username;
				$output['last_thread_id'] = $this->last_thread_id;
				$output['last_thread_title'] = $this->app()->stringFormatter()->censorText($this->last_thread_title);
				$output['last_thread_prefix_id'] = $this->last_thread_prefix_id;
				$output['LastPostUser'] = $this->LastPostUser;
				$output['LastThread'] = $this->LastThread;
			}

			return $output;
		}
		else
		{
			return ['privateInfo' => true];
		}
	}

	public function getNodeTemplateRenderer($depth)
	{
		return [
			'template' => 'node_list_forum',
			'macro'    => $depth <= 2 ? 'depth' . $depth : 'depthN'
		];
	}

	public function getNewContentState(Thread $thread = null)
	{
		$visitor = \XF::visitor();

		if ($visitor->user_id && $visitor->hasNodePermission($this->node_id, 'approveUnapprove'))
		{
			return 'visible';
		}

		if (!$visitor->hasPermission('general', 'submitWithoutApproval'))
		{
			return 'moderated';
		}

		if ($thread)
		{
			return $this->moderate_replies ? 'moderated' : 'visible';
		}
		else
		{
			return $this->moderate_threads ? 'moderated' : 'visible';
		}
	}

	public function getNewThread()
	{
		$thread = $this->_em->create('XF:Thread');
		$thread->node_id = $this->node_id;

		return $thread;
	}

	public function threadAdded(Thread $thread)
	{
		if ($thread->discussion_type == 'redirect')
		{
			return;
		}

		$this->discussion_count++;
		$this->message_count += 1 + $thread->reply_count;

		if ($thread->last_post_date >= $this->last_post_date)
		{
			$this->last_post_date = $thread->last_post_date;
			$this->last_post_id = $thread->last_post_id;
			$this->last_post_user_id = $thread->last_post_user_id;
			$this->last_post_username = $thread->last_post_username;
			$this->last_thread_id = $thread->thread_id;
			$this->last_thread_title = $thread->title;
			$this->last_thread_prefix_id = $thread->prefix_id;
		}
	}

	public function threadDataChanged(Thread $thread)
	{
		$isRedirect = $thread->discussion_type == 'redirect';
		$wasRedirect = $thread->getExistingValue('discussion_type') == 'redirect';

		if ($isRedirect && !$wasRedirect)
		{
			// this is like the thread being deleted for counter purposes
			$this->threadRemoved($thread);
		}
		else if (!$isRedirect && $wasRedirect)
		{
			// like being added
			$this->threadAdded($thread);
		}
		else if ($isRedirect)
		{
			return;
		}

		$this->message_count += $thread->reply_count - $thread->getExistingValue('reply_count');

		if ($thread->last_post_date >= $this->last_post_date)
		{
			$this->last_post_date = $thread->last_post_date;
			$this->last_post_id = $thread->last_post_id;
			$this->last_post_user_id = $thread->last_post_user_id;
			$this->last_post_username = $thread->last_post_username;
			$this->last_thread_id = $thread->thread_id;
			$this->last_thread_title = $thread->title;
			$this->last_thread_prefix_id = $thread->prefix_id;
		}
		else if ($thread->getExistingValue('last_post_id') == $this->last_post_id)
		{
			$this->rebuildLastPost();
		}
	}

	public function threadRemoved(Thread $thread)
	{
		if ($thread->discussion_type == 'redirect')
		{
			// if this was changed, it used to count so we need to continue
			if (!$thread->isChanged('discussion_type'))
			{
				return;
			}
		}

		$this->discussion_count--;
		$this->message_count -= 1 + $thread->reply_count;

		if ($thread->last_post_id == $this->last_post_id)
		{
			$this->rebuildLastPost();
		}
	}

	public function rebuildCounters()
	{
		$counters = $this->db()->fetchRow("
			SELECT COUNT(*) AS discussion_count,
				COUNT(*) + COALESCE(SUM(reply_count), 0) AS message_count
			FROM xf_thread
			WHERE node_id = ?
				AND discussion_state = 'visible'
				AND discussion_type <> 'redirect'
		", $this->node_id);

		$this->discussion_count = $counters['discussion_count'];
		$this->message_count = $counters['message_count'];

		$this->rebuildLastPost();
	}

	public function rebuildLastPost()
	{
		$thread = $this->db()->fetchRow("
			SELECT *
			FROM xf_thread
			WHERE node_id = ?
				AND discussion_state = 'visible'
				AND discussion_type <> 'redirect'
			ORDER BY last_post_date DESC
			LIMIT 1
		", $this->node_id);
		if ($thread)
		{
			$this->last_post_id = $thread['last_post_id'];
			$this->last_post_date = $thread['last_post_date'];
			$this->last_post_user_id = $thread['last_post_user_id'];
			$this->last_post_username = $thread['last_post_username'];
			$this->last_thread_id = $thread['thread_id'];
			$this->last_thread_title = $thread['title'];
			$this->last_thread_prefix_id = $thread['prefix_id'];
		}
		else
		{
			$this->last_post_id = 0;
			$this->last_post_date = 0;
			$this->last_post_user_id = 0;
			$this->last_post_username = '';
			$this->last_thread_id = 0;
			$this->last_thread_title = '';
			$this->last_thread_prefix_id = 0;
		}
	}

	protected function _postDelete()
	{
		$this->db()->delete('xf_forum_prefix', 'node_id = ?', $this->node_id);
		$this->db()->delete('xf_forum_watch', 'node_id = ?', $this->node_id);

		if ($this->getOption('delete_threads'))
		{
			$this->app()->jobManager()->enqueueUnique('forumDelete' . $this->node_id, 'XF:ForumDelete', [
				'node_id' => $this->node_id
			]);
		}
	}

	public function getNodeTypeApiData($verbosity = self::VERBOSITY_NORMAL, array $options = [])
	{
		$result = parent::getNodeTypeApiData();

		if (\XF::visitor()->hasNodePermission($this->node_id, 'viewOthers'))
		{
			$result->includeExtra([
				'discussion_count' => $this->discussion_count,
				'message_count' => $this->message_count,
				'last_post_id' => $this->last_post_id,
				'last_post_date' => $this->last_post_date,
				'last_post_username' => $this->last_post_username,
				'last_thread_id' => $this->last_thread_id,
				'last_thread_title' => $this->app()->stringFormatter()->censorText($this->last_thread_title),
				'last_thread_prefix_id' => $this->last_thread_prefix_id,
			]);
		}

		if ($verbosity > self::VERBOSITY_NORMAL)
		{
			$result->prefixes = $this->prefixes->toApiResults();

			$fields = [];
			if ($this->field_cache)
			{
				$fieldEntities = $this->repository('XF:ThreadField')->findFieldsForList()
					->whereIds($this->field_cache)
					->fetch();
				$fields = $fieldEntities->toApiResults();
			}

			$result->custom_fields = $fields;
		}

		$result->can_create_thread = $this->canCreateThread();
		$result->can_upload_attachment = $this->canUploadAndManageAttachments();

		return $result;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_forum';
		$structure->shortName = 'XF:Forum';
		$structure->primaryKey = 'node_id';
		$structure->columns = [
			'node_id'                     => ['type' => self::UINT, 'required' => true],
			'discussion_count'            => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'message_count'               => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'last_post_id'                => ['type' => self::UINT, 'default' => 0],
			'last_post_date'              => ['type' => self::UINT, 'default' => 0],
			'last_post_user_id'           => ['type' => self::UINT, 'default' => 0],
			'last_post_username'          => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'last_thread_id'              => ['type' => self::UINT, 'default' => 0],
			'last_thread_title'           => ['type' => self::STR, 'maxLength' => 150, 'default' => ''],
			'last_thread_prefix_id'       => ['type' => self::UINT, 'default' => 0],
			'moderate_threads'            => ['type' => self::BOOL, 'default' => false],
			'moderate_replies'            => ['type' => self::BOOL, 'default' => false],
			'allow_posting'               => ['type' => self::BOOL, 'default' => true, 'api' => true],
			'allow_poll'                  => ['type' => self::BOOL, 'default' => true, 'api' => true],
			'count_messages'              => ['type' => self::BOOL, 'default' => true],
			'find_new'                    => ['type' => self::BOOL, 'default' => true],
			'require_prefix'              => ['type' => self::BOOL, 'default' => false, 'api' => true],
			'allowed_watch_notifications' => ['type' => self::STR, 'default' => 'all',
				'allowedValues' => ['all', 'thread', 'none']
			],
			'field_cache'                 => ['type' => self::JSON_ARRAY, 'default' => []],
			'prefix_cache'                => ['type' => self::JSON_ARRAY, 'default' => []],
			'prompt_cache'                => ['type' => self::JSON_ARRAY, 'default' => []],
			'default_prefix_id'           => ['type' => self::UINT, 'default' => 0],
			'default_sort_order'          => ['type' => self::STR, 'default' => 'last_post_date',
				'allowedValues' => ['title', 'post_date', 'reply_count', 'view_count', 'last_post_date']
			],
			'default_sort_direction'      => ['type' => self::STR, 'default' => 'desc',
				'allowedValues' => ['asc', 'desc']
			],
			'list_date_limit_days'        => ['type' => self::UINT, 'default' => 0, 'max' => 3650],
			'min_tags'                    => ['type' => self::UINT, 'default' => 0, 'max' => 100, 'api' => true],
		];
		$structure->getters = [
			'draft_thread'  => true,
			'prefixes'      => true,
			'thread_prompt' => true
		];
		$structure->relations = [
			'Read'   => [
				'entity'     => 'XF:ForumRead',
				'type'       => self::TO_MANY,
				'conditions' => 'node_id',
				'key'        => 'user_id'
			],
			'Watch'  => [
				'entity'     => 'XF:ForumWatch',
				'type'       => self::TO_MANY,
				'conditions' => 'node_id',
				'key'        => 'user_id'
			],
			'DraftThreads' => [
				'entity'     => 'XF:Draft',
				'type'       => self::TO_MANY,
				'conditions' => [
					['draft_key', '=', 'forum-', '$node_id']
				],
				'key'        => 'user_id'
			],
			'LastPost' => [
				'entity' => 'XF:Post',
				'type' => self::TO_ONE,
				'conditions' => [['post_id', '=', '$last_post_id']],
				'primary' => true
			],
			'LastPostUser' => [
				'entity'     => 'XF:User',
				'type'       => self::TO_ONE,
				'conditions' => [['user_id', '=', '$last_post_user_id']],
				'primary'    => true
			],
			'LastThread' => [
				'entity'     => 'XF:Thread',
				'type'       => self::TO_ONE,
				'conditions' => [['thread_id', '=', '$last_thread_id']],
				'primary'    => true
			]
		];

		$structure->options = [
			'delete_threads' => true
		];

		static::addDefaultNodeElements($structure);

		return $structure;
	}

	public static function getListedWith()
	{
		$visitor = \XF::visitor();
		$with = ['LastPostUser', 'LastThread'];

		if ($visitor->user_id)
		{
			$with[] = "Read|{$visitor->user_id}";
			$with[] = "LastThread.Read|{$visitor->user_id}";
		}

		return $with;
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getThreadPrompt()
	{
		static $phraseName; // always return the same phrase for the same forum instance

		if (!$phraseName)
		{
			if ($this->prompt_cache)
			{
				$phraseName = 'thread_prompt.' . array_rand($this->prompt_cache);
			}
			else
			{
				$phraseName = 'thread_prompt.default';
			}
		}

		return \XF::phrase($phraseName);
	}
}