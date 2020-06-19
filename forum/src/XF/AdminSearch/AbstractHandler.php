<?php

namespace XF\AdminSearch;

use XF\App;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

abstract class AbstractHandler
{
	/**
	 * @var string
	 */
	protected $contentType;

	/**
	 * @var App
	 */
	protected $app;

	public function __construct($contentType, App $app)
	{
		$this->contentType = $contentType;
		$this->app = $app;
	}

	abstract public function getDisplayOrder();

	/**
	 * @param string $text
	 * @param integer $limit
	 * @param array $previousMatchIds
	 *
	 * @return AbstractCollection
	 */
	abstract public function search($text, $limit, array $previousMatchIds = []);

	abstract public function getTemplateData(Entity $record);

	public function getTypeName()
	{
		return \XF::phrase(\XF::app()->getContentTypePhraseName($this->contentType, true));
	}

	public function isSearchable()
	{
		return true;
	}

	public function getRelatedPhraseGroups()
	{
		return [];
	}

	public function getTemplateName()
	{
		return 'admin:quick_search_type';
	}

	public function renderType(array $results, Templater $templater)
	{
		$typeName = $this->getTypeName();
		$template = $this->getTemplateName();

		$filtered = [];
		foreach ($results AS $key => $result)
		{
			$filtered[$key] = $this->getTemplateData($result);
		}

		return $templater->getTemplate($template, [
			'typeName' => $typeName,
			'results' => $filtered
		]);
	}
}