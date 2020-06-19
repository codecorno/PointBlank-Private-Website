<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class ButtonManager extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('bbCodeSmilie');
	}

	public function actionIndex()
	{
		$editorRepo = $this->getEditorRepo();
		$toolbarTypes = $editorRepo->getToolbarTypes();
		$editorDropdowns = $editorRepo->findEditorDropdownsForList()->fetch();

		$viewParams = [
			'toolbarTypes' => $toolbarTypes,
			'editorDropdowns' => $editorDropdowns
		];
		return $this->view('XF:ButtonManager\List', 'bb_code_button_manager_list', $viewParams);
	}

	public function actionEdit()
	{
		$editorRepo = $this->getEditorRepo();
		$toolbarTypes = $editorRepo->getToolbarTypes();

		$type = $this->filter('type', 'str');

		if (!$type || !isset($toolbarTypes[$type]))
		{
			return $this->notFound();
		}

		$toolbarType = $toolbarTypes[$type];

		/** @var \XF\Data\Editor $data */
		$data = $this->data('XF:Editor');

		$options = $this->options();
		$editorToolbarConfig = $options->editorToolbarConfig;

		$buttonData = $data->getButtonData();

		$viewParams = [
			'buttonData' => $buttonData,
			'toolbarButtons' => $editorToolbarConfig[$type],
			'type' => $type,
			'typeTitle' => $toolbarType['title'],
			'typeDescription' => $toolbarType['description']
		];
		return $this->view('XF:ButtonManager\Editor', 'bb_code_button_manager_editor', $viewParams);
	}

	public function actionSave()
	{
		$this->assertPostOnly();

		$editorRepo = $this->getEditorRepo();
		$toolbarTypes = $editorRepo->getToolbarTypes();

		$type = $this->filter('type', 'str');

		if (!$type || !isset($toolbarTypes[$type]))
		{
			return $this->notFound();
		}

		$buttonConfig = $this->options()->editorToolbarConfig;
		$typeConfig = $this->filter('editor_toolbar_config.' . $type, 'json-array');
		$buttonConfig[$type] = $typeConfig;

		/** @var \XF\Repository\Option $optionRepo */
		$optionRepo = $this->repository('XF:Option');
		$optionRepo->updateOption('editorToolbarConfig', $buttonConfig);

		return $this->redirect($this->buildLink('button-manager'));
	}

	public function actionReset()
	{
		$editorRepo = $this->getEditorRepo();
		$toolbarTypes = $editorRepo->getToolbarTypes();

		$type = $this->filter('type', 'str');

		if (!$type || !isset($toolbarTypes[$type]))
		{
			return $this->notFound();
		}

		$toolbarType = $toolbarTypes[$type];

		if ($this->isPost())
		{
			/** @var \XF\Data\Editor $data */
			$data = $this->data('XF:Editor');

			$buttonConfig = $this->options()->editorToolbarConfig;

			if ($type == 'toolbarButtonsXS')
			{
				$buttonConfig[$type] = $data->getDefaultToolbarButtons(true);
			}
			else
			{
				$buttonConfig[$type] = $data->getDefaultToolbarButtons();
			}

			/** @var \XF\Repository\Option $optionRepo */
			$optionRepo = $this->repository('XF:Option');
			$optionRepo->updateOption('editorToolbarConfig', $buttonConfig);

			return $this->redirect($this->buildLink('button-manager'));
		}
		else
		{
			$viewParams = [
				'type' => $type,
				'typeTitle' => $toolbarType['title'],
				'typeDescription' => $toolbarType['description']
			];
			return $this->view('XF:ButtonManager\Reset', 'bb_code_button_manager_reset', $viewParams);
		}
	}

	public function dropdownAddEdit(\XF\Entity\EditorDropdown $dropdown)
	{
		/** @var \XF\Data\Editor $data */
		$data = $this->data('XF:Editor');

		// various commands are unsupported inside dropdowns
		$invalidCommands = [
			'-vs',
			'-hs',
			'xfBbCode',
			'insertTable'
		];
		$buttonData = array_filter($data->getButtonData(), function($data, $key) use($invalidCommands)
		{
			if (in_array($key, $invalidCommands))
			{
				return false;
			}

			if (isset($data['type']) && ($data['type'] == 'dropdown' || $data['type'] == 'editable_dropdown'))
			{
				// nested dropdowns not supported
				return false;
			}

			return true;
		}, ARRAY_FILTER_USE_BOTH);

		$viewParams = [
			'dropdown' => $dropdown,
			'buttonData' => $buttonData
		];
		return $this->view('XF:ButtonManager\Dropdown\Edit', 'bb_code_button_manager_dropdown_edit', $viewParams);
	}

	public function actionDropdownEdit(ParameterBag $params)
	{
		$dropdown = $this->assertRecordExists('XF:EditorDropdown', $params->cmd);
		return $this->dropdownAddEdit($dropdown);
	}

	public function actionDropdownAdd()
	{
		$dropdown = $this->em()->create('XF:EditorDropdown');
		return $this->dropdownAddEdit($dropdown);
	}

	protected function dropdownSaveProcess(\XF\Entity\EditorDropdown $dropdown)
	{
		$form = $this->formAction();

		$dropdownInput = $this->filter([
			'icon' => 'str',
			'display_order' => 'uint',
			'active' => 'bool',
			'buttons' => 'json-array'
		]);
		if ($dropdown->isInsert())
		{
			$dropdownInput['cmd'] = $this->filter('cmd', 'str');
		}

		$form->basicEntitySave($dropdown, $dropdownInput);

		$title = $this->filter('title', 'str');
		$form->validate(function(FormAction $form) use ($title)
		{
			if ($title === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($title, $dropdown)
		{
			$masterTitle = $dropdown->getMasterPhrase();
			$masterTitle->phrase_text = $title;
			$masterTitle->save();
		});

		return $form;
	}

	public function actionDropdownSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->cmd)
		{
			$dropdown = $this->assertRecordExists('XF:EditorDropdown', $params->cmd);
		}
		else
		{
			$dropdown = $this->em()->create('XF:EditorDropdown');
		}

		$this->dropdownSaveProcess($dropdown)->run();

		return $this->redirect($this->buildLink('button-manager'));
	}

	public function actionDropdownDelete(ParameterBag $params)
	{
		$dropdown = $this->assertRecordExists('XF:EditorDropdown', $params->cmd);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$dropdown,
			$this->buildLink('button-manager/dropdown/delete', $dropdown),
			$this->buildLink('button-manager/dropdown/edit', $dropdown),
			$this->buildLink('button-manager'),
			$dropdown->title
		);
	}

	public function actionDropdownToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:EditorDropdown');
	}

	/**
	 * @return \XF\Mvc\Entity\Repository|\XF\Repository\Editor
	 */
	public function getEditorRepo()
	{
		return $this->repository('XF:Editor');
	}
}