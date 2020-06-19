<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Repository;
use XF\Mvc\Entity\Structure;
use XF\Util\Arr;

/**
 * COLUMNS
 * @property string option_id
 * @property string option_value_
 * @property string default_value_
 * @property string edit_format
 * @property string edit_format_params
 * @property string data_type
 * @property array sub_options
 * @property string validation_class
 * @property string validation_method
 * @property string addon_id
 *
 * GETTERS
 * @property array formatParams
 * @property array|string|null option_value
 * @property array|string|null default_value
 * @property \XF\Phrase title
 * @property \XF\Phrase explain
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\OptionGroupRelation[] Relations
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterExplain
 */
class Option extends Entity
{
	public function canEdit()
	{
		return \XF::$developmentMode;
	}

	public function isDataTypeNumeric()
	{
		switch ($this->data_type)
		{
			case 'integer':
			case 'numeric':
			case 'unsigned_integer':
			case 'unsigned_numeric':
			case 'positive_integer':
				return true;
			default:
				return false;
		}
	}

	public function renderDisplayCallback(array $htmlParams)
	{
		if ($this->edit_format != 'callback')
		{
			return '';
		}

		$callback = $this->getFormatParams();
		if (!isset($callback['class']) || !isset($callback['method']))
		{
			return '';
		}

		if (!\XF\Util\Php::validateCallbackPhrased($callback['class'], $callback['method'], $error))
		{
			return '';
		}

		return call_user_func([$callback['class'], $callback['method']], $this, $htmlParams);
	}

	public function getDisplayCallbackError()
	{
		if ($this->edit_format != 'callback')
		{
			return "$this->option_id - Not a callback option.";
		}

		$callback = $this->getFormatParams();
		if (!isset($callback['class']) || !isset($callback['method']))
		{
			return "$this->option_id - No callback found.";
		}

		if (!\XF\Util\Php::validateCallbackPhrased($callback['class'], $callback['method'], $error))
		{
			return "$this->option_id - " . $error;
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function getFormatParams()
	{
		$formatParams = [];

		switch ($this->edit_format)
		{
			case 'template':
				if (strpos($this->edit_format_params, '=') === false)
				{
					$formatParams = ['template' => $this->edit_format_params];
					break;
				}
				// fall through if params have an '='

			case 'textbox':
			case 'textarea':
			case 'spinbox':
			case 'radio':
			case 'select':
			case 'checkbox':
			case 'onofftextbox':
			case 'username':
				$formatParams = $this->app()->stringFormatter()->createKeyValueSetFromString($this->edit_format_params);
				break;

			case 'callback':
				$callback = explode('::', $this->edit_format_params);
				if (count($callback) == 2)
				{
					$formatParams = ['class' => $callback[0], 'method' => $callback[1]];
				}
				break;
		}

		return $formatParams;
	}

	/**
	 * @return array|string|null
	 */
	public function getOptionValue()
	{
		$v = $this->getValue('option_value');

		if ($this->data_type == 'array')
		{
			$value = json_decode($v, true);
			if (!is_array($value))
			{
				$value = [];
			}

			return $value;
		}
		else
		{
			return $v;
		}
	}

	/**
	 * @return array|string|null
	 */
	public function getDefaultValue()
	{
		$v = $this->getValue('default_value');

		if ($this->data_type == 'array')
		{
			$value = json_decode($v, true);
			if (!is_array($value))
			{
				$value = [];
			}

			return $value;
		}
		else
		{
			return $v;
		}
	}

	protected function verifyOptionValue(&$optionValue)
	{
		$this->prepareUsernameOptionValue($optionValue);

		$optionValue = $this->castOptionValue($optionValue);

		$validationClass = $this->validation_class;
		$validationMethod = $this->validation_method;

		if ($validationClass && $validationMethod && $this->getOption('verify_value'))
		{
			if (!\XF\Util\Php::validateCallbackPhrased($validationClass, $validationMethod, $error))
			{
				$this->error($error, 'option_value');
				return false;
			}

			$success = (boolean)call_user_func_array(
				[$validationClass, $validationMethod],
				[&$optionValue, $this, $this->option_id]
			);
			if (!$success)
			{
				return false;
			}
		}

		if ($this->data_type == 'array')
		{
			$newOptionValue = $this->validateArraySubOptions($optionValue, $subOptionError);
			if ($subOptionError)
			{
				$this->error($subOptionError, 'sub_options');
				return false;
			}

			$optionValue = json_encode($newOptionValue);
		}

		return true;
	}

	protected function prepareUsernameOptionValue(&$optionValue)
	{
		if ($this->edit_format != 'username')
		{
			return;
		}

		switch ($this->data_type)
		{
			case 'string':
				// store as is, as a username string
				return;

			case 'integer':
			case 'numeric':
			case 'unsigned_integer':
			case 'unsigned_numeric':
			case 'positive_integer':
				if ($optionValue)
				{
					$user = $this->em()->findOne('XF:User', ['username' => $optionValue]);
					if (!$user)
					{
						$this->error(\XF::phrase('requested_user_x_not_found', ['name' => $optionValue]), 'option_value');
						$optionValue = [];
						return;
					}
					$optionValue = $user->user_id;
				}
				else
				{
					$optionValue = 0;
				}
				return;

			case 'array':
				/** @var \XF\Repository\User $userRepo */
				$userRepo = $this->repository('XF:User');

				$usernames = Arr::stringToArray($optionValue, '#\s*,\s*#');
				if ($usernames)
				{
					$users = $userRepo->getUsersByNames($usernames, $notFound, [], false);

					if ($notFound)
					{
						$this->error(\XF::phrase('following_users_not_found_x',
							['usernames' => implode(', ', $notFound)]
						), 'option_value');
						$optionValue = [];
						return;
					}

					$optionValue = $users->keys();
				}
				else
				{
					$optionValue = [];
				}
				return;
		}
	}

	protected function castOptionValue($optionValue)
	{
		switch ($this->data_type)
		{
			case 'string':  $optionValue = strval($optionValue); break;
			case 'integer': $optionValue = intval($optionValue); break;
			case 'numeric': $optionValue = strval(floatval($optionValue)) + 0; break;
			case 'boolean': $optionValue = ($optionValue ? 1 : 0); break;

			case 'array':
				if ($optionValue === false || $optionValue === null)
				{
					// for all checkbox options, this can happen so allow it
					$optionValue = [];
				}
				else if (!is_array($optionValue))
				{
					throw new \LogicException("Only arrays can be set to array type options");
				}
				break;

			case 'unsigned_integer':
				$optionValue = max(0, intval($optionValue));
				break;

			case 'unsigned_numeric':
				$optionValue = strval(floatval($optionValue)) + 0;
				$optionValue = max(0, $optionValue);
				break;

			case 'positive_integer':
				$optionValue = max(1, intval($optionValue));
				break;

			default:
				throw new \LogicException("Unknown option data type $this->data_type");
		}

		return $optionValue;
	}

	protected function validateArraySubOptions(array $optionValue, &$error)
	{
		$error = null;
		$newOptionValue = [];
		$allowAny = false;

		foreach ($this->sub_options AS $subOption)
		{
			$subOption = trim($subOption);
			if ($subOption === '')
			{
				continue;
			}

			if ($subOption == '*')
			{
				$allowAny = true;
			}
			else if (!isset($optionValue[$subOption]))
			{
				$newOptionValue[$subOption] = false;
			}
			else
			{
				$newOptionValue[$subOption] = $optionValue[$subOption];
				unset($optionValue[$subOption]);
			}
		}

		if ($allowAny)
		{
			// allow any keys, so bring all the remaining ones over
			$newOptionValue += $optionValue;
		}
		else if (count($optionValue) > 0)
		{
			$error = \XF::phrase('following_sub_options_unknown_x', [
				'subOptions' => implode(', ', array_keys($optionValue))]
			);
		}

		return $newOptionValue;
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
	public function getExplain()
	{
		return \XF::phrase($this->getPhraseName(false));
	}

	public function getPhraseName($title)
	{
		return 'option' . ($title ? '' : '_explain') . '.' . $this->option_id;
	}

	public function getMasterPhrase($title)
	{
		$phrase = $title ? $this->MasterTitle : $this->MasterExplain;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
			$phrase->title = $this->_getDeferredValue(function() use($title) { return $this->getPhraseName($title); });
			$phrase->language_id = 0;
		}

		return $phrase;
	}

	public function updateRelations(array $relationMap)
	{
		if (!$this->exists())
		{
			throw new \LogicException("Option must be saved first");
		}

		$optionId = $this->option_id;
		$insert = [];
		foreach ($relationMap AS $groupId => $displayOrder)
		{
			$insert[] = [
				'option_id' => $optionId,
				'group_id' => $groupId,
				'display_order' => $displayOrder
			];
		}

		$db = $this->db();
		$db->delete('xf_option_group_relation', 'option_id = ?', $this->option_id);
		if ($insert)
		{
			$db->insertBulk('xf_option_group_relation', $insert);
		}

		unset($this->_relations['Relations']);

		// Ensure the option relations get written to dev output.
		if ($this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output'))
		{
			$devOutput = $this->app()->developmentOutput();
			$devOutput->export($this);
		}
	}

	public function validateDefaultValue($defaultValue, &$error = null)
	{
		$error = null;

		if ($this->data_type === 'array')
		{
			if (!is_array($defaultValue))
			{
				$defaultValue = json_decode($defaultValue, true);
				if (!is_array($defaultValue))
				{
					$defaultValue = [];
				}
			}
		}

		$defaultValue = $this->castOptionValue($defaultValue);

		if ($this->data_type === 'array')
		{
			$defaultValue = $this->validateArraySubOptions($defaultValue, $subOptionsError);
			if ($subOptionsError)
			{
				$error = $subOptionsError;
			}

			$defaultValue = json_encode($defaultValue);
		}

		return strval($defaultValue);
	}

	protected function _preSave()
	{
		if ($this->getOption('verify_validation_callback')
			&& ($this->validation_class || $this->validation_method)
			&& ($this->isChanged('validation_class') || $this->isChanged('validation_method')))
		{
			if (!\XF\Util\Php::validateCallbackPhrased($this->validation_class, $this->validation_method, $error))
			{
				$this->error($error, 'validation_method');
			}
		}

		if ($this->isChanged('data_type') || $this->isChanged('edit_format'))
		{
			$this->validateDataTypeEditFormat($this->data_type, $this->edit_format);
		}

		if ($this->isChanged('edit_format_params'))
		{
			$this->validateEditFormatParams($this->edit_format, $this->edit_format_params);
		}

		if ($this->data_type == 'array' && !$this->sub_options)
		{
			if ($this->edit_format == 'username')
			{
				$this->sub_options = ['*'];
			}
			else
			{
				$this->error(\XF::phrase('please_enter_list_of_sub_options_for_this_array'), 'sub_options');
			}
		}
		else if ($this->data_type != 'array')
		{
			$this->sub_options = [];
		}

		if ($this->isChanged(['default_value', 'sub_options', 'data_type']))
		{
			$defaultValue = $this->validateDefaultValue($this->getValue('default_value'), $defaultValueError);
			if ($defaultValueError && \XF::$developmentMode)
			{
				$this->error(
					\XF::string([\XF::phrase('default_value:'), $defaultValueError]),
					'default_value'
				);
			}
			else
			{
				$this->default_value = $defaultValue;
			}
		}

		if ($this->isInsert() && !$this->isChanged('option_value'))
		{
			$this->_setInternal('option_value', $this->getValue('default_value'));
		}

		if ($this->isUpdate()
			&& $this->_newValues
			&& count($this->_newValues) === 1
			&& $this->isChanged('option_value')
		)
		{
			// only the option value has changed so prevent dev output from being written

			/** @var \XF\Behavior\DevOutputWritable $devOutputWritable */
			$devOutputWritable = $this->getBehavior('XF:DevOutputWritable');
			$devOutputWritable->setOption('write_dev_output', false);
		}
	}

	protected function validateDataTypeEditFormat($dataType, $editFormat)
	{
		switch ($editFormat)
		{
			case 'callback':
			case 'template':
				// can be anything
				break;

			case 'checkbox':
			case 'onofftextbox':
				if ($dataType != 'array')
				{
					$this->error(\XF::phrase('please_select_data_type_array_if_you_want_to_allow_multiple_selections'), 'data_type');
					return false;
				}
				break;

			case 'textbox':
			case 'spinbox':
			case 'onoff':
			case 'radio':
			case 'select':
				if ($dataType == 'array')
				{
					$this->error(\XF::phrase('please_select_data_type_other_than_array_if_you_want_to_allow_single'), 'data_type');
					return false;
				}
				break;
		}

		return true;
	}

	protected function validateEditFormatParams($editFormat, $editFormatParams)
	{
		switch ($editFormat)
		{
			case 'template':
			{
				if (!preg_match('/^\w+$/i', $editFormatParams) && !preg_match('/template\s*=\s*\w+/i', $editFormatParams))
				{
					$this->error(\XF::phrase('template_edit_format_params_invalid'), 'data_type');
					return false;
				}
				break;
			}
		}

		return true;
	}

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			if ($this->isChanged('addon_id') || $this->isChanged('option_id'))
			{
				$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

				/** @var Phrase $titlePhrase */
				$titlePhrase = $this->getExistingRelation('MasterTitle');
				if ($titlePhrase)
				{
					$titlePhrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$titlePhrase->addon_id = $this->addon_id;
					$titlePhrase->title = $this->getPhraseName(true);
					$titlePhrase->save();
				}

				/** @var Phrase $explainPhrase */
				$explainPhrase = $this->getExistingRelation('MasterExplain');
				if ($explainPhrase)
				{
					$explainPhrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$explainPhrase->addon_id = $this->addon_id;
					$explainPhrase->title = $this->getPhraseName(false);
					$explainPhrase->save();
				}
			}

			if ($this->isChanged('option_id'))
			{
				$this->db()->update('xf_option_group_relation',
					['option_id' => $this->option_id],
					'option_id = ?', $this->getExistingValue('option_id')
				);
			}
		}

		$this->rebuildOptionCache();
	}

	protected function _postDelete()
	{
		$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

		$titlePhrase = $this->MasterTitle;
		if ($titlePhrase)
		{
			$titlePhrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$titlePhrase->delete();
		}
		$explainPhrase = $this->MasterExplain;
		if ($explainPhrase)
		{
			$explainPhrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$explainPhrase->delete();
		}

		$this->db()->delete('xf_option_group_relation', 'option_id = ?', $this->option_id);

		$this->rebuildOptionCache();
	}

	protected function rebuildOptionCache()
	{
		$repo = $this->getOptionRepo();

		\XF::runOnce('optionCacheRebuild', function() use ($repo)
		{
			$repo->rebuildOptionCache();
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
		$structure->table = 'xf_option';
		$structure->shortName = 'XF:Option';
		$structure->primaryKey = 'option_id';
		$structure->columns = [
			'option_id' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_option_id',
				'unique' => 'option_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'option_value' => ['type' => self::BINARY, 'default' => ''],
			'default_value' => ['type' => self::BINARY, 'default' => ''],
			'edit_format' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['textbox', 'spinbox', 'onoff', 'onofftextbox', 'radio', 'select', 'checkbox', 'template', 'callback', 'username']
			],
			'edit_format_params' => ['type' => self::STR, 'default' => ''],
			'data_type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['string', 'integer', 'numeric', 'array', 'boolean', 'positive_integer', 'unsigned_integer', 'unsigned_numeric']
			],
			'sub_options' => ['type' => self::LIST_LINES, 'default' => ''],
			'validation_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'validation_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'formatParams' => true,
			'option_value' => true,
			'default_value' => true,
			'title' => true,
			'explain' => true
		];
		$structure->relations = [
			'Relations' => [
				'entity' => 'XF:OptionGroupRelation',
				'type' => self::TO_MANY,
				'conditions' => 'option_id',
				'key' => 'group_id'
			],
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
					['title', '=', 'option.', '$option_id']
				]
			],
			'MasterExplain' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'option_explain.', '$option_id']
				]
			]
		];
		$structure->options = [
			'verify_validation_callback' => true,
			'verify_value' => true
		];

		return $structure;
	}

	/**
	 * @return Repository\Option
	 */
	protected function getOptionRepo()
	{
		return $this->_em->getRepository('XF:Option');
	}
}