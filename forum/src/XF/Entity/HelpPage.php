<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string page_id
 * @property string page_name
 * @property int display_order
 * @property string callback_class
 * @property string callback_method
 * @property bool advanced_mode
 * @property bool active
 * @property string addon_id
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Phrase description
 * @property mixed public_url
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDescription
 * @property \XF\Entity\Template MasterTemplate
 * @property \XF\Entity\AddOn AddOn
 */
class HelpPage extends Entity
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

	public function hasCallback()
	{
		return method_exists($this->callback_class, $this->callback_method);
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

	public function getPhraseName($title)
	{
		return 'help_page_' . ($title ? 'title' : 'desc') . '.' . $this->page_id;
	}

	public function getMasterPhrase($title)
	{
		$phrase = $title ? $this->MasterTitle : $this->MasterDescription;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() use ($title) { return $this->getPhraseName($title); });
			$phrase->language_id = 0;
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
		}

		return $phrase;
	}

	public function getPublicUrl()
	{
		if ($this->page_id == 'terms' && $this->app()['tosUrl'])
		{
			return $this->app()['tosUrl'];
		}
		else if ($this->page_id == 'privacy_policy' && $this->app()['privacyPolicyUrl'])
		{
			return $this->app()['privacyPolicyUrl'];
		}
		else
		{
			return $this->app()->router('public')->buildLink('help', $this);
		}
	}

	public function getTemplateName()
	{
		return '_help_page_' . $this->page_id;
	}

	/**
	 * @return null|Template
	 */
	public function getMasterTemplate()
	{
		$template = $this->MasterTemplate;
		if (!$template)
		{
			$template = $this->_em->create('XF:Template');
			$template->title = $this->_getDeferredValue(function() { return $this->getTemplateName(); });
			$template->type = 'public';
			$template->style_id = 0;
			$template->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
		}

		return $template;
	}

	protected function verifyPageName(&$pageName)
	{
		$pageName = strtolower($pageName);

		if ($pageName === '')
		{
			$this->error(\XF::phrase('please_enter_valid_url_portion'), 'page_name');
			return false;
		}

		if ($pageName === strval(intval($pageName)) || $pageName == '-')
		{
			$this->error(\XF::phrase('node_names_contain_more_numbers_hyphen'), 'page_name');
			return false;
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->callback_class || $this->callback_method)
		{
			if (!\XF\Util\Php::validateCallbackPhrased($this->callback_class, $this->callback_method, $error))
			{
				$this->error($error, 'callback_method');
			}
		}

		if ($this->isChanged('page_name'))
		{
			$this->page_id = preg_replace(
				['/-/', '/[^a-zA-Z0-9_]/'],
				['_', ''],
				$this->page_name
			);
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			if ($this->isChanged(['addon_id', 'page_id']))
			{
				$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

				foreach ($this->_structure->relations AS $name => $relation)
				{
					if (in_array($relation['entity'], ['XF:Phrase', 'XF:Template']))
					{
						$masterContent = $this->getExistingRelation($name);
						if ($masterContent)
						{
							$masterContent->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

							$masterContent->addon_id = $this->addon_id;
							if ($relation['entity'] == 'XF:Phrase')
							{
								$masterContent->title = $this->getPhraseName($name == 'MasterTitle' ? true : false);
							}
							else
							{
								$masterContent->title = $this->getTemplateName();
							}
							$masterContent->save();
						}
					}
				}
			}
		}

		$this->rebuildHelpPageCount();
	}

	protected function _postDelete()
	{
		$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

		foreach ($this->_structure->relations AS $name => $relation)
		{
			if (in_array($relation['entity'], ['XF:Phrase', 'XF:Template']))
			{
				$masterContent = $this->getExistingRelation($name);
				if ($masterContent)
				{
					$masterContent->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
					$masterContent->delete();
				}
			}
		}

		$this->rebuildHelpPageCount();
	}

	protected function rebuildHelpPageCount()
	{
		\XF::runOnce('helpPageCountRebuild', function()
		{
			$this->getHelpPageRepo()->rebuildHelpPageCount();
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
		$structure->table = 'xf_help_page';
		$structure->shortName = 'XF:HelpPage';
		$structure->primaryKey = 'page_id';
		$structure->columns = [
			'page_id' => ['type' => self::STR,
				'required' => true,
				'match' => 'alphanumeric',
				'unique' => true
			],
			'page_name' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_url_portion',
				'unique' => 'help_page_names_must_be_unique',
				'match' => 'alphanumeric_hyphen'
			],
			'display_order' => ['type' => self::UINT, 'default' => 1],
			'callback_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'callback_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'advanced_mode' => ['type' => self::BOOL, 'default' => false],
			'active' => ['type' => self::BOOL, 'default' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'title' => true,
			'description' => true,
			'public_url' => true,
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'help_page_title.', '$page_id']
				]
			],
			'MasterDescription' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'help_page_desc.', '$page_id']
				]
			],
			'MasterTemplate' => [
				'entity' => 'XF:Template',
				'type' => self::TO_ONE,
				'conditions' => [
					['style_id', '=', 0],
					['type', '=', 'public'],
					['title', '=', '_help_page_', '$page_id']
				]
			],
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			]
		];
		$structure->options = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\HelpPage
	 */
	protected function getHelpPageRepo()
	{
		return $this->repository('XF:HelpPage');
	}
}