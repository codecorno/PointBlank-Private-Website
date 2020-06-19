<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null report_id
 * @property string content_type
 * @property int content_id
 * @property int content_user_id
 * @property array content_info
 * @property int first_report_date
 * @property string report_state
 * @property int assigned_user_id
 * @property int comment_count
 * @property int report_count
 * @property int last_modified_date
 * @property int last_modified_user_id
 * @property string last_modified_username
 *
 * GETTERS
 * @property mixed Content
 * @property \XF\Draft draft_comment
 * @property \XF\Report\AbstractHandler|null Handler
 * @property array last_modified_cache
 * @property string title
 * @property string link
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User AssignedUser
 * @property \XF\Entity\User LastModifiedUser
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ReportComment[] Comments
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\Draft[] DraftComments
 */
class Report extends Entity
{
	public function canView()
	{
		$handler = $this->Handler;

		return ($handler && $handler->canView($this));
	}

	public function getReportState($state = null)
	{
		if ($state === null)
		{
			$state = $this->report_state;
		}

		return \XF::phrase('report_state.' . $state);
	}

	public function isAssignedTo($userId = null)
	{
		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}
		if ($userId instanceof User)
		{
			$userId = $userId->user_id;
		}

		return $this->assigned_user_id && $this->assigned_user_id == $userId;
	}

	/**
	 * @return \XF\Draft
	 */
	public function getDraftComment()
	{
		return \XF\Draft::createFromEntity($this, 'DraftComments');
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		$handler = $this->Handler;
		return $handler ? $handler->getContentTitle($this) : '';
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		$handler = $this->Handler;
		return $handler ? $handler->getContentLink($this) : '';
	}

	public function getNewComment()
	{
		$comment = $this->_em->create('XF:ReportComment');

		$comment->report_id = $this->_getDeferredValue(function()
		{
			return $this->report_id;
		}, 'save');

		return $comment;
	}

	public function getContent()
	{
		$handler = $this->Handler;
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	/**
	 * @return \XF\Report\AbstractHandler|null
	 */
	public function getHandler()
	{
		return $this->getReportRepo()->getReportHandler($this->content_type);
	}

	public function isClosed()
	{
		return ($this->report_state == 'rejected' || $this->report_state == 'resolved');
	}

	/**
	 * @return array
	 */
	public function getLastModifiedCache()
	{
		return [
			'user_id' => $this->last_modified_user_id,
			'username' => $this->last_modified_username,
			'modified_date' => $this->last_modified_date
		];
	}

	protected function _postSave()
	{
		$this->rebuildReportCounts();
	}

	protected function _postDelete()
	{
		$this->rebuildReportCounts();
	}

	protected function rebuildReportCounts()
	{
		\XF::runOnce('reportCountsRebuild', function()
		{
			$this->getReportRepo()->rebuildReportCounts();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_report';
		$structure->shortName = 'XF:Report';
		$structure->contentType = 'report';
		$structure->primaryKey = 'report_id';
		$structure->columns = [
			'report_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'content_user_id' => ['type' => self::UINT, 'required' => true],
			'content_info' => ['type' => self::JSON_ARRAY, 'required' => true],
			'first_report_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'report_state' => ['type' => self::STR, 'default' => 'open',
				'allowedValues' => ['open', 'assigned', 'resolved', 'rejected']
			],
			'assigned_user_id' => ['type' => self::UINT, 'default' => 0],
			'comment_count' => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'report_count' => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'last_modified_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_modified_user_id' => ['type' => self::UINT, 'default' => 0],
			'last_modified_username' => ['type' => self::STR, 'maxLength' => 50, 'default' => '']
		];
		$structure->getters = [
			'Content' => true,
			'draft_comment' => true,
			'Handler' => true,
			'last_modified_cache' => true,
			'title' => true,
			'link' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$content_user_id']
				],
				'primary' => true
			],
			'AssignedUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$assigned_user_id']
				],
				'primary' => true
			],
			'LastModifiedUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$last_modified_user_id']
				],
				'primary' => true
			],
			'Comments' => [
				'entity' => 'XF:ReportComment',
				'type' => self::TO_MANY,
				'conditions' => 'report_id'
			],
			'DraftComments' => [
				'entity' => 'XF:Draft',
				'type' => self::TO_MANY,
				'conditions' => [
					['draft_key', '=', 'report-comment-', '$report_id']
				],
				'key' => 'user_id'
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Report
	 */
	protected function getReportRepo()
	{
		return $this->repository('XF:Report');
	}
}