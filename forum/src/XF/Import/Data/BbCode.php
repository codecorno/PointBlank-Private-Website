<?php

namespace XF\Import\Data;

class BbCode extends AbstractEmulatedData
{
	protected $title = '';
	protected $example = '';

	public function getImportType()
	{
		return 'bb_code';
	}

	public function getEntityShortName()
	{
		return 'XF:BbCode';
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setExample($example)
	{
		$this->example = $example;
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Entity\BbCode $bbCode */
		$bbCode = $this->em()->find('XF:BbCode', $newId);
		if ($bbCode)
		{
			$this->insertMasterPhrase($bbCode->getPhraseName(), $this->title);
			$this->insertMasterPhrase($bbCode->getPhraseName('example'), $this->example);

			$this->em()->detachEntity($bbCode);
		}

		/** @var \XF\Repository\BbCode $repo */
		$repo = $this->repository('XF:BbCode');

		\XF::runOnce('rebuildBbCodeCache', function() use ($repo)
		{
			$repo->rebuildBbCodeCache();
		});
	}
}