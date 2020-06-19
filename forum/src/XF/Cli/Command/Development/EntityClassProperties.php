<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Mvc\Entity\Entity;

class EntityClassProperties extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:entity-class-properties')
			->setDescription('Applies class properties to type hint columns, getters and relations')
			->addArgument(
				'addon-or-entity',
				InputArgument::REQUIRED,
				'Add-on ID or specific Entity short name to generate Entity class properties for. Note: Existing class properties will be overwritten.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$entities = [];

		$addOnOrEntity = $input->getArgument('addon-or-entity');
		if (strpos($addOnOrEntity, ':') !== false)
		{
			$entities[] = $addOnOrEntity;
		}
		else
		{
			if ($addOnOrEntity === 'XF')
			{
				$path = \XF::getSourceDirectory() . \XF::$DS . 'XF' . \XF::$DS . 'Entity';
				$addOnId = 'XF';
			}
			else
			{
				$manager = \XF::app()->addOnManager();
				$addOn = $manager->getById($addOnOrEntity);
				if (!$addOn || !$addOn->isAvailable())
				{
					$output->writeln('Add-on could not be found.');
					return 1;
				}

				$addOnId = $addOn->getAddOnId();
				$path = $manager->getAddOnPath($addOnId) . \XF::$DS . 'Entity';
			}

			if (!file_exists($path) || !is_dir($path))
			{
				$output->writeln('<error>The selected add-on does not appear to have an Entity directory.</error>');
				return 1;
			}

			$iterator = new \RegexIterator(
				\XF\Util\File::getRecursiveDirectoryIterator($path, null, null), '/\.php$/'
			);

			/** @var \SplFileInfo $file */
			foreach ($iterator AS $name => $file)
			{
				$name = str_replace('.php', '', $file->getFilename());
				$subDir = substr($file->getPath(), strlen($path));
				$subDir = ltrim(str_replace('/', '\\', $subDir) . '\\', '\\');
				$entities[] = str_replace('/', '\\', $addOnId) . ':' . $subDir . $name;
			}
		}

		if (!$entities)
		{
			$output->writeln('<error>No entity classes could be found.</error>');
			return 1;
		}

		foreach ($entities AS $entity)
		{
			$class = \XF::stringToClass($entity, '%s\Entity\%s');

			$reflection = new \ReflectionClass($class);
			if (!$reflection->isInstantiable() || !$reflection->isSubclassOf('XF\Mvc\Entity\Entity'))
			{
				continue;
			}

			$structure = $class::getStructure(new \XF\Mvc\Entity\Structure());

			$path = realpath(\XF::$autoLoader->findFile($class));
			$contents = file_get_contents($path);

			$output->writeln("Writing class properties for entity $entity");

			$docPlaceholder = $this->getDocPlaceholder();
			$existingComment = $reflection->getDocComment();

			if (!$existingComment)
			{
				$search = 'class ' . $reflection->getShortName() . ' extends ';
				$replace = "$docPlaceholder\n$search";
				$newContents = str_replace($search, $replace, $contents);
			}
			else
			{
				$newContents = str_replace($existingComment, $docPlaceholder, $contents);
			}

			$typeMap = $this->getEntityTypeMap();

			$getters = [];
			foreach ($structure->getters AS $getter => $def)
			{
				if (is_array($def) && isset($def['getter']) && is_string($def['getter']))
				{
					$methodName = $def['getter'];
				}
				else
				{
					$methodName = 'get' . ucfirst(\XF\Util\Php::camelCase($getter));
				}
				if (!$reflection->hasMethod($methodName))
				{
					continue;
				}
				$method = $reflection->getMethod($methodName);

				$type = 'mixed';
				$comment = $method->getDocComment();
				if ($comment)
				{
					if (preg_match('/^\s*?\*\s*?@return\s+(.*)$/mi', $comment, $matches))
					{
						$type = $matches[1];
					}
				}

				$getters[$getter] = [
					'type' => trim($type)
				];
			}

			$columns = [];
			foreach ($structure->columns AS $column => $def)
			{
				if (isset($getters[$column]))
				{
					// There's an overlapping getter so this column
					// is only accessible via the bypass suffix.
					$column .= '_';
				}
				$columns[$column] = [
					'type' => !empty($def['typeHint']) ? $def['typeHint'] : $typeMap[$def['type']],
					'null' => !empty($def['nullable']) ? true : false
				];
			}

			$relations = [];
			foreach ($structure->relations AS $relation => $def)
			{
				if (isset($getters[$relation]))
				{
					// There's an overlapping getter so this relation
					// is only accessible via the bypass suffix.
					$relation .= '_';
				}
				$relations[$relation] = [
					'type' => \XF::stringToClass($def['entity'], '%s\Entity\%s'),
					'many' => ($def['type'] === Entity::TO_MANY)
				];
			}

			$newComment = '/**' . "\n";

			if ($columns)
			{
				$newComment .= ' * COLUMNS';
				foreach ($columns AS $column => $type)
				{
					$newComment .= "\n" . ' * @property ' . $type['type'] . ($type['null'] ? '|null' : '') . ' ' . $column;
				}
			}

			if ($getters)
			{
				if ($columns)
				{
					$newComment .= "\n *\n";
				}
				$newComment .= ' * GETTERS';
				foreach ($getters AS $getter => $type)
				{
					$newComment .= "\n" . ' * @property ' . $type['type'] . ' ' . $getter;
				}
			}

			if ($relations)
			{
				if ($columns || $getters)
				{
					$newComment .= "\n *\n";
				}
				$newComment .= ' * RELATIONS';
				foreach ($relations AS $relation => $type)
				{
					$typeProp = '\\' . ltrim($type['type'], '\\');

					if ($type['many'])
					{
						$typeProp = '\XF\Mvc\Entity\AbstractCollection|' . $typeProp . '[]';
					}

					$newComment .= "\n" . ' * @property ' . $typeProp . ' ' . $relation;
				}
			}

			$newComment .= "\n */";

			$newContents = str_replace($docPlaceholder, $newComment, $newContents);

			if (\XF\Util\File::writeFile($path,$newContents, false))
			{
				$output->writeln("Written out class properties for entity $entity");
			}
			else
			{
				$output->writeln("Could not write out class properties for entity $entity");
			}
			$output->writeln("");
		}

		$output->writeln("Done!");
		return 0;
	}

	protected function getDocPlaceholder()
	{
		return '/** <XF:DOC_COMMENT> */';
	}

	protected function getEntityTypeMap()
	{
		return [
			Entity::INT => 'int',
			Entity::UINT => 'int',
			Entity::FLOAT => 'float',
			Entity::BOOL => 'bool',
			Entity::STR => 'string',
			Entity::BINARY => 'string',
			Entity::SERIALIZED => 'array|bool', // try to decode but bool on failure
			Entity::SERIALIZED_ARRAY => 'array',
			Entity::JSON => 'array|null', // try to decode but null on failure
			Entity::JSON_ARRAY => 'array',
			Entity::LIST_LINES => 'array',
			Entity::LIST_COMMA => 'array'
		];
	}
}
