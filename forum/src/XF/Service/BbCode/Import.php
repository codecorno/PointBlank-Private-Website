<?php

namespace XF\Service\BbCode;

use XF\Service\AbstractXmlImport;

class Import extends AbstractXmlImport
{
	public function import(\SimpleXMLElement $xml)
	{
		$xmlBbCodes = $xml->bb_code;

		$existing = $this->finder('XF:BbCode')
			->where('bb_code_id', $this->getBbCodeIds($xmlBbCodes))
			->order('bb_code_id')
			->fetch();

		foreach ($xmlBbCodes AS $xmlBbCode)
		{
			$data = $this->getBbCodeDataFromXml($xmlBbCode);
			$phrases = $this->getBbCodePhrasesFromXml($xmlBbCode);

			$bbCodeId = $data['bb_code_id'];

			if (isset($existing[$bbCodeId]))
			{
				/** @var \XF\Entity\BbCode $bbCode */
				$bbCode = $this->em()->find('XF:BbCode', $bbCodeId);
			}
			else
			{
				$bbCode = $this->em()->create('XF:BbCode');
			}
			$bbCode->bulkSet($data);
			$bbCode->save(false);

			foreach ($phrases AS $type => $text)
			{
				/** @var \XF\Entity\Phrase $masterPhrase */
				$masterPhrase = $bbCode->getMasterPhrase($type);
				$masterPhrase->phrase_text = $text;
				$masterPhrase->save();
			}
		}
	}

	protected function getBbCodeIds(\SimpleXMLElement $xmlBbCodes)
	{
		$bbCodeIds = [];
		foreach ($xmlBbCodes AS $xmlBbCode)
		{
			$bbCodeIds[] = (string)$xmlBbCode['bb_code_id'];
		}
		return $bbCodeIds;
	}

	protected function getBbCodeDataFromXml(\SimpleXMLElement $xmlBbCode)
	{
		$bbCodeData = [];

		foreach ($this->getAttributes() AS $attr)
		{
			$bbCodeData[$attr] = (string)$xmlBbCode[$attr];
		}

		$bbCodeData['replace_html'] = \XF\Util\Xml::processSimpleXmlCdata($xmlBbCode->replace_html);
		$bbCodeData['replace_html_email'] = \XF\Util\Xml::processSimpleXmlCdata($xmlBbCode->replace_html_email);
		$bbCodeData['replace_text'] = \XF\Util\Xml::processSimpleXmlCdata($xmlBbCode->replace_text);

		$bbCodeData['active'] = 1;
		$bbCodeData['addon_id'] = '';

		return $bbCodeData;
	}

	protected function getBbCodePhrasesFromXml(\SimpleXMLElement $xmlBbCode)
	{
		return [
			'title' => (string)$xmlBbCode['title'],
			'desc' => \XF\Util\Xml::processSimpleXmlCdata($xmlBbCode->desc),
			'example' => \XF\Util\Xml::processSimpleXmlCdata($xmlBbCode->example),
			'output' => \XF\Util\Xml::processSimpleXmlCdata($xmlBbCode->output)
		];
	}

	protected function getAttributes()
	{
		return [
			'bb_code_id', 'bb_code_mode', 'has_option',
			'callback_class', 'callback_method', 'option_regex',
			'trim_lines_after', 'plain_children', 'disable_smilies',
			'disable_nl2br', 'disable_autolink', 'allow_empty',
			'allow_signature', 'editor_icon_type', 'editor_icon_value'
		];
	}
}