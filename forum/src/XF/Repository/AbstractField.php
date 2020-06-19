<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractField extends Repository
{
	abstract protected function getRegistryKey();

	abstract protected function getClassIdentifier();

	abstract public function getDisplayGroups();

	public function getFieldTypes($forDisplay = false)
	{
		$method = $forDisplay ? 'phrase' : 'phraseDeferred';

		return [
			'textbox' => [
				'label' => \XF::$method('single_line_text_box'),
				'type' => 'text',
				'compatible' => 'text',
				'options' => 'text'
			],
			'textarea' => [
				'label' => \XF::$method('multi_line_text_box'),
				'type' => 'text',
				'compatible' => 'text',
				'options' => 'text'
			],
			'bbcode' => [
				'label' => \XF::$method('rich_text_box'),
				'type' => 'rich_text',
				'compatible' => 'text',
				'options' => 'text'
			],
			'select' => [
				'label' => \XF::$method('drop_down_selection'),
				'type' => 'single',
				'compatible' => 'single',
				'options' => 'choice'
			],
			'radio' => [
				'label' => \XF::$method('radio_buttons'),
				'type' => 'single',
				'compatible' => 'single',
				'options' => 'choice'
			],
			'checkbox' => [
				'label' => \XF::$method('check_boxes'),
				'type' => 'multiple',
				'compatible' => 'multiple',
				'options' => 'choice'
			],
			'multiselect' => [
				'label' => \XF::$method('multiple_choice_drop_down_selection'),
				'type' => 'multiple',
				'compatible' => 'multiple',
				'options' => 'choice'
			],
			'stars' => [
				'label' => \XF::$method('star_rating'),
				'type' => 'stars',
				'compatible' => '',
				'options' => ''
			],
		];
	}

	public function getMatchTypePhrases($forDisplay = false)
	{
		$method = $forDisplay ? 'phrase' : 'phraseDeferred';

		return [
			'none' => \XF::$method('none'),
			'number' => \XF::$method('number'),
			'alphanumeric' => \XF::$method('a_z_0_9_and_only'),
			'email' => \XF::$method('email_address'),
			'url' => \XF::$method('url'),
			'color' => \XF::$method('color'),
			'date' => \XF::$method('date'),
			'regex' => \XF::$method('regular_expression'),
			'callback' => \XF::$method('php_callback'),
			'validator' => \XF::$method('validator')
		];
	}

	public function getFieldListData()
	{
		$fields = $this->findFieldsForList()->fetch();

		return [
			'displayGroups' => $this->getDisplayGroups(),
			'fieldsGrouped' => $fields->groupBy('display_group')
		];
	}

	/**
	 * @return Finder
	 */
	public function findFieldsForList()
	{
		$finder = $this->finder($this->getClassIdentifier());

		if (empty($this->em->getEntityStructure($this->getClassIdentifier())->columns['display_group']))
		{
			$finder->order(['display_group', 'display_order']);
		}
		else
		{
			$finder->order('display_order');
		}

		return $finder;
	}

	public function getFieldCacheData()
	{
		$finder = $this->finder($this->getClassIdentifier());

		if (empty($this->em->getEntityStructure($this->getClassIdentifier())->columns['display_group']))
		{
			$finder->order(['display_group', 'display_order']);
		}
		else
		{
			$finder->order('display_order');
		}

		$fields = $finder->fetch();

		$cache = [];

		/** @var \XF\Entity\AbstractField $field */
		foreach ($fields AS $fieldId => $field)
		{
			$fieldTypes = $this->getFieldTypes();

			$cache[$fieldId] = $field->toArray() + [
				'title' => 	$field->getPhraseName(true),
				'description' => $field->getPhraseName(false),
				'type_group' => $fieldTypes[$field->field_type]['type']
			] + $this->getAdditionalCacheData($field);

			if (in_array($cache[$fieldId]['type_group'], ['single', 'multiple']))
			{
				foreach ($cache[$fieldId]['field_choices'] AS $choice => &$title)
				{
					$title = $field->getChoicePhraseName($choice);
				}
			}
		}

		return $cache;
	}

	public function rebuildFieldCache()
	{
		$cache = $this->getFieldCacheData();
		\XF::registry()->set($this->getRegistryKey(), $cache);
		return $cache;
	}

	public function getAdditionalCacheData(\XF\Entity\AbstractField $field)
	{
		return [];
	}
}