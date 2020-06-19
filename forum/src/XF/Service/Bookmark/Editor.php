<?php

namespace XF\Service\Bookmark;

use XF\Entity\BookmarkItem;
use XF\Service\AbstractService;
use XF\Service\ValidateAndSavableTrait;

class Editor extends AbstractService
{
	use ValidateAndSavableTrait;

	/**
	 * @var BookmarkItem
	 */
	protected $bookmark;

	/**
	 * @var Preparer
	 */
	protected $preparer;

	/**
	 * @var \XF\Service\Bookmark\LabelChanger
	 */
	protected $labelChanger;

	public function __construct(\XF\App $app, BookmarkItem $bookmark)
	{
		parent::__construct($app);

		$this->bookmark = $bookmark;
		$this->preparer = $this->service('XF:Bookmark\Preparer', $this->bookmark);
		$this->labelChanger = $this->service('XF:Bookmark\LabelChanger', $this->bookmark, $this->bookmark->User);
	}

	public function getBookmark()
	{
		return $this->bookmark;
	}

	public function getBookmarkPreparer()
	{
		return $this->preparer;
	}

	public function setMessage($message, $format = true)
	{
		return $this->preparer->setMessage($message, $format);
	}

	public function setLabels($labels)
	{
		$this->labelChanger->setLabels($labels);
	}

	protected function finalSetup()
	{
	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->bookmark->preSave();
		return $this->bookmark->getErrors();
	}

	protected function _save()
	{
		$bookmark = $this->bookmark;

		$this->db()->beginTransaction();

		$bookmark->save();
		$this->preparer->afterUpdate();
		$this->labelChanger->save();

		$this->db()->commit();

		return $bookmark;
	}
}