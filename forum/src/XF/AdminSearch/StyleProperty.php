<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class StyleProperty extends AbstractHandler
{
	protected $styleId = 0;

	public function getDisplayOrder()
	{
		return 55;
	}

	public function search($text, $limit, array $previousMatchIds = [])
	{
		$styleId = $this->getSearchStyleId();
		$this->styleId = $styleId;

		$finder = $this->app->finder('XF:StylePropertyMap');

		$conditions = [
			[$finder->caseInsensitive('property_name'), 'like', $finder->escapeLike($text, '%?%')]
		];
		if ($previousMatchIds)
		{
			$conditions[] = ['property_name', $previousMatchIds];
		}

		$finder
			->where('style_id', $styleId)
			->whereOr($conditions)
			->with('Property', true)
			->order($finder->caseInsensitive('property_name'))
			->limit($limit);

		return $finder->fetch()->pluckNamed('Property', 'property_id');
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

	public function getTemplateData(Entity $record)
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app->container('router.admin');

		return [
			'link' => $router->buildLink('style-properties/view', $record, ['style_id' => $this->styleId]),
			'title' => $record->title,
			'extra' => $record->property_name
		];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('style');
	}

	public function getRelatedPhraseGroups()
	{
		return ['style_prop'];
	}
}