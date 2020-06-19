<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use XF\Db\Schema\Alter;

class ConvertUtf8mb4 extends Command
{
	protected function configure()
	{
		$this
			->setName('xf:convert-utf8mb4')
			->setDescription('Converts XenForo tables to utf8mb4');
	}

	protected $newCharset = 'utf8mb4';

	// TODO: this is using utf8mb4_general_ci most for BC reasons. Converting from utf8_general_ci to utf8mb4_unicode_ci
	// would be preferable, but it is not always viable due to more strings being considered identical. In future,
	// we may provide an option to use a different collation if people can manually deal with the issues.
	protected $newCollation = 'utf8mb4_general_ci';

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$db = \XF::db();

		$newCharset = $this->newCharset;
		$newCollation = $this->newCollation;

		$charset = $db->fetchRow("SHOW CHARACTER SET LIKE '{$newCharset}'");
		if (!$charset)
		{
			$output->writeln("<error>Your MySQL server does not support {$newCharset}. Please upgrade to a newer version.</error>");
			return 1;
		}

		$collation = $db->fetchAll("SHOW COLLATION LIKE '{$newCollation}'");
		if (!$collation)
		{
			$output->writeln("<error>Your MySQL server supports {$newCharset} but not {$newCollation}.</error>");
			return 1;
		}

		$regexParts = array_map(
			function($v) { return preg_quote($v, '/'); },
			$this->getTablePrefixes()
		);
		$regexMatch = '/^(' . implode('|', $regexParts) . ')/';

		$convertable = [];
		foreach ($db->fetchAll("SHOW TABLE STATUS") AS $table)
		{
			$name = $table['Name'];
			if (!preg_match($regexMatch, $name))
			{
				continue;
			}

			if (!preg_match('/^utf8_/', $table['Collation']))
			{
				continue;
			}

			$convertable[] = $name;
		}

		if (!$convertable)
		{
			$output->writeln("No convertable tables found. No action required.");
			return 0;
		}

		$totalConvertable = count($convertable);

		$output->writeln([
			"There are {$totalConvertable} tables to convert.",
			'',
			'Conversion may be a time consuming process.',
			'<info>You must close your installation and take a backup before beginning the conversion process!</info>',
			''
		]);

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');
		$question = new ConfirmationQuestion("<question>Are you ready to begin conversion? [y/n] </question>");
		if (!$helper->ask($input, $output, $question))
		{
			return 1;
		}

		$failed = [];

		$sm = $db->getSchemaManager();

		$output->writeln('Beginning conversion...');
		foreach ($convertable AS $i => $table)
		{
			$count = $i + 1;
			$output->write("[{$count}/{$totalConvertable}] $table... ");

			try
			{
				$sm->alterTable($table, function(Alter $alter) use ($newCharset, $newCollation)
				{
					$alter->convertCharset($newCharset, $newCollation);

					foreach ($alter->getColumnDefinitions() AS $column => $definition)
					{
						switch (strtolower($definition['Type']))
						{
							case 'char':
							case 'varchar':
							case 'tinytext':
							case 'text':
							case 'mediumtext':
							case 'longtext':
								$alter->changeColumn($column);
								break;
						}
					}
				});

				$output->write('Done', true);
			}
			catch (\XF\Db\Exception $e)
			{
				$failed[$table] = $e->getMessage();
				$output->write('<error>Failed</error>', true);
			}
		}

		if ($failed)
		{
			$output->writeln([
				'',
				'<error>The following tables failed to convert:</error>'
			]);
			foreach ($failed AS $table => $error)
			{
				$output->writeln("\t* $table: $error");
			}
			$output->writeln('<error>You should contact the table creator for guidance. Failure to correct this may lead to unexpected behavior.</error>');
		}

		$output->writeln([
			'',
			$failed ? 'Conversion complete, but with errors!' : 'Conversion complete!',
			"<info>You must now add the following to your src/config.php file:</info>",
			"<info>\$config['fullUnicode'] = true;</info>"
		]);

		return 0;
	}

	protected function getTablePrefixes()
	{
		return ['xf_', 'xengallery_'];
	}
}