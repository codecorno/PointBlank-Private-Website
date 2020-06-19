<?php

namespace XF\Import\Data;

class Attachment extends AbstractEmulatedData
{
	/**
	 * @var \XF\Entity\AttachmentData|null
	 */
	protected $data;

	/**
	 * @var \XF\FileWrapper|null
	 */
	protected $sourceFile;

	protected $dataExtras = [];

	protected $dataUserId = null;

	/**
	 * @var callable|null
	 */
	protected $containerCallback;

	public function getImportType()
	{
		return 'attachment';
	}

	public function getEntityShortName()
	{
		return 'XF:Attachment';
	}

	public function setDataUserId($userId)
	{
		$this->dataUserId = $userId;
	}

	public function setDataExtra($key, $value)
	{
		$this->dataExtras[$key] = $value;
	}

	public function setSourceFile($sourceFile, $fileName = '')
	{
		$fileName = $this->convertToUtf8($fileName);

		$this->sourceFile = new \XF\FileWrapper($sourceFile, $fileName);
	}

	public function setContainerCallback(callable $callback)
	{
		$this->containerCallback = $callback;
	}

	protected function write($oldId)
	{
		if (!$this->data)
		{
			/** @var \XF\Service\Attachment\Preparer $attachPreparer */
			$attachPreparer = $this->app()->service('XF:Attachment\Preparer');
			$this->data = $attachPreparer->insertDataFromFile($this->sourceFile, $this->dataUserId, $this->dataExtras);
		}

		$this->ee->set('data_id', $this->data->data_id);

		return $this->ee->insert($oldId, $this->db());
	}

	protected function preSave($oldId)
	{
		if (!$this->sourceFile)
		{
			throw new \LogicException("Must set a source file");
		}
		if ($this->dataUserId === null)
		{
			throw new \LogicException("Must set a data user ID (can be 0)");
		}
	}

	protected function postSave($oldId, $newId)
	{
		$this->data->fastUpdate('attach_count', 1);

		$attachment = $this->em()->find('XF:Attachment', $newId);
		if ($attachment && $attachment->Container)
		{
			/** @var \XF\Mvc\Entity\Entity $container */
			$container = $attachment->Container;
			if (isset($container->attach_count))
			{
				$container->attach_count++;
			}

			if ($this->containerCallback)
			{
				$callback = $this->containerCallback;
				$callback($container, $attachment, $oldId, $this->dataExtras);
			}

			$container->saveIfChanged($saved, false, false);
			$this->em()->detachEntity($attachment);
			$this->em()->detachEntity($container);
		}
	}
}