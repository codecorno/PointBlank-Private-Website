<?php

namespace XF\Entity;

use XF\BbCode\RenderableContentInterface;
use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null profile_post_id
 * @property int profile_user_id
 * @property int user_id
 * @property string username
 * @property int post_date
 * @property string message
 * @property int ip_id
 * @property string message_state
 * @property int attach_count
 * @property int warning_id
 * @property string warning_message
 * @property int comment_count
 * @property int first_comment_date
 * @property int last_comment_date
 * @property array latest_comment_ids
 * @property array|null embed_metadata
 * @property int reaction_score
 * @property array reactions_
 * @property array reaction_users_
 *
 * GETTERS
 * @property array comment_ids
 * @property ArrayCollection|null LatestComments
 * @property mixed Unfurls
 * @property mixed reactions
 * @property mixed reaction_users
 *
 * RELATIONS
 * @property \XF\Entity\User ProfileUser
 * @property \XF\Entity\User User
 * @property \XF\Entity\DeletionLog DeletionLog
 * @property \XF\Entity\ApprovalQueue ApprovalQueue
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ProfilePostComment[] Comments
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ReactionContent[] Reactions
 */
class ProfilePost extends Entity implements RenderableContentInterface
{
	use ReactionTrait;

	public function canView(&$error = null)
	{
		$visitor = \XF::visitor();

		if (!$this->ProfileUser)
		{
			return false;
		}

		if (!$this->ProfileUser->canViewPostsOnProfile())
		{
			return false;
		}

		if ($this->message_state == 'moderated')
		{
			if (
				!$visitor->hasPermission('profilePost', 'viewModerated')
				&& (!$visitor->user_id || $visitor->user_id != $this->user_id)
			)
			{
				$error = \XF::phraseDeferred('requested_profile_post_not_found');
				return false;
			}
		}
		else if ($this->message_state == 'deleted')
		{
			if (!$visitor->hasPermission('profilePost', 'viewDeleted'))
			{
				$error = \XF::phraseDeferred('requested_profile_post_not_found');
				return false;
			}
		}

		return true;
	}

	public function canUseInlineModeration(&$error = null)
	{
		$visitor = \XF::visitor();
		return ($visitor->user_id && $visitor->hasPermission('profilePost', 'inlineMod'));
	}

	public function canEdit(&$error = null)
	{
		$visitor = \XF::visitor();

		if (!$visitor->user_id)
		{
			return false;
		}

		if ($visitor->user_id == $this->user_id)
		{
			return $visitor->hasPermission('profilePost', 'editOwn');
		}
		else
		{
			return $visitor->hasPermission('profilePost', 'editAny');
		}
	}

	public function canDelete($type = 'soft', &$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if ($type != 'soft' && !$visitor->hasPermission('profilePost', 'hardDeleteAny'))
		{
			return false;
		}

		if ($visitor->hasPermission('profilePost', 'deleteAny'))
		{
			return true;
		}

		return (
			(
				$visitor->user_id == $this->profile_user_id
				&& $visitor->hasPermission('profilePost', 'manageOwn')
			)
			||
			(
				$visitor->user_id == $this->user_id
				&& $visitor->hasPermission('profilePost', 'deleteOwn')
			)
		);
	}

	public function canUndelete(&$error = null)
	{
		$visitor = \XF::visitor();
		return ($visitor->user_id && $visitor->hasPermission('profilePost', 'undelete'));
	}

	public function canApproveUnapprove(&$error = null)
	{
		$visitor = \XF::visitor();
		return ($visitor->user_id && $visitor->hasPermission('profilePost', 'approveUnapprove'));
	}

	public function canWarn(&$error = null)
	{
		$visitor = \XF::visitor();

		if ($this->warning_id
			|| !$this->user_id
			|| !$visitor->user_id
			|| $this->user_id == $visitor->user_id
			|| !$visitor->hasPermission('profilePost', 'warn')
		)
		{
			return false;
		}

		return ($this->User && $this->User->isWarnable());
	}

	public function canReport(&$error = null, User $asUser = null)
	{
		$asUser = $asUser ?: \XF::visitor();
		return $asUser->canReport($error);
	}

	public function canReact(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if ($this->message_state != 'visible')
		{
			return false;
		}

		if ($this->user_id == $visitor->user_id)
		{
			$error = \XF::phraseDeferred('reacting_to_your_own_content_is_considered_cheating');
			return false;
		}

		return $visitor->hasPermission('profilePost', 'react');
	}

	public function canComment(&$error = null)
	{
		$visitor = \XF::visitor();

		return (
			$this->message_state == 'visible'
				&& $visitor->user_id
				&& $visitor->hasPermission('profilePost', 'view')
				&& $visitor->hasPermission('profilePost', 'comment')
				&& $this->ProfileUser->isPrivacyCheckMet('allow_post_profile', $visitor)
		);
	}

	public function canViewDeletedComments()
	{
		return \XF::visitor()->hasPermission('profilePost', 'viewDeleted');
	}

	public function canViewModeratedComments()
	{
		return \XF::visitor()->hasPermission('profilePost', 'viewModerated');
	}

	public function canSendModeratorActionAlert()
	{
		$visitor = \XF::visitor();

		if (!$visitor->user_id || $visitor->user_id == $this->user_id)
		{
			return false;
		}

		if ($this->message_state != 'visible')
		{
			return false;
		}

		return (
			$visitor->hasPermission('profilePost', 'deleteAny')
			|| $visitor->hasPermission('profilePost', 'editAny')
		);
	}

	public function hasMoreComments()
	{
		if ($this->comment_count > 3)
		{
			return true;
		}

		$visitor = \XF::visitor();

		$canViewDeleted = $visitor->hasPermission('profilePost', 'viewDeleted');
		$canViewModerated = $visitor->hasPermission('profilePost', 'viewModerated');

		if (!$canViewDeleted && !$canViewModerated)
		{
			return false;
		}

		$viewableCommentCount = 0;

		foreach ($this->latest_comment_ids AS $commentId => $state)
		{
			switch ($state[0])
			{
				case 'visible':
					$viewableCommentCount++;
					break;

				case 'moderated':
					if ($canViewModerated)
					{
						$viewableCommentCount++;
					}
					break;

				case 'deleted':
					if ($canViewDeleted)
					{
						$viewableCommentCount++;
					}
					break;
			}

			if ($viewableCommentCount > 3)
			{
				return true;
			}
		}

		return false;
	}

	public function isVisible()
	{
		return ($this->message_state == 'visible');
	}

	public function isIgnored()
	{
		return \XF::visitor()->isIgnoring($this->user_id);
	}

	public function canCleanSpam()
	{
		return (\XF::visitor()->canCleanSpam() && $this->User && $this->User->isPossibleSpammer());
	}

	/**
	 * @return array
	 */
	public function getCommentIds()
	{
		return $this->db()->fetchAllColumn("
			SELECT profile_post_comment_id
			FROM xf_profile_post_comment
			WHERE profile_post_id = ?
			ORDER BY comment_date
		", $this->profile_post_id);
	}

	/**
	 * @return ArrayCollection|null
	 */
	public function getLatestComments()
	{
		$this->repository('XF:ProfilePost')->addCommentsToProfilePosts([$this->profile_post_id => $this]);

		if (isset($this->_getterCache['LatestComments']))
		{
			return $this->_getterCache['LatestComments'];
		}
		else
		{
			return $this->_em->getBasicCollection([]);
		}
	}

	public function setLatestComments(array $latest)
	{
		$this->_getterCache['LatestComments'] = $this->_em->getBasicCollection($latest);
	}

	public function commentAdded(ProfilePostComment $comment)
	{
		$this->comment_count++;

		if (!$this->first_comment_date || $comment->comment_date < $this->first_comment_date)
		{
			$this->first_comment_date = $comment->comment_date;
		}

		if ($comment->comment_date > $this->last_comment_date)
		{
			$this->last_comment_date = $comment->comment_date;
		}

		$this->rebuildLatestCommentIds();

		unset($this->_getterCache['comment_ids']);
	}

	public function commentRemoved(ProfilePostComment $comment)
	{
		$this->comment_count--;

		if ($this->first_comment_date == $comment->comment_date)
		{
			if (!$this->comment_count)
			{
				$this->first_comment_date = 0;
			}
			else
			{
				$this->rebuildFirstCommentInfo();
			}
		}

		if ($this->last_comment_date == $comment->comment_date)
		{
			if (!$this->comment_count)
			{
				$this->last_comment_date = 0;
			}
			else
			{
				$this->rebuildLastCommentInfo();
			}
		}

		$this->rebuildLatestCommentIds();

		unset($this->_getterCache['comment_ids']);
	}

	public function rebuildCounters()
	{
		if (!$this->rebuildFirstCommentInfo())
		{
			// no visible comments, we know we've set the last comment and count to 0
		}
		else
		{
			$this->rebuildLastCommentInfo();
			$this->rebuildCommentCount();
		}

		// since this contains non-visible comments, we always have to rebuild
		$this->rebuildLatestCommentIds();

		return true;
	}

	public function rebuildFirstCommentInfo()
	{
		$firstComment = $this->db()->fetchRow("
			SELECT profile_post_comment_id, comment_date, user_id, username
			FROM xf_profile_post_comment
			WHERE profile_post_id = ?
				AND message_state = 'visible'
			ORDER BY comment_date 
			LIMIT 1
		", $this->profile_post_id);

		if (!$firstComment)
		{
			$this->comment_count = 0;
			$this->first_comment_date = 0;
			$this->last_comment_date = 0;
			return false;
		}
		else
		{
			$this->last_comment_date = $firstComment['comment_date'];
			return true;
		}
	}

	public function rebuildLastCommentInfo()
	{
		$lastComment = $this->db()->fetchRow("
			SELECT profile_post_comment_id, comment_date, user_id, username
			FROM xf_profile_post_comment
			WHERE profile_post_id = ?
				AND message_state = 'visible'
			ORDER BY comment_date DESC
			LIMIT 1
		", $this->profile_post_id);

		if (!$lastComment)
		{
			$this->comment_count = 0;
			$this->first_comment_date = 0;
			$this->last_comment_date = 0;
			return false;
		}
		else
		{
			$this->last_comment_date = $lastComment['comment_date'];
			return true;
		}
	}

	public function rebuildCommentCount()
	{
		$visibleComments = $this->db()->fetchOne("
			SELECT COUNT(*)
			FROM xf_profile_post_comment
			WHERE profile_post_id = ?
				AND message_state = 'visible'
		", $this->profile_post_id);

		$this->comment_count = $visibleComments;

		return $this->comment_count;
	}

	public function rebuildLatestCommentIds()
	{
		$this->latest_comment_ids = $this->repository('XF:ProfilePost')->getLatestCommentCache($this);
	}

	public function getBbCodeRenderOptions($context, $type)
	{
		return [
			'entity' => $this,
			'user' => $this->User,
			'treatAsStructuredText' => true,
			'unfurls' => $this->Unfurls ?: []
		];
	}

	public function getUnfurls()
	{
		return isset($this->_getterCache['Unfurls']) ? $this->_getterCache['Unfurls'] : [];
	}

	public function setUnfurls($unfurls)
	{
		$this->_getterCache['Unfurls'] = $unfurls;
	}

	protected function _postSave()
	{
		$visibilityChange = $this->isStateChanged('message_state', 'visible');
		$approvalChange = $this->isStateChanged('message_state', 'moderated');
		$deletionChange = $this->isStateChanged('message_state', 'deleted');

		if ($this->isUpdate())
		{
			if ($visibilityChange == 'enter')
			{
				$this->profilePostMadeVisible();

				if ($approvalChange)
				{
					$this->submitHamData();
				}
			}
			else if ($visibilityChange == 'leave')
			{
				$this->profilePostHidden();
			}

			if ($deletionChange == 'leave' && $this->DeletionLog)
			{
				$this->DeletionLog->delete();
			}

			if ($approvalChange == 'leave' && $this->ApprovalQueue)
			{
				$this->ApprovalQueue->delete();
			}
		}

		if ($approvalChange == 'enter')
		{
			$approvalQueue = $this->getRelationOrDefault('ApprovalQueue', false);
			$approvalQueue->content_date = $this->post_date;
			$approvalQueue->save();
		}
		else if ($deletionChange == 'enter' && !$this->DeletionLog)
		{
			$delLog = $this->getRelationOrDefault('DeletionLog', false);
			$delLog->setFromVisitor();
			$delLog->save();
		}

		if ($this->isUpdate() && $this->getOption('log_moderator'))
		{
			$this->app()->logger()->logModeratorChanges('profile_post', $this);
		}
	}

	protected function profilePostMadeVisible()
	{
	}

	protected function profilePostHidden($hardDelete = false)
	{
		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->fastDeleteAlertsForContent('profile_post', $this->profile_post_id);
		$alertRepo->fastDeleteAlertsForContent('profile_post_comment', $this->comment_ids);
	}

	protected function submitHamData()
	{
		/** @var \XF\Spam\ContentChecker $submitter */
		$submitter = $this->app()->container('spam.contentHamSubmitter');
		$submitter->submitHam('profile_post', $this->profile_post_id);
	}

	protected function _postDelete()
	{
		if ($this->message_state == 'visible')
		{
			$this->profilePostHidden(true);
		}

		if ($this->message_state == 'deleted' && $this->DeletionLog)
		{
			$this->DeletionLog->delete();
		}

		if ($this->message_state == 'moderated' && $this->ApprovalQueue)
		{
			$this->ApprovalQueue->delete();
		}

		if ($this->getOption('log_moderator'))
		{
			$this->app()->logger()->logModeratorAction('profile_post', $this, 'delete_hard');
		}

		$db = $this->db();
		$commentIds = $this->comment_ids;
		if ($commentIds)
		{
			$quotedIds = $db->quote($commentIds);

			$db->delete('xf_profile_post_comment', "profile_post_comment_id IN ({$quotedIds})");
			$db->delete('xf_approval_queue', "content_id IN ({$quotedIds}) AND content_type = 'profile_post_comment'");
			$db->delete('xf_deletion_log', "content_id IN ({$quotedIds}) AND content_type = 'profile_post_comment'");
		}
	}

	public function softDelete($reason = '', User $byUser = null)
	{
		$byUser = $byUser ?: \XF::visitor();

		if ($this->message_state == 'deleted')
		{
			return false;
		}

		$this->message_state = 'deleted';

		/** @var \XF\Entity\DeletionLog $deletionLog */
		$deletionLog = $this->getRelationOrDefault('DeletionLog');
		$deletionLog->setFromUser($byUser);
		$deletionLog->delete_reason = $reason;

		$this->save();

		return true;
	}

	public function getNewComment()
	{
		$comment = $this->_em->create('XF:ProfilePostComment');
		$comment->profile_post_id = $this->profile_post_id;

		return $comment;
	}

	public function getNewContentState()
	{
		$visitor = \XF::visitor();

		if ($visitor->user_id && $visitor->hasPermission('profilePost', 'approveUnapprove'))
		{
			return 'visible';
		}

		if (!$visitor->hasPermission('general', 'submitWithoutApproval'))
		{
			return 'moderated';
		}

		return 'visible';
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @api-out str $username
	 * @api-out bool $can_edit
	 * @api-out bool $can_soft_delete
	 * @api-out bool $can_hard_delete
	 * @api-out bool $can_react
	 * @api-out User $ProfileUser <cond> If requested by context, the user this profile post was left for.
	 * @api-out ProfilePostComment[] $LatestComments <cond> If requested, the most recent comments on this profile post.
	 * @api-see XF\Entity\ReactionTrait::addReactionStateToApiResult
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$result->username = $this->User ? $this->User->username : $this->username;

		if (!empty($options['with_profile']))
		{
			$result->includeRelation('ProfileUser');
		}

		if (!empty($options['with_latest']))
		{
			$result->includeGetter('LatestComments');
		}

		$this->addReactionStateToApiResult($result);

		$result->can_edit = $this->canEdit();
		$result->can_soft_delete = $this->canDelete();
		$result->can_hard_delete = $this->canDelete('hard');
		$result->can_react = $this->canReact();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_profile_post';
		$structure->shortName = 'XF:ProfilePost';
		$structure->contentType = 'profile_post';
		$structure->primaryKey = 'profile_post_id';
		$structure->columns = [
			'profile_post_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'profile_user_id' => ['type' => self::UINT, 'required' => true, 'api' => true],
			'user_id' => ['type' => self::UINT, 'required' => true, 'api' => true],
			'username' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_name'
			],
			'post_date' => ['type' => self::UINT, 'required' => true, 'default' => \XF::$time, 'api' => true],
			'message' => ['type' => self::STR,
				'required' => 'please_enter_valid_message', 'api' => true
			],
			'ip_id' => ['type' => self::UINT, 'default' => 0],
			'message_state' => ['type' => self::STR, 'default' => 'visible',
				'allowedValues' => ['visible', 'moderated', 'deleted'], 'api' => true
			],
			'attach_count' => ['type' => self::UINT, 'max' => 65535, 'forced' => true, 'default' => 0],
			'warning_id' => ['type' => self::UINT, 'default' => 0],
			'warning_message' => ['type' => self::STR, 'default' => '', 'maxLength' => 255, 'api' => true],
			'comment_count' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'api' => true],
			'first_comment_date' => ['type' => self::UINT, 'default' => 0, 'api' => true],
			'last_comment_date' => ['type' => self::UINT, 'default' => 0, 'api' => true],
			'latest_comment_ids' => ['type' => self::JSON_ARRAY, 'default' => []],
			'embed_metadata' => ['type' => self::JSON_ARRAY, 'nullable' => true, 'default' => null]
		];
		$structure->behaviors = [
			'XF:Reactable' => ['stateField' => 'message_state'],
			'XF:ReactableContainer' => [
				'childContentType' => 'profile_post_comment',
				'childIds' => function($profilePost) { return $profilePost->comment_ids; },
				'stateField' => 'message_state'
			],
			'XF:Indexable' => [
				'checkForUpdates' => ['message', 'profile_user_id', 'user_id', 'post_date', 'message_state']
			],
			'XF:IndexableContainer' => [
				'childContentType' => 'profile_post_comment',
				'childIds' => function($profilePost) { return $profilePost->comment_ids; },
				'checkForUpdates' => ['profile_user_id', 'message_state']
			],
			'XF:NewsFeedPublishable' => [
				'usernameField' => 'username',
				'dateField' => 'post_date'
			]
		];
		$structure->getters = [
			'comment_ids' => true,
			'LatestComments' => true,
			'Unfurls' => true,
		];
		$structure->relations = [
			'ProfileUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$profile_user_id']],
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true,
				'api' => true
			],
			'DeletionLog' => [
				'entity' => 'XF:DeletionLog',
				'type' => self::TO_ONE,
				'conditions' => [
					['content_type', '=', 'profile_post'],
					['content_id', '=', '$profile_post_id']
				],
				'primary' => true
			],
			'ApprovalQueue' => [
				'entity' => 'XF:ApprovalQueue',
				'type' => self::TO_ONE,
				'conditions' => [
					['content_type', '=', 'profile_post'],
					['content_id', '=', '$profile_post_id']
				],
				'primary' => true
			],
			'Comments' => [
				'entity' => 'XF:ProfilePostComment',
				'type' => self::TO_MANY,
				'conditions' => 'profile_post_id',
				'primary' => true
			]
		];
		$structure->options = [
			'log_moderator' => true
		];

		$structure->withAliases = [
			'full' => [
				'User',
				function()
				{
					$userId = \XF::visitor()->user_id;
					if ($userId)
					{
						return 'Reactions|' . $userId;
					}

					return null;
				}
			],
			'fullProfile' => ['full', 'ProfileUser', 'ProfileUser.Privacy'],
			'api' => [
				'User',
				'User.api',
				function($withParams)
				{
					if (!empty($withParams['profile']))
					{
						return ['ProfileUser.api'];
					}
				}
			]
		];

		static::addReactableStructureElements($structure);

		return $structure;
	}
}