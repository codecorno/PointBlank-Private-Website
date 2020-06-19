<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Legacy\DataWriter;

class GenerateEntityDw extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:generate-entity-dw')
			->setDescription('Generates an entity from a legacy DataWriter')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Identifier for the DataWriter and Entity (Prefix:Type format)'
			)
			->addOption(
				'write',
				null,
				InputOption::VALUE_NONE,
				'If enabled, writes to the appropriate file'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('id');
		if (!$id || !preg_match('#^[a-z0-9_\\\\]+:[a-z0-9_\\\\]+$#i', $id))
		{
			$output->writeln("Identifier in the form of Prefix:Type must be provided.");
			return 1;
		}

		try
		{
			$dw = \XF\Legacy\DataWriter::create($id);
		}
		catch (\Exception $e)
		{
			$class = \XF::stringToClass($id, '%s\Legacy\DataWriter\%s');
			$output->writeln("Legacy class for $id ($class) could not be created.");
			return 2;
		}

		list($prefix, $type) = explode(':', $id);

		$classParts = explode('\\', $type);
		$className = array_pop($classParts);
		$namespace = "$prefix\\Entity";
		if ($classParts)
		{
			$namespace .= "\\" . implode("\\", $classParts);
		}

		$fields = $dw->getFields();
		if (count($fields) > 1)
		{
			$output->writeln("This tool does not support DataWriters with more than 1 table.");
			return 3;
		}

		$tableName = key($fields);
		$fields = reset($fields);

		$primaryKey = null;
		$columns = [];
		foreach ($fields AS $columnName => $field)
		{
			$record = [];
			$forced = false;

			switch ($field['type'])
			{
				case DataWriter::TYPE_BINARY: $type = 'self::BINARY'; break;
				case DataWriter::TYPE_BOOLEAN: $type = 'self::BOOL'; break;
				case DataWriter::TYPE_STRING: $type = 'self::STR'; break;
				case DataWriter::TYPE_INT: $type = 'self::INT'; break;
				case DataWriter::TYPE_UINT: $type = 'self::UINT'; break;
				case DataWriter::TYPE_UINT_FORCED: $type = 'self::UINT'; $forced = true; break;
				case DataWriter::TYPE_FLOAT: $type = 'self::FLOAT'; break;
				case DataWriter::TYPE_SERIALIZED: $type = 'self::SERIALIZED'; break;
				case DataWriter::TYPE_JSON: $type = 'self::JSON_ARRAY'; break;
				case DataWriter::TYPE_UNKNOWN: $type = 'self::BINARY'; break;
				default: $type = 'self::BINARY';
			}

			$record[] = "'type' => $type";
			if ($forced)
			{
				$record[] = "'forced' => true";
			}

			if (!empty($field['autoIncrement']))
			{
				$record[] = "'autoIncrement' => true";
				$primaryKey = $columnName;
			}

			if (!empty($field['required']))
			{
				if (!empty($field['requiredError']))
				{
					$record[] = "\n\t\t\t\t'required' => '" . addslashes($field['requiredError']) . "'";
				}
				else
				{
					$record[] = "'required' => true";
				}
			}

			if (array_key_exists('default', $field) && !is_array($field['default']))
			{
				if (is_string($field['default']))
				{
					$record[] = "'default' => '" . addslashes($field['default']) . "'";
				}
				else if (is_numeric($field['default']))
				{
					$record[] = "'default' => " . floatval($field['default']);
				}
				else if (is_bool($field['default']))
				{
					$record[] = "'default' => " . ($field['default'] ? 'true' : 'false');
				}
				else if ($field['default'] === null)
				{
					$record[] = "'default' => null";
				}
			}

			if (!empty($field['maxLength']))
			{
				$record[] = "'maxLength' => " . intval($field['maxLength']);
			}
			if (isset($field['min']))
			{
				$record[] = "'min' => " . floatval($field['min']);
			}
			if (isset($field['max']))
			{
				$record[] = "'max' => " . floatval($field['max']);
			}

			if (!empty($field['allowedValues']))
			{
				$record[] = "\n\t\t\t\t'allowedValues' => ['"  . implode('\', \'', $field['allowedValues']) .  "']";
			}

			$code = implode(', ', $record);
			$trailingBreak = (strpos($code, "\n") ? "\n\t\t\t" : '');
			$columns[] = "'$columnName' => [$code$trailingBreak]";
		}

		if (!$primaryKey)
		{
			reset($fields);
			$primaryKey = key($fields);
		}

		$columnOutput = implode(",\n\t\t\t", $columns);

		$fileOutput = <<< ENTITYOUT
<?php

namespace {$namespace};

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class {$className} extends Entity
{
	public static function getStructure(Structure \$structure)
	{
		\$structure->table = '{$tableName}';
		\$structure->shortName = '{$id}';
		\$structure->primaryKey = '{$primaryKey}';
		\$structure->columns = [
			{$columnOutput}
		];
		\$structure->getters = [];
		\$structure->relations = [];

		return \$structure;
	}
}
ENTITYOUT;

		if ($input->getOption('write'))
		{
			$parts = explode('\\', $namespace);
			if ($parts[0] === 'XF')
			{
				$path = \XF::getSourceDirectory() . \XF::$DS . implode(\XF::$DS, $parts);
			}
			else
			{
				$path = \XF::getAddOnDirectory() . \XF::$DS . implode(\XF::$DS, $parts);
			}

			$fileName = $path . \XF::$DS . $className . '.php';
			$output->writeln("Writing $fileName...");

			if (file_exists($fileName))
			{
				$output->writeln("File already exists, aborting.");
				return 4;
			}
			if (!is_writable(dirname($fileName)))
			{
				$output->writeln("File could not be written to. Check directories exist and permissions.");
				return 5;
			}

			file_put_contents($fileName, $fileOutput);
			$output->writeln("Written successfully.");
		}
		else
		{
			$output->write($fileOutput);
		}

		return 0;
	}
}