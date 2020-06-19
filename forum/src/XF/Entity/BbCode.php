<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string bb_code_id
 * @property string bb_code_mode
 * @property string has_option
 * @property string replace_html
 * @property string replace_html_email
 * @property string replace_text
 * @property string callback_class
 * @property string callback_method
 * @property string option_regex
 * @property int trim_lines_after
 * @property bool plain_children
 * @property bool disable_smilies
 * @property bool disable_nl2br
 * @property bool disable_autolink
 * @property bool allow_empty
 * @property bool allow_signature
 * @property string editor_icon_type
 * @property string editor_icon_value
 * @property bool active
 * @property string addon_id
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Phrase description
 * @property \XF\Phrase example
 * @property \XF\Phrase output
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDesc
 * @property \XF\Entity\Phrase MasterExample
 * @property \XF\Entity\Phrase MasterOutput
 */
class BbCode extends Entity
{
	public function canEdit()
	{
		if (!$this->addon_id || $this->isInsert())
		{
			return true;
		}
		else
		{
			return \XF::$developmentMode;
		}
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getDescription()
	{
		return \XF::phrase($this->getPhraseName('desc'));
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getExample()
	{
		return \XF::phrase($this->getPhraseName('example'));
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getOutput()
	{
		return \XF::phrase($this->getPhraseName('output'));
	}

	public function getPhraseName($type = 'title')
	{
		return 'custom_bb_code_' . $type . '.' . $this->bb_code_id;
	}

	public function getMasterPhrase($type = 'title')
	{
		$relation = 'Master' . ucfirst($type);
		$phrase = $this->$relation;

		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
			$phrase->title = $this->_getDeferredValue(function() use ($type) { return $this->getPhraseName($type); });
			$phrase->language_id = 0;
		}

		return $phrase;
	}

	protected function verifyBbCodeId(&$id)
	{
		$id = strtolower($id);

		return true;
	}

	protected function verifyOptionRegex($regex)
	{
		if (strlen($regex))
		{
			if (preg_match('/\W[\s\w]*e[\s\w]*$/', $regex))
			{
				// can't run a /e regex
				$this->error(\XF::phrase('please_enter_valid_regular_expression'), 'option_regex');
				return false;
			}
			else
			{
				try
				{
					preg_replace($regex, '', '');
				}
				catch (\ErrorException $e)
				{
					$this->error(\XF::phrase('please_enter_valid_regular_expression'), 'option_regex');
					return false;
				}
			}
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->bb_code_mode == 'replace')
		{
			$this->callback_class = '';
			$this->callback_method = '';
		}
		else if ($this->bb_code_mode == 'callback' && $this->isChanged(['callback_class', 'callback_method']))
		{
			$this->replace_html = '';
			$this->replace_html_email = '';
			$this->replace_text = '';

			if (!\XF\Util\Php::validateCallbackPhrased(
				$this->callback_class,
				$this->callback_method,
				$error)
			)
			{
				$this->error($error, 'callback_method');
			}
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			if ($this->isChanged('addon_id') || $this->isChanged('bb_code_id'))
			{
				foreach ($this->_structure->relations AS $name => $relation)
				{
					if ($relation['entity'] == 'XF:Phrase')
					{
						$masterPhrase = $this->getExistingRelation($name);
						if ($masterPhrase)
						{
							$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
							$masterPhrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

							$type = substr(strtolower($name), 6);

							$masterPhrase->addon_id = $this->addon_id;
							$masterPhrase->title = $this->getPhraseName($type);
							$masterPhrase->save();
						}
					}
				}
			}
		}

		$this->rebuildBbCodeCache();
	}

	protected function _postDelete()
	{
		foreach ($this->_structure->relations AS $name => $relation)
		{
			if ($relation['entity'] == 'XF:Phrase')
			{
				if ($this->$name)
				{
					$this->$name->delete();
				}
			}
		}

		$this->rebuildBbCodeCache();
	}

	protected function rebuildBbCodeCache()
	{
		\XF::runOnce('customBbCodeCache', function()
		{
			$this->getBbCodeRepo()->rebuildBbCodeCache();
		});
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_bb_code';
		$structure->shortName = 'XF:BbCode';
		$structure->primaryKey = 'bb_code_id';
		$structure->columns = [
			'bb_code_id' => ['type' => self::STR, 'maxLength' => 25,
				'required' => 'please_enter_valid_bb_code_tag',
				'unique' => 'bb_code_tags_must_be_unique',
				'match' => 'alphanumeric'
			],
			'bb_code_mode' => ['type' => self::STR, 'required' => true, 'default' => 'replace',
				'allowedValues' => ['replace', 'callback']
			],
			'has_option' => ['type' => self::STR, 'required' => true, 'default' => 'no',
				'allowedValues' => ['yes', 'no', 'optional']
			],
			'replace_html' => ['type' => self::STR, 'default' => ''],
			'replace_html_email' => ['type' => self::STR, 'default' => ''],
			'replace_text' => ['type' => self::STR, 'default' => ''],
			'callback_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'callback_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'option_regex' => ['type' => self::STR, 'default' => ''],
			'trim_lines_after' => ['type' => self::UINT, 'max' => 10, 'default' => 0],
			'plain_children' => ['type' => self::BOOL, 'default' => false],
			'disable_smilies' => ['type' => self::BOOL, 'default' => false],
			'disable_nl2br' => ['type' => self::BOOL, 'default' => false],
			'disable_autolink' => ['type' => self::BOOL, 'default' => false],
			'allow_empty' => ['type' => self::BOOL, 'default' => 0],
			'allow_signature' => ['type' => self::BOOL, 'default' => true],
			'editor_icon_type' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'image', 'fa']
			],
			'editor_icon_value' => ['type' => self::STR, 'maxLength' => 150, 'default' => ''],
			'active' => ['type' => self::BOOL, 'default' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'title' => true,
			'description' => true,
			'example' => true,
			'output' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'custom_bb_code_title.', '$bb_code_id']
				]
			],
			'MasterDesc' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'custom_bb_code_desc.', '$bb_code_id']
				]
			],
			'MasterExample' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'custom_bb_code_example.', '$bb_code_id']
				]
			],
			'MasterOutput' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'custom_bb_code_output.', '$bb_code_id']
				]
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\BbCode
	 */
	protected function getBbCodeRepo()
	{
		return $this->repository('XF:BbCode');
	}
}