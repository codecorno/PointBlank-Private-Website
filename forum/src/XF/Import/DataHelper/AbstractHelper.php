<?php

namespace XF\Import\DataHelper;

abstract class AbstractHelper
{
	/**
	 * @var \XF\Import\DataManager
	 */
	protected $dataManager;

	public function __construct(\XF\Import\DataManager $dataManager)
	{
		$this->dataManager = $dataManager;
	}

	protected function db()
	{
		return $this->dataManager->db();
	}

	protected function em()
	{
		return $this->dataManager->em();
	}
}