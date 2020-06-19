<?php

namespace XF\Service\BbCode;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Service\AbstractXmlExport;

class Export extends AbstractXmlExport
{
	public function getRootName()
	{
		return 'bb_codes';
	}

	public function getChildName()
	{
		return 'bb_code';
	}

	protected function exportEntry(Entity $entity, \DOMElement $node)
	{
		$node->setAttribute('title', $this->getPhrase($entity, 'title'));

		$childNodes = [
			'desc' => $this->getPhrase($entity, 'desc'),
			'example' => $this->getPhrase($entity, 'example'),
			'output' => $this->getPhrase($entity, 'output'),
			'replace_html' => $entity->replace_html,
			'replace_html_email' => $entity->replace_html_email,
			'replace_text' => $entity->replace_text
		];
		foreach ($childNodes AS $attr => $value)
		{
			$childNode = $node->ownerDocument->createElement($attr);
			$this->exportCdata($childNode, $value);
			$node->appendChild($childNode);
		}
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