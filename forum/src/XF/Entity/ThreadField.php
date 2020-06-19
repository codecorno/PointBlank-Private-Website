<?php

namespace XF\Entity;

use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string field_id
 * @property int display_order
 * @property string field_type
 * @property array field_choices
 * @property string match_type
 * @property array match_params
 * @property int max_length
 * @property bool required
 * @property string display_template
 * @property string display_group
 * @property array editable_user_group_ids
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Phrase description
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDescription
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ForumField[] ForumFields
 */
class ThreadField extends AbstractField
{
	protected function getClassIdentifier()
	{
		return 'XF:ThreadField';
	}

	protected static function getPhrasePrefix()
	{
		return 'thread_field';
	}

	protected function _postDelete()
	{
		parent::_postDelete();

		/** @var \XF\Repository\ForumField $repo */
		$repo = $this->repository('XF:ForumField');
		$repo->removeFieldAssociations($this);

		$this->db()->delete('xf_thread_field_value', 'field_id = ?', $this->field_id);
	}

	public static function getStructure(Structure $structure)
	{
		self::setupDefaultStructure(
			$structure,
			'xf_thread_field',
			'XF:ThreadField',
			[
				'groups' => ['before', 'after', 'thread_status'],
				'has_user_group_editable' => true,
			]
		);

		$structure->relations['ForumFields'] = [
			'entity' => 'XF:ForumField',
			'type' => self::TO_MANY,
			'conditions' => 'field_id'
		];

		return $structure;
	}
}