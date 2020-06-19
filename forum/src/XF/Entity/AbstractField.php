<?php

namespace XF\Entity;

use XF\Db\Exception;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Class AbstractField
 *
 * @package XF\Entity
 *
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
 *
 * GETTERS
 * @property \XF\Phrase|string title
 * @property \XF\Phrase|string description
 *
 * RELATIONS
 * @property Phrase MasterTitle
 * @property Phrase MasterDescription
 */
abstract class AbstractField extends Entity
{
	abstract protected function getClassIdentifier();

	protected static function getPhrasePrefix()
	{
		throw new \LogicException('The phrase key must be overridden.');
	}

	public function getPhraseName($title)
	{
		return static::getPhrasePrefix() . '_' . ($title ? 'title' : 'desc') . '.' . $this->field_id;
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName(true));
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getDescription()
	{
		return \XF::phrase($this->getPhraseName(false));
	}

	/**
	 * @param $title
	 *
	 * @return Phrase
	 */
	public function getMasterPhrase($title)
	{
		$phrase = $title ? $this->MasterTitle : $this->MasterDescription;
		if (!$phrase)
		{
			/** @var Phrase $phrase */
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() use ($title) { return $this->getPhraseName($title); });
			$phrase->language_id = 0;
			$phrase->addon_id = '';
		}

		return $phrase;
	}

	public function isChoiceField()
	{
		$types = $this->getFieldRepo()->getFieldTypes();
		if (isset($types[$this->field_type]))
		{
			$type = $types[$this->field_type]['type'];
			return ($type == 'single' || $type == 'multiple');
		}
		else
		{
			return false;
		}
	}

	public function getChoiceLabel($choice)
	{
		if (!isset($this->field_choices[$choice]))
		{
			return $choice;
		}
		else
		{
			return \XF::phrase($this->getChoicePhraseName($choice));
		}
	}

	public function getChoicePhraseNames($choices)
	{
		$phraseNames = [];
		foreach ($choices AS $choice)
		{
			$phraseNames[$choice] = $this->getChoicePhraseName($choice);
		}
		return $phraseNames;
	}

	public function getChoicePhraseName($choice)
	{
		return static::getPhrasePrefix() . '_' . 'choice' . '.' . $this->field_id . '_' . $choice;
	}

	public function getMasterChoicePhrase($choice)
	{
		$choicePhraseName = $this->getChoicePhraseName($choice);

		$phrase = $this->finder('XF:Phrase')
			->where('title', $choicePhraseName)
			->fetchOne();

		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $choicePhraseName;
			$phrase->language_id = 0;
			$phrase->addon_id = '';
		}

		return $phrase;
	}

	protected function verifyFieldChoices(array &$choices)
	{
		foreach ($choices AS $value => &$text)
		{
			$text = trim(strval($text));

			if ($text === '')
			{
				$this->error(\XF::phrase('please_enter_text_for_each_choice'), 'field_choices');
				return false;
			}

			if ($value === '' || preg_match('#[^a-z0-9_]#i', $value))
			{
				$this->error(\XF::phrase('please_enter_an_id_using_only_alphanumeric'), 'field_choices');
				return false;
			}

			if (strlen($value) > 25)
			{
				$this->error(\XF::phrase('please_enter_value_using_x_characters_or_fewer', ['count' => 25]));
				return false;
			}
		}

		return true;
	}

	protected function _validateMatchTypeNumber(&$error, array &$matchParams)
	{
		$min = null;
		$max = null;

		if (!empty($matchParams['number_min']))
		{
			if (is_numeric($matchParams['number_min']))
			{
				$min = $matchParams['number_min'];
			}
			else
			{
				$error = 'invalid minimum value';
				return false;
			}
		}

		if (!empty($matchParams['number_max']))
		{
			if (is_numeric($matchParams['number_max']))
			{
				$max = $matchParams['number_max'];
			}
			else
			{
				$error = 'invalid maximum value';
				return false;
			}
		}

		if ($min !== null && $max !== null)
		{
			if ($min > $max)
			{
				$error = 'min can not be greater than max';
				return false;
			}
		}

		return true;
	}

	protected function _validateMatchTypeDate(&$error, array &$matchParams)
	{
		$matchParams = array_replace([
			'date_constraint' => ''
		], $matchParams);

		switch ($matchParams['date_constraint'])
		{
			case 'past':
			case 'future':
				return true;

			default:
				$matchParams['date_constraint'] = '';
		}

		return true;
	}

	protected function _validateMatchTypeRegex(&$error, array &$matchParams)
	{
		$matchParams = array_replace([
			'regex' => ''
		], $matchParams);

		if (!$matchParams['regex'] || !\XF\Util\Php::isValidRegex($matchParams['regex'], '/'))
		{
			$error = \XF::phrase('please_enter_valid_regular_expression');
			return false;
		}

		return true;
	}

	protected function _validateMatchTypeCallback(&$error, array &$matchParams)
	{
		$matchParams = array_replace([
			'callback_class' => '',
			'callback_method' => ''
		], $matchParams);

		if (!\XF\Util\Php::validateCallbackPhrased($matchParams['callback_class'], $matchParams['callback_method'], $error))
		{
			return false;
		}

		return true;
	}

	protected function _validateMatchTypeValidator(&$error, array &$matchParams)
	{
		if (empty($matchParams['validator']))
		{
			$error = \XF::phrase('please_enter_valid_validator');
			return false;
		}

		try
		{
			\XF::app()->validator($matchParams['validator']);
		}
		catch (\LogicException $e)
		{
			$error = $e->getMessage();
			return false;
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->isUpdate() && $this->isChanged('field_id'))
		{
			$this->error('The field ID cannot be changed once set.', 'field_id');
		}

		if ($this->isChanged(['match_type', 'match_params']))
		{
			$validateMatchTypeMethod = '_validateMatchType' . \XF\Util\Php::camelCase($this->get('match_type'));
			if (method_exists($this, $validateMatchTypeMethod))
			{
				$matchParams = $this->match_params;
				if ($this->$validateMatchTypeMethod($error, $matchParams))
				{
					$this->match_params = $matchParams;
				}
				else
				{
					$this->error($error, 'match_params');
				}
			}
		}

		if ($this->isUpdate() && $this->isChanged('field_type'))
		{
			$typeMap = $this->getFieldRepo()->getFieldTypes();
			if ($typeMap[$this->get('field_type')]['compatible'] != $typeMap[$this->getExistingValue('field_type')]['compatible'])
			{
				$this->error(\XF::phrase('you_may_not_change_field_to_different_type_after_it_has_been_created'), 'field_type');
			}
		}

		if ($this->isChoiceField())
		{
			if (($this->isInsert() && !$this->field_choices) || (is_array($this->field_choices) && !$this->field_choices))
			{
				$this->error(\XF::phrase('please_enter_at_least_one_choice'), 'field_choices', false);
			}
		}
	}

	protected function _postSave()
	{
		if ($this->isChanged('field_choices'))
		{
			$removed = [];
			$updated = [];

			$newValues = $this->getValue('field_choices');
			$oldValues = $this->getExistingValue('field_choices');

			foreach ($oldValues AS $key => $oldValue)
			{
				if (!isset($newValues[$key]))
				{
					$removed[] = $key;
				}
				else
				{
					$newValue = $newValues[$key];
					if ($oldValue !== $newValue)
					{
						$updated[$key] = $newValue;
					}
				}
			}
			foreach ($newValues AS $key => $newValue)
			{
				if (!isset($oldValues[$key]))
				{
					$updated[$key] = $newValue;
				}
				// otherwise, handled
			}

			if ($removed)
			{
				$this->deleteChoicePhrases($removed);
			}
			foreach ($updated AS $key => $value)
			{
				$phrase = $this->getMasterChoicePhrase($key);
				$phrase->phrase_text = $value;
				$phrase->save(true, false);
			}
		}

		$this->rebuildFieldCache();
	}

	protected function _postDelete()
	{
		if ($this->MasterTitle)
		{
			$this->MasterTitle->delete();
		}
		if ($this->MasterDescription)
		{
			$this->MasterDescription->delete();
		}
		$this->deleteChoicePhrases(array_keys($this->field_choices));

		$this->rebuildFieldCache();
	}

	protected function deleteChoicePhrases(array $choices)
	{
		$phraseNames = $this->getChoicePhraseNames($choices);

		$choicePhrases = $this->finder('XF:Phrase')
			->where('title', $phraseNames)
			->fetch();

		foreach ($choicePhrases AS $phrase)
		{
			$phrase->delete();
		}
	}

	protected function rebuildFieldCache()
	{
		\XF::runOnce('rebuildFieldCache' . $this->field_id, function()
		{
			$this->getFieldRepo()->rebuildFieldCache();
		});
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @api-out str $field_id
	 * @api-out str $title
	 * @api-out str $description
	 * @api-out int $display_order
	 * @api-out str $field_type
	 * @api-out object $field_choices <cond> For choice types, an ordered list of choices, with "option" and "name" keys for each.
	 * @api-out str $match_type
	 * @api-out array $match_params
	 * @api-out int $max_length
	 * @api-out bool $required
	 * @api-out str $display_group <cond> If this field type supports grouping, the group this field belongs to.
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$result->title = $this->title;
		$result->description = $this->description;

		if ($this->isChoiceField())
		{
			$choices = [];
			foreach ($this->field_choices AS $option => $printable)
			{
				$choices[] = [
					'option' => $option,
					'name' => $printable
				];
			}
			$result->field_choices = $choices;
		}

		$result->match_params = (object)$this->match_params;
	}

	protected static function setupDefaultStructure(Structure $structure, $table, $shortName, array $options = [])
	{
		$options = array_replace([
			'groups' => [],
			'has_user_editable' => false,
			'has_user_editable_once' => false,
			'has_moderator_editable' => false,
			'has_user_group_editable' => false
		], $options);

		$structure->table = $table;
		$structure->shortName = $shortName;
		$structure->primaryKey = 'field_id';
		$structure->columns = [
			'field_id' => ['type' => self::STR, 'maxLength' => 25,
				'required' => 'please_enter_valid_field_id',
				'unique' => 'field_ids_must_be_unique',
				'match' => 'alphanumeric',
				'api' => true
			],
			'display_order' => ['type' => self::UINT, 'default' => 1, 'api' => true],
			'field_type' => ['type' => self::STR, 'default' => 'textbox',
				'allowedValues' => ['textbox', 'textarea', 'bbcode', 'select', 'radio', 'checkbox', 'multiselect', 'date', 'stars', 'color'],
				'api' => true
			],
			'field_choices' => ['type' => self::JSON_ARRAY, 'default' => []],
			'match_type' => ['type' => self::STR, 'default' => 'none',
				'allowedValues' => ['none', 'number', 'alphanumeric', 'email', 'url', 'color', 'date', 'regex', 'callback', 'validator'],
				'api' => true
			],
			'match_params' => ['type' => self::JSON_ARRAY, 'default' => []],
			'max_length' => ['type' => self::UINT, 'default' => 0, 'api' => true],
			'required' => ['type' => self::BOOL, 'default' => false, 'api' => true],
			'display_template' => ['type' => self::STR, 'default' => '']
		];

		if ($options['groups'])
		{
			$firstOption = reset($options['groups']);
			$structure->columns['display_group'] = ['type' => self::STR, 'default' => $firstOption,
				'allowedValues' => $options['groups'],
				'api' => true
			];
		}
		if ($options['has_user_editable'])
		{
			$userEditAllowedValues = $options['has_user_editable_once'] ? ['yes', 'once', 'never'] : ['yes', 'never'];
			$structure->columns['user_editable'] = ['type' => self::STR, 'default' => 'yes',
				'allowedValues' => $userEditAllowedValues
			];
		}
		if ($options['has_moderator_editable'])
		{
			$structure->columns['moderator_editable'] = ['type' => self::BOOL, 'default' => true];
		}
		if ($options['has_user_group_editable'])
		{
			$structure->columns['editable_user_group_ids'] = ['type' => self::LIST_COMMA, 'default' => [-1]];
		}

		$structure->getters = [
			'title' => true,
			'description' => true
		];

		$phrasePrefix = static::getPhrasePrefix();
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', $phrasePrefix . '_title.', '$field_id']
				]
			],
			'MasterDescription' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', $phrasePrefix . '_desc.', '$field_id']
				]
			]
		];
	}

	/**
	 * @return \XF\Repository\AbstractField
	 */
	protected function getFieldRepo()
	{
		return $this->repository($this->getClassIdentifier());
	}
}