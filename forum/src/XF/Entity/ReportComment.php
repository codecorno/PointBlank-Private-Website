<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null report_comment_id
 * @property int report_id
 * @property int comment_date
 * @property int user_id
 * @property string username
 * @property string message
 * @property string state_change
 * @property bool is_report
 *
 * RELATIONS
 * @property \XF\Entity\Report Report
 * @property \XF\Entity\User User
 */
class ReportComment extends Entity implements \XF\BbCode\RenderableContentInterface
{
	public function hasSaveableChanges()
	{
		return (
			$this->state_change ||
			$this->message ||
			$this->Report->isChanged('assigned_user_id')
		);
	}

	public function isClosureComment()
	{
		return ($this->state_change === 'resolved' || $this->state_change === 'rejected');
	}

	public function getBbCodeRenderOptions($context, $type)
	{
		return [
			'entity' => $this,
			'user' => $this->User
		];
	}

	protected function _preSave()
	{
		if (!$this->hasSaveableChanges())
		{
			$this->error(\XF::phrase('please_enter_valid_message'), 'message');
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_report_comment';
		$structure->shortName = 'XF:ReportComment';
		$structure->contentType = 'report_comment';
		$structure->primaryKey = 'report_comment_id';
		$structure->columns = [
			'report_comment_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'report_id' => ['type' => self::UINT, 'required' => true],
			'comment_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'username' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_name'
			],
			'message' => ['type' => self::STR, 'default' => ''],
			'state_change' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'open', 'assigned', 'resolved', 'rejected']
			],
			'is_report' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [];
		$structure->relations = [
			'Report' => [
				'entity' => 'XF:Report',
				'type' => self::TO_ONE,
				'conditions' => 'report_id',
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];

		return $structure;
	}
}