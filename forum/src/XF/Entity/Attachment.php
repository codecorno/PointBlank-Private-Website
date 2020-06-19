<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null attachment_id
 * @property int data_id
 * @property string content_type
 * @property int content_id
 * @property int attach_date
 * @property string temp_hash
 * @property bool unassociated
 * @property int view_count
 *
 * GETTERS
 * @property Entity|null Container
 * @property string filename
 * @property string extension
 * @property int file_size
 * @property bool has_thumbnail
 * @property string thumbnail_url
 * @property bool is_video
 * @property string video_url
 *
 * RELATIONS
 * @property \XF\Entity\AttachmentData Data
 */
class Attachment extends Entity
{
	public function canView(&$error = null)
	{
		if ($this->temp_hash || !$this->content_type)
		{
			return false;
		}

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $this->repository('XF:Attachment');
		$handler = $attachmentRepo->getAttachmentHandler($this->content_type);
		if (!$handler)
		{
			return false;
		}

		$container = $handler->getContainerEntity($this->content_id);
		if (!$container)
		{
			return false;
		}

		return $handler->canView($this, $container, $error);
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->Data ? $this->Data->filename : '';
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
		return $this->Data ? $this->Data->extension : '';
	}

	/**
	 * @return int
	 */
	public function getFileSize()
	{
		return $this->Data ? $this->Data->file_size : 0;
	}

	/**
	 * @return bool
	 */
	public function hasThumbnail()
	{
		return $this->Data ? $this->Data->hasThumbnail() : false;
	}

	/**
	 * @return string
	 */
	public function getThumbnailUrl()
	{
		return $this->Data ? $this->Data->getThumbnailUrl() : '';
	}

	/**
	 * @return bool
	 */
	public function isVideo()
	{
		return $this->Data ? $this->Data->isVideo() : false;
	}

	/**
	 * @return string
	 */
	public function getVideoUrl()
	{
		return $this->Data ? $this->Data->getVideoUrl() : '';
	}

	public function getContainerLink()
	{
		$container = $this->getContainer();
		if ($container)
		{
			$handler = $this->getHandler();
			return $handler ? $handler->getContainerLink($this->getContainer()) : null;
		}

		return null;
	}

	public function getContentTypePhrase()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContentTypePhrase() : null;
	}

	public function getHandler()
	{
		return $this->getAttachmentRepo()->getAttachmentHandler($this->content_type);
	}

	/**
	 * @return Entity|null
	 */
	public function getContainer()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContainerEntity($this->content_id) : null;
	}

	public function setContainer(Entity $content = null)
	{
		$this->_getterCache['Container'] = $content;
	}

	protected function _preSave()
	{
		if (!$this->content_id)
		{
			if (!$this->temp_hash)
			{
				throw new \LogicException('Temp hash must be specified if no content is specified.');
			}

			$this->unassociated = true;
		}
		else
		{
			$this->temp_hash = '';
			$this->unassociated = false;
		}
	}

	protected function _postSave()
	{
		if ($this->isInsert())
		{
			/** @var AttachmentData $data */
			$data = $this->Data;
			if ($data)
			{
				$data->fastUpdate('attach_count', $data->attach_count + 1);
			}
		}
	}

	protected function _preDelete()
	{
		if ($this->content_id)
		{
			/** @var \XF\Repository\Attachment $attachmentRepo */
			$attachmentRepo = $this->repository('XF:Attachment');
			$handler = $attachmentRepo->getAttachmentHandler($this->content_type);
			if ($handler)
			{
				$container = $handler->getContainerEntity($this->content_id);
				$handler->beforeAttachmentDelete($this, $container);
			}
		}
	}

	protected function _postDelete()
	{
		/** @var AttachmentData $data */
		$data = $this->Data;
		if ($data && $data->attach_count)
		{
			$data->fastUpdate('attach_count', $data->attach_count - 1);
		}

		if ($this->content_id)
		{
			/** @var \XF\Repository\Attachment $attachmentRepo */
			$attachmentRepo = $this->repository('XF:Attachment');
			$handler = $attachmentRepo->getAttachmentHandler($this->content_type);
			if ($handler)
			{
				$container = $handler->getContainerEntity($this->content_id);
				$handler->onAttachmentDelete($this, $container);
			}
		}
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @api-out str $filename
	 * @api-out int $file_size
	 * @api-out int $height
	 * @api-out int $width
	 * @api-out str $thumbnail_url
	 * @api-out str $video_url
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$result->filename = $this->filename;
		$result->file_size = $this->file_size;
		$result->height = $this->Data->height;
		$result->width = $this->Data->width;

		if ($this->has_thumbnail)
		{
			$result->thumbnail_url = $this->Data->getThumbnailUrl(true);
		}
		if ($this->is_video)
		{
			$result->video_url = $this->Data->getVideoUrl(true);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_attachment';
		$structure->shortName = 'XF:Attachment';
		$structure->primaryKey = 'attachment_id';
		$structure->columns = [
			'attachment_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'data_id' => ['type' => self::UINT, 'required' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'default' => '', 'api' => true],
			'content_id' => ['type' => self::UINT, 'default' => 0, 'api' => true],
			'attach_date' => ['type' => self::UINT, 'default' => \XF::$time, 'api' => true],
			'temp_hash' => ['type' => self::STR, 'maxLength' => 32, 'default' => ''],
			'unassociated' => ['type' => self::BOOL, 'default' => true],
			'view_count' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'api' => true]
		];
		$structure->getters = [
			'Container' => true,

			'filename' => ['getter' => 'getFilename', 'cache' => false],
			'extension' => ['getter' => 'getExtension', 'cache' => false],
			'file_size' => ['getter' => 'getFileSize', 'cache' => false],
			'has_thumbnail' => ['getter' => 'hasThumbnail', 'cache' => false],
			'thumbnail_url' => ['getter' => 'getThumbnailUrl', 'cache' => false],
			'is_video' => ['getter' => 'isVideo', 'cache' => false],
			'video_url' => ['getter' => 'getVideoUrl', 'cache' => false]
		];
		$structure->relations = [
			'Data' => [
				'entity' => 'XF:AttachmentData',
				'type' => self::TO_ONE,
				'conditions' => 'data_id',
				'primary' => true
			],
		];
		$structure->defaultWith = ['Data'];
		$structure->withAliases = [
			'api' => []
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Attachment
	 */
	protected function getAttachmentRepo()
	{
		return $this->repository('XF:Attachment');
	}
}