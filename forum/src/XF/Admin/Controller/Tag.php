<?php

namespace XF\Admin\Controller;

use XF\Mvc\Entity\Finder;
use XF\Mvc\ParameterBag;

class Tag extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('tags');
	}

	public function actionIndex(ParameterBag $params)
	{
		$page = $this->filterPage();
		$perPage = 50;

		$tagFinder = $this->finder('XF:Tag');

		$this->applyTagListFilters($tagFinder, $filters);

		$total = $tagFinder->total();
		$this->assertValidPage($page, $perPage, $total, 'tags');

		$tagFinder->limitByPage($page, $perPage);

		$viewParams = [
			'tags' => $tagFinder->fetch(),
			'filters' => $filters,

			'page' => $page,
			'perPage' => $perPage,
			'total' => $total
		];
		return $this->view('XF:Tag\Listing', 'tag_list', $viewParams);
	}

	protected function applyTagListFilters(Finder $finder, &$filters)
	{
		$filters = [];

		$containing = $this->filter('containing', 'str');
		if ($containing !== '')
		{
			$finder->where('tag', 'LIKE', $finder->escapeLike($containing, '%?%'));
			$filters['containing'] = $containing;
		}

		$order = $this->filter('order', 'str');
		switch ($order)
		{
			case 'use_count':
			case 'last_use_date':
				$finder->order($order, 'DESC');
				$filters['order'] = $order;
				break;

			default:
				$finder->order('tag');
		}
	}

	public function tagAddEdit(\XF\Entity\Tag $tag)
	{
		$viewParams = [
			'tag' => $tag
		];
		return $this->view('XF:Tag\Edit', 'tag_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$tag = $this->assertTagExists($params->tag_id);
		return $this->tagAddEdit($tag);
	}

	public function actionAdd()
	{
		$tag = $this->em()->create('XF:Tag');
		$tag->permanent = 1;

		return $this->tagAddEdit($tag);
	}

	protected function tagSaveProcess(\XF\Entity\Tag $tag)
	{
		$form = $this->formAction();

		$tag->setOption('admin_edit', true);

		$input = $this->filter([
			'tag' => 'str',
			'tag_url' => 'str',
			'permanent' => 'bool'
		]);
		$form->basicEntitySave($tag, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->tag_id)
		{
			$tag = $this->assertTagExists($params->tag_id);
		}
		else
		{
			$tag = $this->em()->create('XF:Tag');
		}

		$this->tagSaveProcess($tag)->run();

		return $this->redirect($this->buildLink('tags'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$tag = $this->assertTagExists($params->tag_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$tag,
			$this->buildLink('tags/delete', $tag),
			$this->buildLink('tags/edit', $tag),
			$this->buildLink('tags'),
			$tag->tag
		);
	}

	public function actionMerge(ParameterBag $params)
	{
		$tag = $this->assertTagExists($params->tag_id);
		if (!$tag->preDelete())
		{
			return $this->error($tag->getErrors());
		}

		if ($this->isPost())
		{
			$sourceTag = $tag;

			$targetTagName = $this->filter('target', 'str');
			$targetTag = $this->finder('XF:Tag')->where('tag', $targetTagName)->fetchOne();
			if (!$targetTag)
			{
				return $this->error(\XF::phrase('requested_tag_not_found'));
			}

			if ($sourceTag->tag_id == $targetTag->tag_id)
			{
				return $this->error(\XF::phrase('you_may_not_merge_tag_with_itself'));
			}

			/** @var \XF\Service\Tag\Merger $merger */
			$merger = $this->service('XF:Tag\Merger', $targetTag);
			$merger->merge($sourceTag);

			return $this->redirect($this->buildLink('tags'));
		}
		else
		{
			$viewParams = [
				'tag' => $tag
			];
			return $this->view('XF:Tag\Merge', 'tag_merge', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Tag
	 */
	protected function assertTagExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Tag', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Tag
	 */
	protected function getTagRepo()
	{
		return $this->repository('XF:Tag');
	}
}