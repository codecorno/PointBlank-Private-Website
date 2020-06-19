<?php

namespace XF\Cli\Command\Designer;

class ImportTemplates extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'templates',
			'command' => 'templates',
			'dir' => 'templates',
			'entity' => 'XF:Template'
		];
	}

	protected function getTitleIdMap($typeDir, $styleId)
	{
		return \XF::db()->fetchPairs("
			SELECT CONCAT(type, '/', title), template_id
			FROM xf_template
			WHERE style_id = ?
		", $styleId);
	}

	public function importData($typeDir, $fileName, $path, $content, \XF\Entity\Style $style, array $metadata)
	{
		/** @var \XF\DesignerOutput\Template $designerOutputHandler */
		$designerOutputHandler = \XF::app()->designerOutput()->getHandler('XF:Template');
		$title = $designerOutputHandler->convertTemplateFileToName($fileName);
		$template = $designerOutputHandler->import($title, $style->style_id, $content, $metadata, [
			'import' => true
		]);
		return "$template->type/$template->title";
	}
}