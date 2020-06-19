<?php

namespace XF\Admin\Controller;

use XF\Diff;
use XF\Diff3;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class Template extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('style');
	}

	public function actionIndex()
	{
		$style = $this->plugin('XF:Style')->getActiveEditStyle();

		$type = $this->filter('type', 'string');
		if (!$type)
		{
			$type = 'public';
		}

		return $this->redirect($this->buildLink('styles/templates', $style, ['type' => $type]));
	}

	protected function templateAddEdit(\XF\Entity\Template $template)
	{
		if ($template->exists() && !$this->request->exists('style_id'))
		{
			$styleId = $template->style_id;
		}
		else
		{
			$styleId = $this->filter('style_id', 'uint');

			if (empty($template->title) && $prefix = $this->filter('prefix', 'str'))
			{
				$template->set('title', $prefix);
			}
		}

		$style = $this->assertStyleExists($styleId);
		if (!$style->canEdit())
		{
			return $this->error(\XF::phrase('templates_in_this_style_can_not_be_modified'));
		}

		$types = $this->getTemplateRepo()->getTemplateTypes($style);
		if (!isset($types[$template->type]))
		{
			return $this->error(\XF::phrase('templates_in_this_style_can_not_be_modified'));
		}

		if (!$template->exists() && $style->style_id)
		{
			$template->addon_id = '';
		}

		$viewParams = [
			'template' => $template,
			'hasHistory' => $template->exists() ? $template->History->count() : false,
			'style' => $style,
			'styleTree' => $this->getStyleRepo()->getStyleTree(),
			'types' => $types,
			'redirect' => $this->getDynamicRedirect()
		];
		return $this->view('XF:Template\Edit', 'template_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$template = $this->assertTemplateExists($params->template_id);
		return $this->templateAddEdit($template);
	}

	public function actionAdd()
	{
		$template = $this->em()->create('XF:Template');
		$template->type = $this->filter('type', 'str', 'public');
		return $this->templateAddEdit($template);
	}

	public function actionCodeEditorModeLoader()
	{
		$language = $this->filter('language', 'str');

		/** @var \XF\ControllerPlugin\CodeEditor $plugin */
		$plugin = $this->plugin('XF:CodeEditor');

		return $plugin->actionModeLoader($language);
	}

	protected function templateSaveProcess(\XF\Entity\Template $template)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'style_id' => 'uint',
			'type' => 'str',
			'title' => 'str',
			'template' => 'str',
			'addon_id' => 'str'
		]);

		$form->setup(function() use ($template)
		{
			if ($template->style_id > 0)
			{
				// force an update to resolve any out of date issues
				$template->updateVersionId();
				$template->last_edit_date = \XF::$time;
			}
		});

		$form->basicEntitySave($template, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->template_id)
		{
			$template = $this->assertTemplateExists($params->template_id);

			$styleId = $this->filter('style_id', 'uint');
			if ($template->style_id != $styleId)
			{
				$template = $this->finder('XF:Template')->where([
					'style_id' => $styleId,
					'title' => $template->title,
					'type' => $template->type
				])->fetchOne();
				if (!$template)
				{
					$template = $this->em()->create('XF:Template');
				}
			}
		}
		else
		{
			$template = $this->em()->create('XF:Template');
		}

		$this->templateSaveProcess($template)->run();

		$dynamicRedirect = $this->getDynamicRedirect('invalid', false);
		if ($dynamicRedirect == 'invalid' || !preg_match('#(styles|templates)/#', $dynamicRedirect))
		{
			$dynamicRedirect = null;
		}

		if ($this->request->exists('exit'))
		{
			if ($dynamicRedirect)
			{
				$redirect = $dynamicRedirect;
			}
			else
			{
				$redirect = $this->buildLink('styles/templates', $template->Style, ['type' => $template->type]);
			}
			$redirect .= $this->buildLinkHash($template->template_id);
		}
		else
		{
			$redirect = $this->buildLink('templates/edit', $template, ['_xfRedirect' => $dynamicRedirect]);
		}

		return $this->redirect($redirect);
	}

	public function actionHistory(ParameterBag $params)
	{
		$template = $this->assertTemplateExists($params->template_id);

		$oldId = $this->filter('old', 'uint');
		$newId = $this->filter('new', 'uint');

		$list = [];

		if ($template->History)
		{
			$list = $template->History;
			$list = $list->toArray();
			arsort($list);
		}
		$newestHistory = reset($list);

		if ($oldId)
		{
			// doing a comparison
			$oldText = isset($list[$oldId]) ? $list[$oldId]['template'] : '';

			if ($newId)
			{
				$newText = isset($list[$newId]) ? $list[$newId]['template'] : '';
			}
			else
			{
				$newText = $template['template'];
			}

			$diff = new Diff();
			$diffs = $diff->findDifferences($oldText, $newText);
		}
		else
		{
			$diffs = [];
		}

		$viewId = $this->filter('view', 'uint');
		if ($viewId)
		{
			$history = isset($list[$viewId]) ? $list[$viewId] : false;
		}
		else
		{
			$history = false;
		}

		$viewParams = [
			'template' => $template,
			'list' => $list,
			'oldId' => ($oldId ? $oldId : ($newestHistory ? $newestHistory['template_history_id'] : 0)),
			'newId' => $newId,
			'diffs' => $diffs,
			'history' => $history
		];

		if ($history)
		{
			return $this->view('XF:Template\History\View', 'template_history_view', $viewParams);
		}
		else if ($oldId)
		{
			return $this->view('XF:Template\History\Compare', 'template_history_compare', $viewParams);
		}
		else
		{
			return $this->view('XF:Template\History', 'template_history', $viewParams);
		}
	}

	public function actionDelete(ParameterBag $params)
	{
		$template = $this->assertTemplateExists($params->template_id);
		if (!$template->Style || !$template->Style->canEdit())
		{
			return $this->error(\XF::phrase('templates_in_this_style_can_not_be_modified'));
		}

		if ($this->isPost())
		{
			$template->delete();

			$redirect = $this->getDynamicRedirect('invalid', false);
			if ($redirect == 'invalid' || !preg_match('#(styles|templates)/#', $redirect))
			{
				$redirect = $this->buildLink('styles/templates', $template->Style, ['type' => $template->type]);
			}
			return $this->redirect($redirect);
		}
		else
		{
			$viewParams = [
				'template' => $template
			];
			return $this->view('XF:Template\Delete', 'template_delete', $viewParams);
		}
	}

	protected function filterSearchConditions()
	{
		return $this->filter([
			'addon_id' => 'str',
			'type' => 'str',
			'title' => 'str',
			'template' => 'str',
			'template_cs' => 'bool',
			'state' => 'array-str'
		]);
	}

	protected function getTemplateSearchFinder(\XF\Entity\Style $style, array &$linkParams = [])
	{
		$conditions = $this->filterSearchConditions();

		$templateRepo = $this->getTemplateRepo();

		$availableTypes = $templateRepo->getTemplateTypes($style);
		if (!isset($availableTypes[$conditions['type']]))
		{
			$conditions['type'] = 'public';
		}

		$finder = $templateRepo->findEffectiveTemplatesInStyle($style, $conditions['type']);

		$finder->Template
			->searchTitle($conditions['title'])
			->searchTemplate($conditions['template'], $conditions['template_cs'])
			->fromAddOn($conditions['addon_id']);

		$finder->isTemplateState($conditions['state']);

		if ($conditions['type'])
		{
			$linkParams['type'] = $conditions['type'];
		}
		if ($conditions['title'])
		{
			$linkParams['title'] = $conditions['title'];
		}
		if ($conditions['template'])
		{
			$linkParams['template'] = $conditions['template'];
			$linkParams['template_cs'] = $conditions['template_cs'];
		}
		if ($conditions['addon_id'])
		{
			$linkParams['addon_id'] = $conditions['addon_id'];
		}
		if ($conditions['state'])
		{
			$linkParams['state'] = $conditions['state'];
		}

		return $finder;
	}

	public function actionSearch()
	{
		$this->setSectionContext('searchTemplates');

		$styleRepo = $this->getStyleRepo();

		if ($this->filter('search', 'uint'))
		{
			$style = $this->assertStyleExists($this->filter('style_id', 'uint'));
			if (!$style->canEdit())
			{
				return $this->error(\XF::phrase('templates_in_this_style_can_not_be_modified'));
			}

			$linkParams = [
				'search' => 1,
				'style_id' => $style->style_id
			];

			$finder = $this->getTemplateSearchFinder($style, $linkParams);
			if (count($finder->getConditions()) <= 2)
			{
				return $this->error(\XF::phrase('please_complete_required_fields'));
			}

			$total = $finder->total();

			if ($this->isPost() && $total > 0)
			{
				return $this->redirect($this->buildLink('templates/search', null, $linkParams));
			}

			$finder->with('Template.AddOn');

			$templates = $finder->fetch();

			$viewParams = [
				'style' => $style,
				'conditions' => $this->filterSearchConditions(),
				'templates' => $templates
			];
			return $this->view('XF:Template\SearchResults', 'template_search_results', $viewParams);
		}
		else
		{
			$viewParams = [
				'styleTree' => $styleRepo->getStyleTree(),
				'styleId' => $this->plugin('XF:Style')->getActiveStyleId(),
				'types' => $this->getTemplateRepo()->getTemplateTypes()
			];
			return $this->view('XF:Template\Search', 'template_search', $viewParams);
		}
	}

	public function actionRefineSearch()
	{
		$style = $this->assertStyleExists($this->filter('style_id', 'uint'));
		$styleRepo = $this->getStyleRepo();

		$conditions = $this->filterSearchConditions();

		$viewParams = [
			'style' => $style,
			'styleTree' => $styleRepo->getStyleTree(),
			'conditions' => $conditions,
			'types' => $this->getTemplateRepo()->getTemplateTypes()
		];
		return $this->view('XF:Template\RefineSearch', 'template_refine_search', $viewParams);
	}

	public function actionOutdated()
	{
		$this->setSectionContext('outdatedTemplates');

		$outdatedTemplates = $this->getTemplateRepo()->getOutdatedTemplates();
		$outdatedGrouped = \XF\Util\Arr::arrayGroup(
			$outdatedTemplates,
			function($v) { return $v['template']->style_id; }
		);

		$viewParams = [
			'outdatedTemplates' => $outdatedTemplates,
			'outdatedGrouped' => $outdatedGrouped,
			'styleTree' => $this->repository('XF:Style')->getStyleTree(),
			'total' => count($outdatedTemplates),
			'autoMerged' => $this->filter('automerged', 'bool')
		];
		return $this->view('XF:Template\Outdated', 'template_outdated', $viewParams);
	}

	public function actionCompare(ParameterBag $params)
	{
		$template = $this->assertTemplateForComparison($params->template_id);
		$parentTemplate = $template->ParentTemplate;

		$diff = new Diff();
		$diffs = $diff->findDifferences($parentTemplate->template, $template->template);

		$viewParams = [
			'template' => $template,
			'parentTemplate' => $parentTemplate,
			'diffs' => $diffs
		];
		return $this->view('XF:Template\Compare', 'template_compare', $viewParams);
	}

	public function actionMergeOutdated(ParameterBag $params)
	{
		$this->setSectionContext('outdatedTemplates');

		$template = $this->assertTemplateForComparison($params->template_id);
		$parentTemplate = $template->ParentTemplate;

		if (!$parentTemplate->last_edit_date || $parentTemplate->last_edit_date < $template->last_edit_date)
		{
			return $this->error(\XF::phrase('custom_template_out_of_date_edited_recently_no_merge'));
		}

		/** @var \XF\Repository\TemplateHistory $historyRepo */
		$historyRepo = $this->repository('XF:TemplateHistory');
		$previousVersion = $historyRepo->getHistoryForMerge($template, $parentTemplate);

		if (!$previousVersion)
		{
			return $this->error(\XF::phrase('no_previous_version_of_parent_could_be_found'));
		}

		if ($this->isPost())
		{
			$merged = $this->filter('merged', 'array-str,no-trim');
			$final = implode("\n", $merged);

			$template->template = $final;
			$template->last_edit_date = time();

			// even if the template isn't changed we should
			// make the custom template the same version so
			// it is no longer outdated.
			if (!$template->isChanged('template'))
			{
				$template->updateVersionId();
			}

			$template->save();

			return $this->redirect($this->buildLink('templates/outdated'));
		}
		else
		{
			$diff = new Diff3();
			$diffs = $diff->findDifferences($template->template, $previousVersion->template, $parentTemplate->template);

			$viewParams = [
				'template' => $template,
				'parentTemplate' => $parentTemplate,
				'previousVersion' => $previousVersion,
				'diffs' => $diffs
			];
			return $this->view('XF:Template\MergeOutdated', 'template_merge_outdated', $viewParams);
		}
	}

	public function actionAutoMerge()
	{
		if ($this->isPost())
		{
			$templateRepo = $this->getTemplateRepo();
			$templateIds = array_keys($templateRepo->getOutdatedTemplates());

			$this->app->jobManager()->enqueueUnique('autoMerge', 'XF:TemplateMerge', [
				'templateIds' => $templateIds
			]);

			return $this->redirect($this->buildLink('templates/outdated', null, ['automerged' => 1]));
		}
		else
		{
			return $this->view('XF:Template\AutoMerge', 'template_auto_merge');
		}
	}

	protected function assertTemplateForComparison($templateId)
	{
		$template = $this->assertTemplateExists($templateId, 'Style');
		if (!$template->style_id)
		{
			throw $this->exception($this->error(\XF::phrase('you_cannot_compare_custom_changes_for_master_template')));
		}

		$parentTemplate = $template->ParentTemplate;
		if (!$parentTemplate)
		{
			throw $this->exception($this->error(\XF::phrase('this_template_does_not_have_parent_version')));
		}

		return $template;
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Style
	 */
	protected function assertStyleExists($id, $with = null, $phraseKey = null)
	{
		return $this->plugin('XF:Style')->assertStyleExists($id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Template
	 */
	protected function assertTemplateExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Template', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Style
	 */
	protected function getStyleRepo()
	{
		return $this->repository('XF:Style');
	}

	/**
	 * @return \XF\Repository\Template
	 */
	protected function getTemplateRepo()
	{
		return $this->repository('XF:Template');
	}
}