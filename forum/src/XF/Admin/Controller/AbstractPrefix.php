<?php

namespace XF\Admin\Controller;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

abstract class AbstractPrefix extends AbstractController
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
		$viewParams = $this->getRepo()->getPrefixListData() +
		[
			'linkPrefix' => $this->getLinkPrefix(),
			'groupLinkPrefix' => $this->getGroupLinkPrefix()
		];
		return $this->view($this->getClassIdentifier() . '\Listing', $this->getTemplatePrefix() . '_list', $viewParams);
	}

	protected function prefixAddEditResponse(\XF\Entity\AbstractPrefix $prefix)
	{
		$repo = $this->getRepo();

		$viewParams = [
			'linkPrefix' => $this->getLinkPrefix(),

			'prefix' => $prefix,
			'displayStyles' => $repo->getDefaultDisplayStyles(),
			'prefixGroups' => $repo->findPrefixGroups()->fetch()->pluckNamed('title', 'prefix_group_id')
		];
		return $this->view($this->getClassIdentifier() . '\Edit', $this->getTemplatePrefix() . '_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$prefix = $this->assertPrefixExists($params->prefix_id);
		return $this->prefixAddEditResponse($prefix);
	}

	public function actionAdd()
	{
		$prefix = $this->getEntity();
		return $this->prefixAddEditResponse($prefix);
	}

	protected function prefixSaveProcess(\XF\Entity\AbstractPrefix $prefix)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'css_class' => 'str',
			'prefix_group_id' => 'uint',
			'display_order' => 'uint'
		]);

		$usableUserGroups = $this->filter('usable_user_group', 'str');
		if ($usableUserGroups == 'all')
		{
			$input['allowed_user_group_ids'] = [-1];
		}
		else
		{
			$input['allowed_user_group_ids'] = $this->filter('usable_user_group_ids', 'array-uint');
		}

		$form->basicEntitySave($prefix, $input);
		$this->saveAdditionalData($form, $prefix);

		$title = $this->filter('title', 'str');
		$form->validate(function(FormAction $form) use ($title)
		{
			if ($title === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($title, $prefix)
		{
			$phrase = $prefix->getMasterPhrase();
			$phrase->phrase_text = $title;
			$phrase->save();
		});

		return $form;
	}

	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractPrefix $prefix)
	{
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->prefix_id)
		{
			$prefix = $this->assertPrefixExists($params->prefix_id);
		}
		else
		{
			$prefix = $this->getEntity();
		}

		$this->prefixSaveProcess($prefix)->run();

		return $this->redirect($this->buildLink($this->getLinkPrefix()));
	}

	public function actionDelete(ParameterBag $params)
	{
		$prefix = $this->assertPrefixExists($params->prefix_id);
		if ($this->isPost())
		{
			$prefix->delete();
			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			$viewParams = [
				'linkPrefix' => $this->getLinkPrefix(),
				'prefix' => $prefix
			];
			return $this->view($this->getClassIdentifier() . '\Delete', $this->getTemplatePrefix() . '_delete', $viewParams);
		}
	}

	protected function prefixQuickSetProcess()
	{
		$form = $this->formAction();

		$prefixIds = $this->filter('prefix_ids', 'array-uint');
		if (!$prefixIds)
		{
			return $form;
		}

		$prefixes = $this->finder($this->getClassIdentifier())
			->where('prefix_id', $prefixIds)
			->fetch();

		$input = $this->filter([
			'apply_css_class' => 'bool',
			'css_class' => 'str',

			'apply_prefix_group_id' => 'bool',
			'prefix_group_id' => 'uint',

			'apply_user_group_ids' => 'bool',
			'usable_user_group' => 'str',
			'usable_user_group_ids' => 'array-uint'
		]);

		foreach ($prefixes AS $prefix)
		{
			if ($input['apply_css_class'])
			{
				$prefix->css_class = $input['css_class'];
			}

			if ($input['apply_prefix_group_id'])
			{
				$prefix->prefix_group_id = $input['prefix_group_id'];
			}

			if ($input['apply_user_group_ids'])
			{
				if ($input['usable_user_group'] == 'all')
				{
					$prefix->allowed_user_group_ids = [-1];
				}
				else
				{
					$prefix->allowed_user_group_ids = $input['usable_user_group_ids'];
				}
			}

			$prefix->save();
		}

		$this->quickSetAdditionalData($form, $prefixes);

		return $form;
	}

	protected function quickSetAdditionalData(FormAction $form, ArrayCollection $prefixes)
	{
	}

	public function actionQuickSet()
	{
		$this->assertPostOnly();

		$prefixIds = $this->filter('prefix_ids', 'array-uint');
		if (empty($prefixIds))
		{
			return $this->redirect($this->buildLink($this->getLinkPrefix()), '');
		}

		if ($this->isPost() && !$this->filter('quickset', 'bool'))
		{
			$this->prefixQuickSetProcess()->run();

			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			if ($prefixId = $this->filter('prefix_id', 'int'))
			{
				if ($prefixId > 0)
				{
					$prefix = $this->assertPrefixExists($prefixId);
				}
				else
				{
					$prefix = $this->getEntity();
				}

				$prefixes = $this->finder($this->getClassIdentifier())
					->where('prefix_id', $prefixIds);

				$repo = $this->getRepo();

				$viewParams = [
					'linkPrefix' => $this->getLinkPrefix(),

					'prefix' => $prefix,
					'prefixes' => $prefixes->fetch(),
					'displayStyles' => $repo->getDefaultDisplayStyles(),
					'prefixGroups' => $repo->findPrefixGroups()->fetch()->pluckNamed('title', 'prefix_group_id')
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
				$listData = $repo->getPrefixListData();

				$viewParams = [
					'linkPrefix' => $this->getLinkPrefix(),

					'prefixGroups' => $listData['prefixGroups'],
					'prefixesGrouped' => $listData['prefixesGrouped'],

					'prefixIds' => $prefixIds
				];
				return $this->view(
					$this->getClassIdentifier() . '\QuickSetChooser',
					$this->getTemplatePrefix() . '_quickset_chooser',
					$viewParams
				);
			}
		}
	}

	protected function groupAddEditResponse(\XF\Entity\AbstractPrefixGroup $prefixGroup)
	{
		$viewParams = [
			'groupLinkPrefix' => $this->getGroupLinkPrefix(),
			'prefixGroup' => $prefixGroup
		];
		return $this->view($this->getGroupClassIdentifier() . '\Edit', $this->getGroupTemplatePrefix() . '_edit', $viewParams);
	}

	public function actionGroupEdit(ParameterBag $params)
	{
		$prefixGroup = $this->assertGroupExists($params->prefix_group_id);
		return $this->groupAddEditResponse($prefixGroup);
	}

	public function actionGroupAdd()
	{
		$prefixGroup = $this->getGroupEntity();
		return $this->groupAddEditResponse($prefixGroup);
	}

	protected function prefixGroupSaveProcess(\XF\Entity\AbstractPrefixGroup $prefixGroup)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'display_order' => 'uint'
		]);

		$form->basicEntitySave($prefixGroup, $input);
		$this->saveGroupAdditionalData($form, $prefixGroup);

		$title = $this->filter('title', 'str');
		$form->validate(function(FormAction $form) use ($title)
		{
			if ($title === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($title, $prefixGroup)
		{
			$phrase = $prefixGroup->getMasterPhrase();
			$phrase->phrase_text = $title;
			$phrase->save();
		});

		return $form;
	}

	protected function saveGroupAdditionalData(FormAction $form, \XF\Entity\AbstractPrefixGroup $prefixGroup)
	{
	}

	public function actionGroupSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->prefix_group_id)
		{
			$prefixGroup = $this->assertGroupExists($params->prefix_group_id);
		}
		else
		{
			$prefixGroup = $this->getGroupEntity();
		}

		$this->prefixGroupSaveProcess($prefixGroup)->run();

		return $this->redirect($this->buildLink($this->getLinkPrefix()));
	}

	public function actionGroupDelete(ParameterBag $params)
	{
		$prefixGroup = $this->assertGroupExists($params->prefix_group_id);
		if ($this->isPost())
		{
			$prefixGroup->delete();
			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			$viewParams = [
				'groupLinkPrefix' => $this->getGroupLinkPrefix(),
				'prefixGroup' => $prefixGroup
			];
			return $this->view($this->getGroupClassIdentifier() . '\Delete', $this->getGroupTemplatePrefix() . '_delete', $viewParams);
		}
	}

	/**
	 * @return \XF\Entity\AbstractPrefix
	 */
	protected function assertPrefixExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists($this->getClassIdentifier(), $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Entity\AbstractPrefixGroup
	 */
	protected function assertGroupExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists($this->getGroupClassIdentifier(), $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Entity\AbstractPrefix
	 */
	protected function getEntity()
	{
		return $this->em()->create($this->getClassIdentifier());
	}

	/**
	 * @return \XF\Entity\AbstractPrefix
	 */
	protected function getGroupEntity()
	{
		return $this->em()->create($this->getGroupClassIdentifier());
	}

	/**
	 * @return \XF\Repository\AbstractPrefix
	 */
	protected function getRepo()
	{
		return $this->repository($this->getClassIdentifier());
	}
}