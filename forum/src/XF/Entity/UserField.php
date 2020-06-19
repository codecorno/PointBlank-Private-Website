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
 * @property string user_editable
 * @property bool moderator_editable
 * @property bool show_registration
 * @property bool viewable_profile
 * @property bool viewable_message
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Phrase description
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDescription
 */
class UserField extends AbstractField
{
	protected function getClassIdentifier()
	{
		return 'XF:UserField';
	}

	protected static function getPhrasePrefix()
	{
		return 'user_field';
	}

	protected function _postDelete()
	{
		$db = $this->db();
		$db->delete('xf_user_field_value', 'field_id = ?', $this->field_id);
		$db->delete('xf_change_log', 'content_type = \'user\' AND field = ?', "custom_fields:{$this->field_id}");

		parent::_postDelete();
	}

	public static function getStructure(Structure $structure)
	{
		self::setupDefaultStructure(
			$structure,
			'xf_user_field',
			'XF:UserField',
			[
				'groups' => ['personal', 'contact', 'preferences'],
				'has_user_editable' => true,
				'has_user_editable_once' => true,
				'has_moderator_editable' => true
			]
		);

		$structure->columns += [
			'show_registration' => ['type' => self::BOOL, 'default' => false],
			'viewable_profile' => ['type' => self::BOOL, 'default' => true],
			'viewable_message' => ['type' => self::BOOL, 'default' => false]
		];

		return $structure;
	}
}