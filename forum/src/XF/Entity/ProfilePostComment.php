<?php

namespace XF\Entity;

use XF\BbCode\RenderableContentInterface;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null profile_post_comment_id
 * @property int profile_post_id
 * @property int user_id
 * @property string username
 * @property int comment_date
 * @property string message
 * @property int ip_id
 * @property string message_state
 * @property int warning_id
 * @property string warning_message
 * @property array|null embed_metadata
 * @property int reaction_score
 * @property array reactions_
 * @property array reaction_users_
 *
 * GETTERS
 * @property mixed Unfurls
 * @property mixed reactions
 * @property mixed reaction_users
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\ProfilePost ProfilePost
 * @property \XF\Entity\DeletionLog DeletionLog
 * @property \XF\Entity\ApprovalQueue ApprovalQueue
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ReactionContent[] Reactions
 */
class ProfilePostComment extends Entity implements RenderableContentInterface
{
	use ReactionTrait;

	public function canView(&$error = null)
	{
		$visitor = \XF::visitor();

		/** @var \XF\Entity\ProfilePost $profilePost */
		$profilePost = $this->ProfilePost;
		if (!$profilePost)
		{
			return false;
		}

		if ($this->message_state == 'moderated')
		{
			if (
				!$profilePost->canViewModeratedComments()
				&& (!$visitor->user_id || $visitor->user_id != $this->user_id)
			)
			{
				$error = \XF::phraseDeferred('requested_comment_not_found');
				return false;
			}
		}
		else if ($this->message_state == 'deleted')
		{
			if (!$profilePost->canViewDeletedComments())
			{
				$error = \XF::phraseDeferred('requested_comment_not_found');
				return false;
			}
		}

		return $profilePost->canView($error);
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
				$this->ProfilePost
				&& $visitor->user_id == $this->ProfilePost->profile_user_id
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
			$this->ProfilePost->canSendModeratorActionAlert()
			&& $this->message_state == 'visible'
		);
	}

	public function isVisible()
	{
		return (
			$this->message_state == 'visible'
			&& $this->ProfilePost
			&& $this->ProfilePost->message_state == 'visible'
		);
	}

	public function isIgnored()
	{
		return \XF::visitor()->isIgnoring($this->user_id);
	}

	public function isLastComment()
	{
		return (
			$this->ProfilePost
			&& $this->ProfilePost->last_comment_date == $this->comment_date
		);
	}

	public function canCleanSpam()
	{
		return (\XF::visitor()->canCleanSpam() && $this->User && $this->User->isPossibleSpammer());
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
				$this->commentMadeVisible();

				if ($approvalChange)
				{
					$this->submitHamData();
				}
			}
			else if ($visibilityChange == 'leave')
			{
				$this->commentHidden();
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
			$approvalQueue->content_date = $this->comment_date;
			$approvalQueue->save();
		}
		else if ($deletionChange == 'enter' && !$this->DeletionLog)
		{
			$delLog = $this->getRelationOrDefault('DeletionLog', false);
			$delLog->setFromVisitor();
			$delLog->save();
		}

		$this->updateProfilePostRecord();

		if ($this->isUpdate() && $this->getOption('log_moderator'))
		{
			$this->app()->logger()->logModeratorChanges('profile_post_comment', $this);
		}
	}

	protected function commentMadeVisible()
	{
	}

	protected function commentHidden($hardDelete = false)
	{
		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->fastDeleteAlertsForContent('profile_post_comment', $this->profile_post_comment_id);
	}

	protected function updateProfilePostRecord()
	{
		if (!$this->ProfilePost || !$this->ProfilePost->exists())
		{
			return;
		}

		$visibilityChange = $this->isStateChanged('message_state', 'visible');
		if ($visibilityChange == 'enter')
		{
			$this->ProfilePost->commentAdded($this);
			$this->ProfilePost->save();
		}
		else if ($visibilityChange == 'leave')
		{
			$this->ProfilePost->commentRemoved($this);
			$this->ProfilePost->save();
		}
	}

	protected function submitHamData()
	{
		/** @var \XF\Spam\ContentChecker $submitter */
		$submitter = $this->app()->container('spam.contentHamSubmitter');
		$submitter->submitHam('profile_post_comment', $this->profile_post_comment_id);
	}

	protected function _postDelete()
	{
		if ($this->message_state == 'visible')
		{
			$this->commentHidden(true);
		}

		if ($this->ProfilePost && $this->message_state == 'visible')
		{
			$this->ProfilePost->commentRemoved($this);
			$this->ProfilePost->save();
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
			$this->app()->logger()->logModeratorAction('profile_post_comment', $this, 'delete_hard');
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
	 * @api-out ProfilePost $ProfilePost <cond> If requested by context, the profile post this comment relates to.
	 *
	 * @api-see XF\Entity\ReactionTrait::addReactionStateToApiResult
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$result->username = $this->User ? $this->User->username : $this->username;

		if (!empty($options['with_post']))
		{
			$result->includeRelation('ProfilePost', self::VERBOSITY_NORMAL, [
				'with_profile' => true
			]);
		}

		$this->addReactionStateToApiResult($result);

		$result->can_edit = $this->canEdit();
		$result->can_soft_delete = $this->canDelete();
		$result->can_hard_delete = $this->canDelete('hard');
		$result->can_react = $this->canReact();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_profile_post_comment';
		$structure->shortName = 'XF:ProfilePostComment';
		$structure->contentType = 'profile_post_comment';
		$structure->primaryKey = 'profile_post_comment_id';
		$structure->columns = [
			'profile_post_comment_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'profile_post_id' => ['type' => self::UINT, 'required' => true, 'api' => true],
			'user_id' => ['type' => self::UINT, 'required' => true, 'api' => true],
			'username' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_name'
			],
			'comment_date' => ['type' => self::UINT, 'required' => true, 'default' => \XF::$time, 'api' => true],
			'message' => ['type' => self::STR,
				'required' => 'please_enter_valid_message', 'api' => true
			],
			'ip_id' => ['type' => self::UINT, 'default' => 0],
			'message_state' => ['type' => self::STR, 'default' => 'visible',
				'allowedValues' => ['visible', 'moderated', 'deleted'], 'api' => true
			],
			'warning_id' => ['type' => self::UINT, 'default' => 0],
			'warning_message' => ['type' => self::STR, 'default' => '', 'maxLength' => 255, 'api' => true],
			'embed_metadata' => ['type' => self::JSON_ARRAY, 'nullable' => true, 'default' => null]
		];
		$structure->behaviors = [
			'XF:Reactable' => ['stateField' => 'message_state'],
			'XF:Indexable' => [
				'checkForUpdates' => ['message', 'user_id', 'comment_date', 'message_state']
			],
			'XF:NewsFeedPublishable' => [
				'usernameField' => 'username',
				'dateField' => 'comment_date'
			]
		];
		$structure->getters = [
			'Unfurls' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true,
				'api' => true
			],
			'ProfilePost' => [
				'entity' => 'XF:ProfilePost',
				'type' => self::TO_ONE,
				'conditions' => 'profile_post_id',
				'primary' => true
			],
			'DeletionLog' => [
				'entity' => 'XF:DeletionLog',
				'type' => self::TO_ONE,
				'conditions' => [
					['content_type', '=', 'profile_post_comment'],
					['content_id', '=', '$profile_post_comment_id']
				],
				'primary' => true
			],
			'ApprovalQueue' => [
				'entity' => 'XF:ApprovalQueue',
				'type' => self::TO_ONE,
				'conditions' => [
					['content_type', '=', 'profile_post_comment'],
					['content_id', '=', '$profile_post_comment_id']
				],
				'primary' => true
			]
		];
		$structure->options = [
			'log_moderator' => true
		];
		$structure->defaultWith = ['ProfilePost'];

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
			'api' => [
				'User',
				'User.api',
				function($withParams)
				{
					if (!empty($withParams['post']))
					{
						return ['ProfilePost.api|profile'];
					}
				}
			]
		];

		static::addReactableStructureElements($structure);

		return $structure;
	}
}