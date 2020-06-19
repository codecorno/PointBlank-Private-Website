<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class CaptchaQuestion extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('option');
	}

	public function actionIndex()
	{
		$questionRepo = $this->getCaptchaQuestionRepo();
		$questions = $questionRepo->findCaptchaQuestionsForList()->fetch();

		$viewParams = [
			'questions' => $questions
		];
		return $this->view('XF:CaptchaQuestion\Listing', 'captcha_question_list', $viewParams);
	}

	protected function questionAddEdit(\XF\Entity\CaptchaQuestion $question)
	{
		$viewParams = [
			'question' => $question
		];
		return $this->view('XF:CaptchaQuestion\Edit', 'captcha_question_edit', $viewParams);
	}

	public function actionAdd()
	{
		$question = $this->em()->create('XF:CaptchaQuestion');
		return $this->questionAddEdit($question);
	}

	public function actionEdit(ParameterBag $params)
	{
		$question = $this->assertCaptchaQuestionExists($params['captcha_question_id']);
		return $this->questionAddEdit($question);
	}

	protected function questionSaveProcess(\XF\Entity\CaptchaQuestion $question)
	{
		$input = $this->filter([
			'question' => 'str',
			'answers' => 'array-str',
			'active' => 'bool'
		]);

		$form = $this->formAction();
		$form->basicEntitySave($question, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['captcha_question_id'])
		{
			$question = $this->assertCaptchaQuestionExists($params['captcha_question_id']);
		}
		else
		{
			$question = $this->em()->create('XF:CaptchaQuestion');
		}

		$form = $this->questionSaveProcess($question);
		$form->run();

		return $this->redirect($this->buildLink('captcha-questions') . $this->buildLinkHash($question->captcha_question_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$question = $this->assertCaptchaQuestionExists($params['captcha_question_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$question,
			$this->buildLink('captcha-questions/delete', $question),
			$this->buildLink('captcha-questions/edit', $question),
			$this->buildLink('captcha-questions'),
			$question->question
		);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:CaptchaQuestion');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\CaptchaQuestion
	 */
	protected function assertCaptchaQuestionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:CaptchaQuestion', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\CaptchaQuestion
	 */
	protected function getCaptchaQuestionRepo()
	{
		return $this->repository('XF:CaptchaQuestion');
	}
}