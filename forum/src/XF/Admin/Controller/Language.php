<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Language extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('language');
	}

	public function actionIndex()
	{
		$viewParams = [
			'languageTree' => $this->getLanguageRepo()->getLanguageTree()
		];
		return $this->view('XF:Language\Listing', 'language_list', $viewParams);
	}

	protected function languageAddEdit(\XF\Entity\Language $language)
	{
		$languageData = $this->data('XF:Language');
		$currencyData = $this->data('XF:Currency');

		$viewParams = [
			'language' => $language,
			'languageTree' => $this->getLanguageRepo()->getLanguageTree(false),

			'locales' => $languageData->getLocaleList(),
			'dateFormats' => $languageData->getDateFormatExamples(),
			'timeFormats' => $languageData->getTimeFormatExamples(),

			'currencyFormats' => $currencyData->getCurrencyFormatExamples(),

			'quickPhrases' => null
		];

		if (!$language->exists())
		{
			$language->language_code = 'en-US';
			$language->date_format = key($viewParams['dateFormats']);
			$language->time_format = key($viewParams['timeFormats']);
			$language->currency_format = key($viewParams['currencyFormats']);
		}
		else
		{
			$viewParams['quickPhrases'] = $this->getPhraseRepo()->getEffectivePhrasesByTitles(
				$language, $this->getQuickEditPhraseNames()
			);
		}

		return $this->view('XF:Language\Edit', 'language_edit', $viewParams);
	}

	protected function getQuickEditPhraseNames()
	{
		return [
			'privacy_policy_text',
			'terms_rules_text',
			'email_footer_html',
			'email_footer_text',
			'extra_copyright'
		];
	}

	public function actionEdit(ParameterBag $params)
	{
		$language = $this->assertLanguageExists($params['language_id']);
		return $this->languageAddEdit($language);
	}

	public function actionAdd()
	{
		$language = $this->em()->create('XF:Language');
		return $this->languageAddEdit($language);
	}

	protected function languageSaveProcess(\XF\Entity\Language $language)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'parent_id' => 'uint',
			'title' => 'str',
			'date_format' => 'str',
			'time_format' => 'str',
			'currency_format' => 'str',
			'decimal_point' => 'str,no-trim',
			'thousands_separator' => 'str,no-trim',
			'language_code' => 'str',
			'text_direction' => 'str',
			'week_start' => 'uint',
			'label_separator' => 'str,no-trim',
			'comma_separator' => 'str,no-trim',
			'ellipsis' => 'str,no-trim',
			'parenthesis_open' => 'str,no-trim',
			'parenthesis_close' => 'str,no-trim',
		]);

		if ($input['date_format'] === '')
		{
			$input['date_format'] = $this->filter('date_format_other', 'str');
		}
		if ($input['time_format'] === '')
		{
			$input['time_format'] = $this->filter('time_format_other', 'str');
		}
		if ($input['currency_format'] === '')
		{
			$input['currency_format'] = $this->filter('currency_format_other', 'str');
			if (strpos($input['currency_format'], '{value}') === false)
			{
				$form->logError(\XF::phrase('currency_format_must_contain_at_least_value'), 'currency_format');
			}
		}

		$form->basicEntitySave($language, $input);

		if ($language->isUpdate())
		{
			$phraseRepo = $this->getPhraseRepo();

			$qpOriginal = $phraseRepo->getEffectivePhrasesByTitles($language, $this->getQuickEditPhraseNames());
			$qpInput = $this->filter('quick_phrases', 'array-str');
			foreach ($qpOriginal AS $qpName => /** @var \XF\Entity\Phrase $qp */ $qp)
			{
				if (!isset($qpInput[$qpName]))
				{
					continue;
				}

				$qpNew = $qpInput[$qpName];
				if ($qpNew != $qp->phrase_text)
				{
					$form->complete(function() use ($phraseRepo, $language, $qpName, $qpNew)
					{
						$phraseRepo->quickCustomizePhrase($language, $qpName, $qpNew);
					});
				}
			}
		}

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['language_id'])
		{
			$language = $this->assertLanguageExists($params['language_id']);
		}
		else
		{
			$language = $this->em()->create('XF:Language');
		}

		$this->languageSaveProcess($language)->run();

		return $this->redirect($this->buildLink('languages'));
	}

	public function actionToggle()
	{
		// update defaultLanguageId option if necessary
		$input = $this->filter([
			'default_language_id' => 'int',
			'default_language_id_original' => 'int'
		]);
		if ($input['default_language_id'] != $input['default_language_id_original'])
		{
			$this->assertLanguageExists($input['default_language_id']);
			$this->repository('XF:Option')->updateOptions(['defaultLanguageId' => $input['default_language_id']]);
		}

		return $this->redirect($this->buildLink('languages'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$language = $this->assertLanguageExists($params->language_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$language,
			$this->buildLink('languages/delete', $language),
			$this->buildLink('languages/edit', $language),
			$this->buildLink('languages'),
			$language->title
		);
	}

	public function actionPhrases(ParameterBag $params)
	{
		$currentAddOn = null;
		$addOnId = $this->filter('addon_id', 'str');
		if ($addOnId)
		{
			$currentAddOn = $this->em()->find('XF:AddOn', $addOnId);
		}

		$language = $this->assertLanguageExists($params['language_id']);
		if (!$language->canEdit())
		{
			return $this->error(\XF::phrase('phrases_in_this_language_can_not_be_modified'));
		}

		$this->app->response()->setCookie('edit_language_id', $language->language_id);

		$page = $this->filterPage();
		$perPage = 300;

		$phrasesFinder = $this->getPhraseRepo()->findEffectivePhrasesInLanguage($language);
		$phrasesFinder->limitByPage($page, $perPage);

		if ($currentAddOn)
		{
			$phrasesFinder->where('Phrase.addon_id', $currentAddOn->addon_id);
		}
		$phrasesFinder->with('Phrase.AddOn');

		$filter = $this->filter('_xfFilter', [
			'text' => 'str',
			'prefix' => 'bool'
		]);
		if (strlen($filter['text']))
		{
			$phrasesFinder->Phrase->searchTitle($filter['text'], $filter['prefix']);
		}

		$total = $phrasesFinder->total();

		$linkParams = [
			'addon_id' => $currentAddOn ? $currentAddOn->addon_id : null
		];

		$viewParams = [
			'language' => $language,
			'phrases' => $phrasesFinder->fetch(),
			'languageTree' => $this->getLanguageRepo()->getLanguageTree(),

			'currentAddOn' => $currentAddOn,
			'addOns' => $this->getAddOnRepo()->findAddOnsForList()->fetch(),

			'linkParams' => $linkParams,

			'filter' => $filter['text'],

			'page' => $page,
			'perPage' => $perPage,
			'total' => $total
		];
		return $this->view('XF:Phrase\Listing', 'phrase_list', $viewParams);
	}

	public function actionImport()
	{
		if ($this->isPost())
		{
			$upload = $this->request->getFile('upload', false);
			if (!$upload)
			{
				return $this->error(\XF::phrase('please_upload_valid_language_xml_file'));
			}

			/** @var \XF\Service\Language\Import $languageImporter */
			$languageImporter = $this->service('XF:Language\Import');

			try
			{
				$document = \XF\Util\Xml::openFile($upload->getTempFile());
			}
			catch (\Exception $e)
			{
				$document = null;
			}

			if (!$languageImporter->isValidXml($document, $error))
			{
				return $this->error($error);
			}

			$input = $this->filter([
				'target' => 'str',
				'parent_language_id' => 'uint',
				'overwrite_language_id' => 'uint'
			]);

			if ($input['target'] == 'overwrite')
			{
				$overwriteLanguage = $this->assertRecordExists('XF:Language', $input['overwrite_language_id']);
				$languageImporter->setOverwriteLanguage($overwriteLanguage);
			}
			else
			{
				$parentLanguage = $input['parent_language_id']
					? $this->assertRecordExists('XF:Language', $input['parent_language_id'])
					: null;
				$languageImporter->setParentLanguage($parentLanguage);
			}

			if (!$this->filter('force', 'bool'))
			{
				if (!$languageImporter->isValidConfiguration($document, $errors))
				{
					return $this->error(\XF::phrase('import_verification_errors_x_select_skip_checks', [
						'errors' => implode(' ', $errors
					)]));
				}
			}

			$languageImporter->importFromXml($document);

			return $this->redirect($this->buildLink('languages'));
		}
		else
		{
			$viewParams = [
				'languageTree' => $this->repository('XF:Language')->getLanguageTree(false)
			];
			return $this->view('XF:Language\Import', 'language_import', $viewParams);
		}
	}

	public function actionExport(ParameterBag $params)
	{
		$language = $this->assertLanguageExists($params['language_id']);

		if ($this->isPost())
		{
			$this->setResponseType('xml');

			/** @var \XF\Service\Language\Export $languageExporter */
			$languageExporter = $this->service('XF:Language\Export', $language);

			$addOnId = $this->filter('addon_id', 'str');
			$addOn = $addOnId ? $this->assertRecordExists('XF:AddOn', $addOnId) : null;

			$languageExporter->setAddOn($addOn);
			$languageExporter->setIncludeUntranslated($this->filter('untranslated', 'bool'));

			$viewParams = [
				'language' => $language,
				'xml' => $languageExporter->exportToXml(),
				'filename' => $languageExporter->getExportFileName()
			];
			return $this->view('XF:Language\Export', '', $viewParams);
		}
		else
		{
			$viewParams = [
				'language' => $language
			];
			return $this->view('XF:Language\Export', 'language_export', $viewParams);
		}
	}

	public function actionAdmin()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canChangeLanguage($error))
		{
			return $this->noPermission($error);
		}

		$redirect = $this->getDynamicRedirect(null, true);

		if ($this->request->exists('language_id'))
		{
			$this->assertValidCsrfToken($this->filter('t', 'str'));

			$languageId = $this->filter('language_id', 'uint');

			$visitor->Admin->admin_language_id = $languageId;
			$visitor->Admin->save();

			return $this->redirect($redirect);
		}
		else
		{
			$viewParams = [
				'redirect' => $redirect,
				'languageTree' => $this->repository('XF:Language')->getLanguageTree(false)
			];
			return $this->view('XF:Language\Admin', 'language_chooser', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Language
	 */
	protected function assertLanguageExists($id, $with = null, $phraseKey = null)
	{
		if ($id === 0 || $id === "0")
		{
			return $this->getLanguageRepo()->getMasterLanguage();
		}

		return $this->assertRecordExists('XF:Language', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Language
	 */
	protected function getLanguageRepo()
	{
		return $this->repository('XF:Language');
	}

	/**
	 * @return \XF\Repository\Phrase
	 */
	protected function getPhraseRepo()
	{
		return $this->repository('XF:Phrase');
	}

	/**
	 * @return \XF\Repository\AddOn
	 */
	protected function getAddOnRepo()
	{
		return $this->repository('XF:AddOn');
	}
}