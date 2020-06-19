<?php

namespace XF\ControllerPlugin;

class CodeEditor extends AbstractPlugin
{
	public function actionModeLoader($language)
	{
		/** @var \XF\Data\CodeLanguage $languageData */
		$languageData = $this->data('XF:CodeLanguage');
		$languages = $languageData->getSupportedLanguages(true);

		if (isset($languages[$language]))
		{
			$modeConfig = $languages[$language];
		}
		else
		{
			$modeConfig = [];
		}

		$reply = $this->view('XF:CodeEditor\ModeLoader', 'public:code_editor_mode_loader', [
			'modeConfig' => $modeConfig
		]);

		if (isset($modeConfig['modes']))
		{
			if (is_array($modeConfig['modes']))
			{
				$mode = reset($modeConfig['modes']);
			}
			else
			{
				$mode = $modeConfig['modes'];
			}
		}
		else
		{
			$mode = '';
		}

		$reply->setJsonParams([
			'mode' => $mode,
			'mime' => isset($modeConfig['mime']) ? $modeConfig['mime'] : '',
			'config' => isset($modeConfig['config']) ? $modeConfig['config'] : [],
			'language' => $language
		]);

		return $reply;
	}
}