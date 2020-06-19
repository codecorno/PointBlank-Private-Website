<?php

namespace XF\Import\Data;

abstract class AbstractField extends AbstractEmulatedData
{
	protected $title = '';
	protected $description = '';

	public function setTitle($title, $description = null)
	{
		$this->title = $title;
		if ($description !== null)
		{
			$this->description = $description;
		}
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function setChoices(array $choices)
	{
		foreach ($choices AS &$value)
		{
			if (is_string($value))
			{
				$value = $this->convertToUtf8($value);
			}
		}

		$this->field_choices = $choices;
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Entity\AbstractField $field */
		$field = $this->em()->find($this->getEntityShortName(), $newId);
		if ($field)
		{
			$this->insertMasterPhrase($field->getPhraseName(true), $this->title, [], true);
			$this->insertMasterPhrase($field->getPhraseName(false), $this->description, [], true);

			$choices = $this->field_choices;
			if ($choices)
			{
				foreach ($choices AS $choice => $value)
				{
					$this->insertMasterPhrase($field->getChoicePhraseName($choice), $value, [], true);
				}
			}

			$this->em()->detachEntity($field);
		}

		/** @var \XF\Repository\AbstractField $repo */
		$repo = $this->repository($this->getEntityShortName());

		\XF::runOnce('rebuildFieldImport-' . $this->getEntityShortName(), function() use ($repo)
		{
			$repo->rebuildFieldCache();
		});
	}
}