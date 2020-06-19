<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Util\File;
use XF\Util\Json;

class MetadataFix extends Command
{
	protected function configure()
	{
		$this
			->setName('xf-dev:metadata-fix')
			->setDescription('Internal use only: Compare metadata files across branches in order to automatically fix conflicts.')
			->addArgument(
				'root',
				InputArgument::REQUIRED,
				'Path to XF root to compare against.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$root = realpath($input->getArgument('root'));
		$ourRoot = \XF::getRootDirectory();

		$templatesPath = "src/addons/XF/_output/templates";

		$metadata = json_decode(file_get_contents("$root/$templatesPath/_metadata.json"), true);
		$ourMetadata = json_decode(file_get_contents("$ourRoot/$templatesPath/_metadata.json"), true);

		foreach ($metadata AS $template => $_metadata)
		{
			if (!isset($ourMetadata[$template]))
			{
				// template apparently doesn't exist in our system, let's check
				if (!file_exists("$ourRoot/$templatesPath/$template"))
				{
					// it doesn't so ignore
					continue;
				}

				$ourMetadata[$template] = $_metadata;
			}

			if ($ourMetadata[$template]['version_id'] !== $_metadata['version_id']
				|| $ourMetadata[$template]['version_string'] !== $_metadata['version_string']
				|| $ourMetadata[$template]['hash'] !== $_metadata['hash']
			)
			{
				if ($ourMetadata[$template]['version_id'] < $_metadata['version_id'])
				{
					$ourMetadata[$template]['version_id'] = $_metadata['version_id'];
					$ourMetadata[$template]['version_string'] = $_metadata['version_string'];
				}
				else
				{
					$ourMetadata[$template]['version_id'] = \XF::$versionId;
					$ourMetadata[$template]['version_string'] = \XF::$version;
				}

				$contents = file_get_contents("$ourRoot/$templatesPath/$template");
				$contents = str_replace("\r", '', $contents);
				$ourMetadata[$template]['hash'] = md5($contents);
			}
		}

		ksort($ourMetadata);

		File::writeFile("$ourRoot/$templatesPath/_metadata.json", Json::jsonEncodePretty($ourMetadata), false);

		return 0;
	}
}