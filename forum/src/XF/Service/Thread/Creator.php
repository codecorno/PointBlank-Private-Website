<?php

namespace XF\Service\Thread;

use XF\Entity\Forum;
use XF\Entity\Post;
use XF\Entity\Thread;
use XF\Entity\User;

class Creator extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var Forum
	 */
	protected $forum;

	/**
	 * @var Thread
	 */
	protected $thread;

	/**
	 * @var Post
	 */
	protected $post;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var \XF\Service\Post\Preparer
	 */
	protected $postPreparer;

	/**
	 * @var \XF\Service\Tag\Changer
	 */
	protected $tagChanger;

	/** @var  \XF\Service\Poll\Creator|null */
	protected $pollCreator;

	protected $performValidations = true;

	public function __construct(\XF\App $app, Forum $forum)
	{
		parent::__construct($app);
		$this->forum = $forum;
		$this->setupDefaults();
	}

	protected function setupDefaults()
	{
		$this->thread = $this->forum->getNewThread();
		$this->post = $this->thread->getNewPost();

		$this->postPreparer = $this->service('XF:Post\Preparer', $this->post);

		$this->thread->addCascadedSave($this->post);
		$this->post->hydrateRelation('Thread', $this->thread);

		$this->tagChanger = $this->service('XF:Tag\Changer', 'thread', $this->forum);

		$user = \XF::visitor();
		$this->setUser($user);

		$this->thread->discussion_state = $this->forum->getNewContentState();
		$this->post->message_state = 'visible';
	}

	public function getForum()
	{
		return $this->forum;
	}

	public function getThread()
	{
		return $this->thread;
	}

	public function getPost()
	{
		return $this->post;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getPostPreparer()
	{
		return $this->postPreparer;
	}

	protected function setUser(\XF\Entity\User $user)
	{
		$this->user = $user;

		$this->thread->user_id = $user->user_id;
		$this->thread->username = $user->username;

		$this->post->user_id = $user->user_id;
		$this->post->username = $user->username;
	}

	public function setPerformValidations($perform)
	{
		$this->performValidations = (bool)$perform;
	}

	public function getPerformValidations()
	{
		return $this->performValidations;
	}

	public function setIsAutomated()
	{
		$this->logIp(false);
		$this->setPerformValidations(false);
	}

	public function setPollCreator(\XF\Service\Poll\Creator $creator = null)
	{
		$this->pollCreator = $creator;
	}

	public function getPollCreator()
	{
		return $this->pollCreator;
	}

	public function logIp($logIp)
	{
		$this->postPreparer->logIp($logIp);
	}

	public function setContent($title, $message, $format = true)
	{
		$this->thread->set('title', $title,
			['forceConstraint' => $this->performValidations ? false : true]
		);

		return $this->postPreparer->setMessage($message, $format, $this->performValidations);
	}

	public function setPrefix($prefixId)
	{
		$this->thread->prefix_id = $prefixId;
	}

	public function setTags($tags)
	{
		if ($this->tagChanger->canEdit())
		{
			$this->tagChanger->setEditableTags($tags);
		}
	}

	public function setAttachmentHash($hash)
	{
		$this->postPreparer->setAttachmentHash($hash);
	}

	public function setDiscussionOpen($discussionOpen)
	{
		$this->thread->discussion_open = $discussionOpen;
	}

	public function setDiscussionState($discussionState)
	{
		$this->thread->discussion_state = $discussionState;
	}

	public function setSticky($sticky)
	{
		$this->thread->sticky = $sticky;
	}

	public function setCustomFields(array $customFields)
	{
		/** @var \XF\CustomField\Set $fieldSet */
		$fieldSet = $this->thread->custom_fields;
		$fieldDefinition = $fieldSet->getDefinitionSet()
			->filterEditable($fieldSet, 'user')
			->filterOnly($this->forum->field_cache);

		$customFieldsShown = array_keys($fieldDefinition->getFieldDefinitions());

		if ($customFieldsShown)
		{
			$fieldSet->bulkSet($customFields, $customFieldsShown);
		}
	}

	public function checkForSpam()
	{
		if ($this->thread->discussion_state == 'visible' && $this->user->isSpamCheckRequired())
		{
			$this->postPreparer->checkForSpam();
		}
	}

	protected function finalSetup()
	{
		$date = time();

		$this->thread->post_date = $date;
		$this->thread->last_post_date = $date;
		$this->thread->last_post_user_id = $this->thread->user_id;
		$this->thread->last_post_username = $this->thread->username;

		$this->post->post_date = $date;
		$this->post->position = 0;
	}

	protected function _validate()
	{
		$this->finalSetup();

		$thread = $this->thread;

		if (!$thread->user_id)
		{
			/** @var \XF\Validator\Username $validator */
			$validator = $this->app->validator('Username');
			$thread->username = $validator->coerceValue($thread->username);
			$this->post->username = $thread->username;

			if ($this->performValidations && !$validator->isValid($thread->username, $error))
			{
				return [
					$validator->getPrintableErrorValue($error)
				];
			}
		}

		$thread->preSave();
		$errors = $thread->getErrors();

		if ($this->performValidations)
		{
			if (!$thread->prefix_id
				&& $this->forum->require_prefix
				&& $this->forum->getUsablePrefixes()
			)
			{
				$errors[] = \XF::phraseDeferred('please_select_a_prefix');
			}

			if ($this->tagChanger->canEdit())
			{
				$tagErrors = $this->tagChanger->getErrors();
				if ($tagErrors)
				{
					$errors = array_merge($errors, $tagErrors);
				}
			}
		}

		if ($this->pollCreator)
		{
			if (!$this->pollCreator->validate($pollErrors))
			{
				$errors = array_merge($errors, $pollErrors);
			}
		}

		return $errors;
	}

	protected function _save()
	{
		$forum = $this->forum;
		$thread = $this->thread;
		$post = $this->post;

		$db = $this->db();
		$db->beginTransaction();

		$thread->save(true, false);
		// post will also be saved now

		$thread->fastUpdate([
			'first_post_id' => $post->post_id,
			'last_post_id' => $post->post_id
		]);

		if ($thread->last_post_date == $forum->last_post_date)
		{
			$forum->fastUpdate([
				'last_post_id' => $post->post_id,
				'last_thread_id' => $post->thread_id
			]);
		}

		$this->postPreparer->afterInsert();

		if ($this->tagChanger->canEdit())
		{
			$this->tagChanger
				->setContentId($thread->thread_id, true)
				->save($this->performValidations);
		}

		if ($this->pollCreator)
		{
			$this->pollCreator->save();
		}

		$db->commit();

		return $thread;
	}

	public function sendNotifications()
	{
		if ($this->thread->isVisible())
		{
			/** @var \XF\Service\Post\Notifier $notifier */
			$notifier = $this->service('XF:Post\Notifier', $this->post, 'thread');
			$notifier->setMentionedUserIds($this->postPreparer->getMentionedUserIds());
			$notifier->setQuotedUserIds($this->postPreparer->getQuotedUserIds());
			$notifier->notifyAndEnqueue(3);
		}
	}
}