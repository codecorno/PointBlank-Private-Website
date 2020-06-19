<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Reaction extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('reaction');
	}

	public function actionIndex()
	{
		$reactionRepo = $this->getReactionRepo();
		$reactionFinder = $reactionRepo->findReactionsForList();

		$viewParams = [
			'reactions' => $reactionFinder->fetch()
		];
		return $this->view('XF:Reaction\List', 'reaction_list', $viewParams);
	}

	public function reactionAddEdit(\XF\Entity\Reaction $reaction)
	{
		$propertyRepo = $this->repository('XF:StyleProperty');
		$reactionRepo = $this->getReactionRepo();

		$viewParams = [
			'reaction' => $reaction,
			'colorData' => $propertyRepo->getStyleColorData(),
			'reactionScores' => $reactionRepo->getReactionScores()
		];
		return $this->view('XF:Reaction\Edit', 'reaction_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$reaction = $this->assertReactionExists($params->reaction_id);
		return $this->reactionAddEdit($reaction);
	}

	public function actionAdd()
	{
		$reaction = $this->em()->create('XF:Reaction');
		return $this->reactionAddEdit($reaction);
	}

	protected function reactionSaveProcess(\XF\Entity\Reaction $reaction)
	{
		$entityInput = $this->filter([
			'text_color' => 'str',
			'reaction_score' => 'int',
			'image_url' => 'str',
			'image_url_2x' => 'str',
			'sprite_mode' => 'uint',
			'sprite_params' => 'array',
			'display_order' => 'uint',
			'active' => 'bool',
		]);

		$form = $this->formAction();

		$customReactionScore = $this->filter('custom_reaction_score', 'int');
		if ($customReactionScore)
		{
			$entityInput['reaction_score'] = $customReactionScore;
		}
		else
		{
			$entityInput['reaction_score'] = intval($entityInput['reaction_score']);
		}

		$form->validate(function(FormAction $form) use ($entityInput)
		{
			if ($entityInput['text_color']
				&& strpos($entityInput['text_color'], '@xf-') !== 0
				&& !\XF\Util\Color::isValidColor($entityInput['text_color'])
			)
			{
				$form->logError(\XF::phrase('please_choose_valid_text_color'));
			}
		});

		$form->basicEntitySave($reaction, $entityInput);

		$title = $this->filter('title', 'str');
		$form->validate(function(FormAction $form) use ($title)
		{
			if ($title === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($reaction, $title)
		{
			$masterTitle = $reaction->getMasterPhrase(true);
			$masterTitle->phrase_text = $title;
			$masterTitle->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->reaction_id)
		{
			$reaction = $this->assertReactionExists($params['reaction_id']);
		}
		else
		{
			$reaction = $this->em()->create('XF:Reaction');
		}

		$this->reactionSaveProcess($reaction)->run();

		return $this->redirect($this->buildLink('reactions'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$reaction = $this->assertReactionExists($params->reaction_id);
		if (!$reaction->canDelete($error))
		{
			return $this->error($error);
		}

		if ($this->isPost())
		{
			$reaction->delete();
			return $this->redirect($this->buildLink('reactions'));
		}
		else
		{
			$viewParams = [
				'reaction' => $reaction
			];
			return $this->view('XF:Reaction\Delete', 'reaction_delete', $viewParams);
		}
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:Reaction');
	}

	public function actionSort()
	{
		$reactionRepo = $this->getReactionRepo();
		$reactionFinder = $reactionRepo->findReactionsForList();
		$reactions = $reactionFinder->fetch();

		if ($this->isPost())
		{
			$sortData = $this->filter('reactions', 'json-array');

			/** @var \XF\ControllerPlugin\Sort $sorter */
			$sorter = $this->plugin('XF:Sort');
			$sorter->sortFlat($sortData, $reactions);

			return $this->redirect($this->buildLink('reactions'));
		}
		else
		{
			$viewParams = [
				'reactions' => $reactionFinder->fetch()
			];
			return $this->view('XF:Reaction\Sort', 'reaction_sort', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Reaction
	 */
	protected function assertReactionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Reaction', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Reaction
	 */
	protected function getReactionRepo()
	{
		return $this->repository('XF:Reaction');
	}
}