<?php

namespace XF\Admin\Controller;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

abstract class AbstractPrompt extends AbstractController
{
	abstract protected function getClassIdentifier();

	protected function getGroupClassIdentifier()
	{
		return $this->getClassIdentifier() . 'Group';
	}

	abstract protected function getLinkPrefix();

	protected function getGroupLinkPrefix()
	{
		return $this->getLinkPrefix() . '/group';
	}

	abstract protected function getTemplatePrefix();

	protected function getGroupTemplatePrefix()
	{
		return $this->getTemplatePrefix() . '_group';
	}

	public function actionIndex()
	{
		$viewParams = $this->getRepo()->getPromptListData() +
		[
			'linkPrefix' => $this->getLinkPrefix(),
			'groupLinkPrefix' => $this->getGroupLinkPrefix()
		];
		return $this->view($this->getClassIdentifier() . '\Listing', $this->getTemplatePrefix() . '_list', $viewParams);
	}

	protected function promptAddEditResponse(\XF\Entity\AbstractPrompt $prompt)
	{
		$repo = $this->getRepo();

		$viewParams = [
			'linkPrefix' => $this->getLinkPrefix(),

			'prompt' => $prompt,
			'promptGroups' => $repo->findPromptGroups()->fetch()->pluckNamed('title', 'prompt_group_id')
		];
		return $this->view($this->getClassIdentifier() . '\Edit', $this->getTemplatePrefix() . '_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$prompt = $this->assertPromptExists($params->prompt_id);
		return $this->promptAddEditResponse($prompt);
	}

	public function actionAdd()
	{
		$prompt = $this->getEntity();
		return $this->promptAddEditResponse($prompt);
	}

	protected function promptSaveProcess(\XF\Entity\AbstractPrompt $prompt)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'prompt_group_id' => 'uint',
			'display_order' => 'uint'
		]);

		$form->basicEntitySave($prompt, $input);
		$this->saveAdditionalData($form, $prompt);

		$title = $this->filter('title', 'str');
		$form->validate(function(FormAction $form) use ($title)
		{
			if ($title === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($title, $prompt)
		{
			$phrase = $prompt->getMasterPhrase();
			$phrase->phrase_text = $title;
			$phrase->save();
		});

		return $form;
	}

	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractPrompt $prompt)
	{
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->prompt_id)
		{
			$prompt = $this->assertPromptExists($params->prompt_id);
		}
		else
		{
			$prompt = $this->getEntity();
		}

		$this->promptSaveProcess($prompt)->run();

		return $this->redirect($this->buildLink($this->getLinkPrefix()));
	}

	public function actionDelete(ParameterBag $params)
	{
		$prompt = $this->assertPromptExists($params->prompt_id);
		if ($this->isPost())
		{
			$prompt->delete();
			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			$viewParams = [
				'linkPrefix' => $this->getLinkPrefix(),
				'prompt' => $prompt
			];
			return $this->view($this->getClassIdentifier() . '\Delete', $this->getTemplatePrefix() . '_delete', $viewParams);
		}
	}

	protected function promptQuickSetProcess()
	{
		$form = $this->formAction();

		$promptIds = $this->filter('prompt_ids', 'array-uint');
		if (!$promptIds)
		{
			return $form;
		}

		$prompts = $this->finder($this->getClassIdentifier())
			->where('prompt_id', $promptIds)
			->fetch();

		$input = $this->filter([
			'apply_prompt_group_id' => 'bool',
			'prompt_group_id' => 'uint'
		]);

		foreach ($prompts AS $prompt)
		{
			if ($input['apply_prompt_group_id'])
			{
				$prompt->prompt_group_id = $input['prompt_group_id'];
			}

			$prompt->save();
		}

		$this->quickSetAdditionalData($form, $prompts);

		return $form;
	}

	protected function quickSetAdditionalData(FormAction $form, ArrayCollection $prompts)
	{
	}

	public function actionQuickSet()
	{
		$this->assertPostOnly();

		$promptIds = $this->filter('prompt_ids', 'array-uint');
		if (empty($promptIds))
		{
			return $this->redirect($this->buildLink($this->getLinkPrefix()), '');
		}

		if ($this->isPost() && !$this->filter('quickset', 'bool'))
		{
			$this->promptQuickSetProcess()->run();

			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			if ($promptId = $this->filter('prompt_id', 'int'))
			{
				if ($promptId > 0)
				{
					$prompt = $this->assertPromptExists($promptId);
				}
				else
				{
					$prompt = $this->getEntity();
				}

				$prompts = $this->finder($this->getClassIdentifier())
					->where('prompt_id', $promptIds);

				$repo = $this->getRepo();

				$viewParams = [
					'linkPrefix' => $this->getLinkPrefix(),

					'prompt' => $prompt,
					'prompts' => $prompts->fetch(),
					'promptGroups' => $repo->findPromptGroups()->fetch()->pluckNamed('title', 'prompt_group_id')
				];
				return $this->view(
					$this->getClassIdentifier() . '\QuickSetEditor',
					$this->getTemplatePrefix() . '_quickset_editor',
					$viewParams
				);
			}
			else
			{
				$repo = $this->getRepo();
				$listData = $repo->getPromptListData();

				$viewParams = [
					'linkPrefix' => $this->getLinkPrefix(),

					'promptGroups' => $listData['promptGroups'],
					'promptsGrouped' => $listData['promptsGrouped'],

					'promptIds' => $promptIds
				];
				return $this->view(
					$this->getClassIdentifier() . '\QuickSetChooser',
					$this->getTemplatePrefix() . '_quickset_chooser',
					$viewParams
				);
			}
		}
	}

	protected function groupAddEditResponse(\XF\Entity\AbstractPromptGroup $promptGroup)
	{
		$viewParams = [
			'groupLinkPrefix' => $this->getGroupLinkPrefix(),
			'promptGroup' => $promptGroup
		];
		return $this->view($this->getGroupClassIdentifier() . '\Edit', $this->getGroupTemplatePrefix() . '_edit', $viewParams);
	}

	public function actionGroupEdit(ParameterBag $params)
	{
		$promptGroup = $this->assertGroupExists($params->prompt_group_id);
		return $this->groupAddEditResponse($promptGroup);
	}

	public function actionGroupAdd()
	{
		$promptGroup = $this->getGroupEntity();
		return $this->groupAddEditResponse($promptGroup);
	}

	protected function promptGroupSaveProcess(\XF\Entity\AbstractPromptGroup $promptGroup)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'display_order' => 'uint'
		]);

		$form->basicEntitySave($promptGroup, $input);
		$this->saveGroupAdditionalData($form, $promptGroup);

		$title = $this->filter('title', 'str');
		$form->validate(function(FormAction $form) use ($title)
		{
			if ($title === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($title, $promptGroup)
		{
			$phrase = $promptGroup->getMasterPhrase();
			$phrase->phrase_text = $title;
			$phrase->save();
		});

		return $form;
	}

	protected function saveGroupAdditionalData(FormAction $form, \XF\Entity\AbstractPromptGroup $promptGroup)
	{
	}

	public function actionGroupSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->prompt_group_id)
		{
			$promptGroup = $this->assertGroupExists($params->prompt_group_id);
		}
		else
		{
			$promptGroup = $this->getGroupEntity();
		}

		$this->promptGroupSaveProcess($promptGroup)->run();

		return $this->redirect($this->buildLink($this->getLinkPrefix()));
	}

	public function actionGroupDelete(ParameterBag $params)
	{
		$promptGroup = $this->assertGroupExists($params->prompt_group_id);
		if ($this->isPost())
		{
			$promptGroup->delete();
			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			$viewParams = [
				'groupLinkPrefix' => $this->getGroupLinkPrefix(),
				'promptGroup' => $promptGroup
			];
			return $this->view($this->getGroupClassIdentifier() . '\Delete', $this->getGroupTemplatePrefix() . '_delete', $viewParams);
		}
	}

	/**
	 * @return \XF\Entity\AbstractPrompt
	 */
	protected function assertPromptExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists($this->getClassIdentifier(), $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Entity\AbstractPromptGroup
	 */
	protected function assertGroupExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists($this->getGroupClassIdentifier(), $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Entity\AbstractPrompt
	 */
	protected function getEntity()
	{
		return $this->em()->create($this->getClassIdentifier());
	}

	/**
	 * @return \XF\Entity\AbstractPrompt
	 */
	protected function getGroupEntity()
	{
		return $this->em()->create($this->getGroupClassIdentifier());
	}

	/**
	 * @return \XF\Repository\AbstractPrompt
	 */
	protected function getRepo()
	{
		return $this->repository($this->getClassIdentifier());
	}
}