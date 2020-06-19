<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class PublicTemplate extends AbstractHandler
{
	protected $styleId = 0;

	public function getDisplayOrder()
	{
		return 60;
	}

	public function search($text, $limit, array $previousMatchIds = [])
	{
		$styleId = $this->getSearchStyleId();
		$this->styleId = $styleId;

		$finder = $this->app->finder('XF:TemplateMap');

		$conditions = [
			[$finder->caseInsensitive('title'), 'like', $finder->escapeLike($text, '%?%')]
		];
		if ($previousMatchIds)
		{
			$conditions[] = ['template_id', $previousMatchIds];
		}

		$finder
			->where('style_id', $styleId)
			->where('type', $this->getSearchTemplateType())
			->whereOr($conditions)
			->with('Template', true)
			->order($finder->caseInsensitive('title'))
			->limit($limit);

		return $finder->fetch()->pluckNamed('Template', 'template_id');
	}

	protected function getSearchStyleId()
	{
		// TODO: this is duplicated from the template controller, not ideal
		$styleId = $this->app->request()->getCookie('edit_style_id', null);
		if ($styleId === null)
		{
			$styleId = \XF::$developmentMode ? 0 : $this->app->options()->defaultStyleId;
		}
		$styleId = intval($styleId);

		if ($styleId == 0 && !\XF::$developmentMode)
		{
			$styleId = $this->app->options()->defaultStyleId;
		}

		return $styleId;
	}

	protected function getSearchTemplateType()
	{
		return 'public';
	}

	public function getTemplateData(Entity $record)
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app->container('router.admin');

		return [
			'link' => $router->buildLink('templates/edit', $record, ['style_id' => $this->styleId]),
			'title' => $record->title
		];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('style');
	}
}