<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class Language extends AbstractPlugin
{
	public function getActiveLanguageId()
	{
		$languageId = $this->request->getCookie('edit_language_id', null);
		if ($languageId === null)
		{
			$languageId = \XF::$developmentMode ? 0 : $this->options()->defaultLanguageId;
		}
		$languageId = intval($languageId);

		if ($languageId == 0 && !\XF::$developmentMode)
		{
			$languageId = $this->options()->defaultLanguageId;
		}

		return $languageId;
	}

	/**
	 * Gets the active editable language.
	 *
	 * @return \XF\Entity\Language
	 */
	public function getActiveEditLanguage()
	{
		$languageId = $this->getActiveLanguageId();

		if ($languageId == 0)
		{
			/** @var \XF\Repository\Language $languageRepo */
			$languageRepo = $this->repository('XF:Language');
			$language = $languageRepo->getMasterLanguage();
		}
		else
		{
			$language = $this->em()->find('XF:Language', $languageId);
		}

		/** @var $language \XF\Entity\Language */
		if (!$language || !$language->canEdit())
		{
			$language = $this->em()->find('XF:Language', $this->options()->defaultLanguageId);
		}

		return $language;
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Language
	 */
	public function assertLanguageExists($id, $with = null, $phraseKey = null)
	{
		if ($id === 0 || $id === "0")
		{
			/** @var \XF\Repository\Language $languageRepo */
			$languageRepo = $this->repository('XF:Language');
			return $languageRepo->getMasterLanguage();
		}

		return $this->controller->assertRecordExists('XF:Language', $id, $with, $phraseKey);
	}
}