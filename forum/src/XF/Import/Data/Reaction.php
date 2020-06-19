<?php

namespace XF\Import\Data;

class Reaction extends AbstractEmulatedData
{
	protected $title;

	protected $sourceFile;
	protected $filename;

	public function getImportType()
	{
		return 'reaction';
	}

	public function getEntityShortName()
	{
		return 'XF:Reaction';
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setSourceImagePath($sourceFile, $filename)
	{
		$this->sourceFile = $sourceFile;
		$this->filename = $filename;

		$this->image_url = "data/imported_reactions/$filename";
	}

	protected function preSave($oldId)
	{
		if (!$this->image_url)
		{
			$this->image_url = 'styles/default/xenforo/missing-image.png';
		}
	}

	protected function postSave($oldId, $newId)
	{
		$this->insertMasterPhrase('reaction_title.' . $newId, $this->title);

		if ($this->sourceFile)
		{
			$image = $this->app()->imageManager()->imageFromFile($this->sourceFile);
			$image->resizeAndCrop(32, 32);

			$newTempFile = \XF\Util\File::getTempFile();
			if ($newTempFile && $image->save($newTempFile))
			{
				\XF\Util\File::copyFileToAbstractedPath($newTempFile, 'data://imported_reactions/' . $this->filename);
			}
		}

		/** @var \XF\Repository\Reaction $repo */
		$repo = $this->repository('XF:Reaction');

		\XF::runOnce('rebuildReactionImport', function() use ($repo)
		{
			$repo->rebuildReactionCache();
			$repo->rebuildReactionSpriteCache();
		});
	}
}