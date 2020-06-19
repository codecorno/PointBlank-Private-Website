<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Mvc\Entity\Finder;

class ExportStyleProperties extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'style properties',
			'command' => 'style-properties',
			'entity' => 'XF:StyleProperty'
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
			$this->exportData($input, $output, 'style property groups', 'XF:StylePropertyGroup', $write);
		}

		return $returnCode;
	}

	protected function extraFinderConditions(Finder $finder)
	{
		$finder->where('style_id', 0);
	}
}