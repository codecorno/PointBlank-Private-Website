<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class ClassExtension extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertDevelopmentMode();
	}

	public function actionIndex()
	{
		/** @var \XF\Repository\ClassExtension $extensionRepo */
		$extensionRepo = $this->getExtensionRepo();
		$extensions = $extensionRepo->findExtensionsForList()->fetch();

		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->repository('XF:AddOn');
		$addOns = $addOnRepo->findAddOnsForList()->fetch();

		$viewParams = [
			'extensions' => $extensions->groupBy('addon_id'),
			'addOns' => $addOns,
			'totalExtensions' => count($extensions)
		];
		return $this->view('XF:ClassExtension\Listing', 'class_extension_list', $viewParams);
	}

	protected function extensionAddEdit(\XF\Entity\ClassExtension $extension)
	{
		$viewParams = [
			'extension' => $extension
		];
		return $this->view('XF:ClassExtension\Edit', 'class_extension_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$extension = $this->assertExtensionExists($params['extension_id']);
		return $this->extensionAddEdit($extension);
	}

	public function actionAdd()
	{
		$extension = $this->em()->create('XF:ClassExtension');
		return $this->extensionAddEdit($extension);
	}

	protected function extensionSaveProcess(\XF\Entity\ClassExtension $extension)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'from_class' => 'str',
			'to_class' => 'str',
			'execute_order' => 'uint',
			'active' => 'bool',
			'addon_id' => 'str'
		]);
		$form->basicEntitySave($extension, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['extension_id'])
		{
			$extension = $this->assertExtensionExists($params['extension_id']);
		}
		else
		{
			$extension = $this->em()->create('XF:ClassExtension');
		}

		$this->extensionSaveProcess($extension)->run();

		return $this->redirect($this->buildLink('class-extensions') . $this->buildLinkHash($extension->extension_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$extension = $this->assertExtensionExists($params['extension_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$extension,
			$this->buildLink('class-extensions/delete', $extension),
			$this->buildLink('class-extensions/edit', $extension),
			$this->buildLink('class-extensions'),
			$extension->to_class
		);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:ClassExtension');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ClassExtension
	 */
	protected function assertExtensionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:ClassExtension', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\ClassExtension
	 */
	protected function getExtensionRepo()
	{
		return $this->repository('XF:ClassExtension');
	}
}