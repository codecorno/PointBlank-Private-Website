<?php

namespace XF\Admin\Controller;

use XF\Http\Request;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Smilie extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('bbCodeSmilie');
	}

	public function actionIndex()
	{
		$smilieData = $this->getSmilieRepo()->getSmilieListData();

		$viewParams = [
			'smilieData' => $smilieData,
			'exportView' => $this->filter('export', 'bool')
		];
		return $this->view('XF:Smilie\Listing', 'smilie_list', $viewParams);
	}

	public function smilieAddEdit(\XF\Entity\Smilie $smilie)
	{
		$viewParams = [
			'smilie' => $smilie,
			'smilieCategories' => $this->getSmilieCategoryRepo()->getSmilieCategoryTitlePairs()
		];
		return $this->view('XF:Smilie\Edit', 'smilie_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$smilie = $this->assertSmilieExists($params['smilie_id']);
		return $this->smilieAddEdit($smilie);
	}

	public function actionAdd()
	{
		$smilie = $this->em()->create('XF:Smilie');

		return $this->smilieAddEdit($smilie);
	}

	protected function smilieSaveProcess(\XF\Entity\Smilie $smilie)
	{
		$entityInput = $this->filter([
			'title' => 'str',
			'smilie_text' => 'str',
			'image_url' => 'str',
			'image_url_2x' => 'str',
			'sprite_mode' => 'uint',
			'sprite_params' => 'array',
			'smilie_category_id' => 'uint',
			'display_order' => 'uint',
			'display_in_editor' => 'uint',
		]);

		$form = $this->formAction();
		$form->basicEntitySave($smilie, $entityInput);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['smilie_id'])
		{
			$smilie = $this->assertSmilieExists($params['smilie_id']);
		}
		else
		{
			$smilie = $this->em()->create('XF:Smilie');
		}

		$this->smilieSaveProcess($smilie)->run();

		return $this->redirect($this->buildLink('smilies'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$smilie = $this->assertSmilieExists($params['smilie_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$smilie,
			$this->buildLink('smilies/delete', $smilie),
			$this->buildLink('smilies/edit', $smilie),
			$this->buildLink('smilies'),
			$smilie->title
		);
	}

	public function actionExport()
	{
		$smilies = $this->finder('XF:Smilie')
			->where('smilie_id', $this->filter('export', 'array-str'))
			->order(['Category.display_order', 'display_order', 'title']);

		return $this->plugin('XF:Xml')->actionExport($smilies, 'XF:Smilie\Export');
	}

	public function actionImport()
	{
		if ($this->isPost())
		{
			$input = $this->filterFormJson([
				'categories' => 'array',
				'import' => 'array-int',
				'smilies' => 'array',
			]);

			$smilies = [];

			foreach ($input['import'] AS $smilieId)
			{
				if (empty($input['smilies'][$smilieId]) || !is_array($input['smilies'][$smilieId]))
				{
					continue;
				}

				$smilies[$smilieId] = $this->filterSmilieImportInput($input['smilies'][$smilieId]);
			}

			/** @var \XF\Service\Smilie\Import $smilieImporter */
			$smilieImporter = $this->service('XF:Smilie\Import');
			$smilieImporter->importSmilies($smilies, $input['categories'], $errors);

			if (empty($errors))
			{
				return $this->redirect($this->buildLink('smilies'));
			}
			else
			{
				return $this->error($errors);
			}
		}
		else
		{
			$viewParams = [
				'smilieCategories' => $this->getSmilieCategoryRepo()->getSmilieCategoryTitlePairs(),
				'smilieXmlFiles' => $this->getSmilieRepo()->getSmilieImportXmlFiles()
			];
			return $this->view('XF:Smilie\Import', 'smilie_import', $viewParams);
		}
	}

	protected function filterSmilieImportInput(array $smilieInput)
	{
		return $this->filterArray($smilieInput, [
			'title' => 'str',
			'smilie_text' => 'str',
			'image_url' => 'str',
			'image_url_2x' => 'str',
			'sprite_mode' => 'uint',
			'sprite_params' => 'array',
			'smilie_category_id' => 'str',
			'display_order' => 'uint',
			'display_in_editor' => 'uint',
		]);
	}

	public function actionImportForm()
	{
		$this->assertPostOnly();

		$input = $this->filter([
			'mode' => 'str',
			'directory' => 'str',
		]);

		/** @var \XF\Service\Smilie\Import $smilieImporter */
		$smilieImporter = $this->service('XF:Smilie\Import');

		if ($input['mode'] == 'directory')
		{
			$directory = $this->filter('directory', 'str');

			$smilieData = $smilieImporter->getSmilieDataFromDirectory($directory);
		}
		else
		{
			if ($input['mode'] == 'upload')
			{
				$upload = $this->request->getFile('upload', false);
				if (!$upload)
				{
					return $this->error(\XF::phrase('please_upload_valid_smilies_xml_file'));
				}

				try
				{
					$xml = \XF\Util\Xml::openFile($upload->getTempFile());
				}
				catch (\Exception $e)
				{
					$xml = null;
				}
			}
			else
			{
				$xml = \XF\Util\Xml::open($this->app()->fs()->read(
					$this->getSmilieRepo()->getAbstractedImportedXmlFilePath($this->filter('filename', 'str'))
				));
			}

			if (!$xml || $xml->getName() != 'smilies_export')
			{
				return $this->error(\XF::phrase('please_upload_valid_smilies_xml_file'));
			}

			$smilieData = $smilieImporter->getSmilieDataFromXml($xml);
		}

		$viewParams = [
			'uploadMode' => ($input['mode'] == 'upload'),
			'smilies' => $smilieData['smilies'],
			'smilieCategoryMap' => $smilieData['smilieCategoryMap'],
			'newCategories' => $smilieData['categories'],
			'newCategoryPairs' => $smilieData['categoryPairs'],
			'categoryPairs' => $this->getSmilieCategoryRepo()->getSmilieCategoryTitlePairs(),
		];
		return $this->view('XF:Smilie\ImportForm', 'smilie_import_form', $viewParams);
	}

	public function actionSort(ParameterBag $params)
	{
		if ($this->isPost())
		{
			$smilies = $this->finder('XF:Smilie')->fetch();

			foreach ($this->filter('smilies', 'array-json-array') AS $smiliesInCategory)
			{
				$lastOrder = 0;
				foreach ($smiliesInCategory AS $key => $smilieValue)
				{
					if (!isset($smilieValue['id']) || !isset($smilies[$smilieValue['id']]))
					{
						continue;
					}

					$lastOrder += 10;

					/** @var \XF\Entity\Smilie $smilie */
					$smilie = $smilies[$smilieValue['id']];
					$smilie->smilie_category_id = $smilieValue['parent_id'];
					$smilie->display_order = $lastOrder;
					$smilie->saveIfChanged();
				}
			}

			return $this->redirect($this->buildLink('smilies'));
		}
		else
		{
			$smilieData = $this->getSmilieRepo()->getSmilieListData();

			$viewParams = [
				'smilieData' => $smilieData
			];
			return $this->view('XF:Smilie\Sort', 'smilie_sort', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Smilie
	 */
	protected function assertSmilieExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Smilie', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Smilie
	 */
	protected function getSmilieRepo()
	{
		return $this->repository('XF:Smilie');
	}

	/**
	 * @return \XF\Repository\SmilieCategory
	 */
	protected function getSmilieCategoryRepo()
	{
		return $this->repository('XF:SmilieCategory');
	}
}