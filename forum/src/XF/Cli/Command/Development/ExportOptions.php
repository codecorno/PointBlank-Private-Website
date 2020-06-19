<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportOptions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'options',
			'command' => 'options',
			'entity' => 'XF:Option'
		];
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$returnCode = parent::execute($input, $output);
		if (!$returnCode)
		{
			// success
			$write = function($entity)
			{
				\XF::app()->developmentOutput()->export($entity);
			};
			$this->exportData($input, $output, 'option groups', 'XF:OptionGroup', $write);
		}

		return $returnCode;
	}
}