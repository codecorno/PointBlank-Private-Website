<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use XF\Install\Data\MySql;

class Install extends Command implements CustomAppCommandInterface
{
	use JobRunnerTrait;

	public static function getCustomAppClass()
	{
		return 'XF\Install\App';
	}

	protected function configure()
	{
		$this
			->setName('xf:install')
			->setDescription('Installs XenForo')
			->addOption(
				'user',
				null,
				InputOption::VALUE_REQUIRED,
				'Name of the administrator user (default: Admin)'
			)
			->addOption(
				'password',
				null,
				InputOption::VALUE_REQUIRED,
				'Password of the administrator user'
			)
			->addOption(
				'email',
				null,
				InputOption::VALUE_OPTIONAL,
				'Primary board email and email for administrator user (default: (empty))'
			)
			->addOption(
				'title',
				null,
				InputOption::VALUE_REQUIRED,
				'Board title (default: XenForo)'
			)
			->addOption(
				'url',
				null,
				InputOption::VALUE_REQUIRED,
				'Board URL (default: http://localhost)'
			)
			->addOption(
				'skip-statistics',
				null,
				InputOption::VALUE_NONE,
				'If set, the question to opt into anonymous server statistics collection will be skipped.'
			)
			->addOption(
				'clear',
				null,
				InputOption::VALUE_NONE,
				'If set, existing application tables will be cleared before installing. If not set and tables are found, an error will be triggered.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// TODO: Should phrase the remainder of the text in this class?

		/** @var \XF\Install\App $app */
		$app = \XF::app();

		$data = new MySql();
		$config = \XF::config();

		$installHelper = new \XF\Install\Helper($app);

		if ($installHelper->isInstalled())
		{
			$output->writeln('<error>' . \XF::phrase('you_have_completed_installation_to_reinstall') . '</error>');
			return 1;
		}

		if (!$config['exists'])
		{
			$output->writeln('<error>' . \XF::phrase('config_file_x_could_not_be_found', ['file' => 'src/config.php']) . '</error>');
			return 1;
		}

		$db = \XF::db();

		$errors = $installHelper->getRequirementErrors($db);
		if ($errors)
		{
			$output->writeln('<error>' . \XF::phrase('following_errors_were_found_when_verifying_requirements:') . '</error>');
			foreach ($errors AS $error)
			{
				$output->writeln("<info>\t * " . (string)$error . '</info>');
			}
			return 1;
		}

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$warnings = $installHelper->getRequirementWarnings($db);
		if ($warnings)
		{
			$output->writeln('<warning>' . \XF::phrase('following_warnings_were_found_when_verifying_requirements:') . '</warning>');
			foreach ($warnings AS $warning)
			{
				$output->writeln("<info>\t * " . (string)$warning . '</info>');
			}
			$output->writeln("");

			$warningQ = new ConfirmationQuestion(\XF::phrase('you_sure_you_want_to_continue_cli'));
			if (!$helper->ask($input, $output, $warningQ))
			{
				return 1;
			}
		}

		$command = $this->getApplication()->find('xf:file-check');
		$childInput = new ArrayInput([
			'command' => 'xf:file-check',
			'--addon' => 'XF'
		]);
		$result = $command->run($childInput, $output);

		if ($result === 1)
		{
			return 1;
		}

		if (isset($config['db']['dbname']))
		{
			$output->writeln("Database name: " . $config['db']['dbname']);
		}

		if ($installHelper->hasApplicationTables() && !$input->getOption('clear'))
		{
			$output->writeln('<error>' . \XF::phrase('you_cannot_proceed_unless_tables_removed_cli') . '</error>');
			return 1;
		}

		$username = $input->getOption('user');
		if (!$username)
		{
			$question = new Question('<question>Name of the administrator user (default: Admin):</question> ', 'Admin');
			$username = $helper->ask($input, $output, $question);
			$output->writeln("");
		}

		/** @var \XF\Validator\Username $usernameValidator */
		$usernameValidator = $app->validator('Username');
		$usernameValidator->setOption('allow_censored', true);
		$usernameValidator->setOption('check_unique', false);
		$username = $usernameValidator->coerceValue($username);
		if (!$usernameValidator->isValid($username, $errorKey))
		{
			$output->writeln('<error>' . $usernameValidator->getPrintableErrorValue($errorKey) . '</error>');
			return 1;
		}

		$password = $input->getOption('password');
		if (!$password)
		{
			$question = new Question('<question>Password of the administrator user:</question> ');
			$question->setValidator(function ($value)
			{
				if (trim($value) == '')
				{
					throw new \InvalidArgumentException('The password can not be empty');
				}

				return $value;
			});
			$question->setHidden(true);
			$password = $helper->ask($input, $output, $question);
			$output->writeln("");

			// we can just trust the password input if it was entered when executing the command, otherwise ask:
			$question = new Question('<question>Re-enter the administrator user password to confirm:</question> ');
			$question->setValidator(function ($value) use ($password)
			{
				if ($value !== $password)
				{
					throw new \InvalidArgumentException('Passwords did not match. Please enter the same password.');
				}

				return $value;
			});
			$question->setHidden(true);
			$question->setMaxAttempts(5);
			$helper->ask($input, $output, $question);
			$output->writeln("");
		}

		$email = $input->getOption('email');
		if (!$email)
		{
			$question = new Question('<question>Primary board email and email for administrator user (default: example@example.com):</question> ', 'example@example.com');
			$email = $helper->ask($input, $output, $question);
			$output->writeln("");
		}

		/** @var \XF\Validator\Email $emailValidator */
		$emailValidator = $app->validator('Email');
		$emailValidator->setOption('allow_empty', true);
		$emailValidator->setOption('check_typos', true);
		$email = $emailValidator->coerceValue($email);
		if (!$emailValidator->isValid($email, $errorKey))
		{
			if ($errorKey == 'typo')
			{
				$output->writeln('<error>' . \XF::phrase('email_address_you_entered_appears_have_typo') . '</error>');
			}
			else
			{
				$output->writeln('<error>' . \XF::phrase('please_enter_valid_email') . '</error>');
			}
			return 1;
		}

		if (!$password)
		{
			$output->writeln('<error>' . \XF::phrase('please_enter_valid_password') . '</error>');
			return 1;
		}

		$title = $input->getOption('title');
		if (!$title)
		{
			$question = new Question('<question>Board title (default: XenForo):</question> ', 'XenForo');
			$title = $helper->ask($input, $output, $question);
			$output->writeln("");
		}

		$url = $input->getOption('url');
		if (!$url)
		{
			$question = new Question('<question>Board URL (default: http://localhost):</question> ', 'http://localhost');
			$url = $helper->ask($input, $output, $question);
			$output->writeln("");
		}

		if ($installHelper->hasApplicationTables() && $input->getOption('clear'))
		{
			$output->write("Deleting existing tables... ");
			$installHelper->deleteApplicationTables();
			$output->writeln("Done.");
		}

		$sm = $db->getSchemaManager();

		$tables = $data->getTables();

		$output->writeln("Creating tables...");

		$progress = new ProgressBar($output, count($tables));
		$progress->start();

		foreach ($tables AS $tableName => $definition)
		{
			$sm->createTable($tableName, $definition);
			$progress->advance();
		}

		$progress->finish();
		$output->writeln("");
		$output->writeln("Done.");

		$output->writeln("Creating default data...");

		$data = $data->getData();

		$progress = new ProgressBar($output, count($data));
		$progress->start();

		foreach ($data AS $dataQuery)
		{
			$db->query($dataQuery);
			$progress->advance();
		}

		$progress->finish();
		$output->writeln("");
		$output->writeln("Done. Importing data...");
		$output->writeln("");

		$devOutput = $app->developmentOutput();
		if ($devOutput->isEnabled() && $devOutput->isCoreXfDataAvailable())
		{
			$command = $this->getApplication()->find('xf-dev:import');
			$childInput = new ArrayInput([
				'command' => 'xf-dev:import',
				'--addon' => 'XF'
			]);
			$command->run($childInput, $output);
		}
		else
		{
			$command = $this->getApplication()->find('xf:rebuild-master-data');
			$childInput = new ArrayInput(['command' => 'xf:rebuild-master-data']);
			$childInput->setInteractive(false);
			$command->run($childInput, $output);
		}

		$output->writeln("Done. Apply installation configuration...");
		$output->writeln("");

		$installHelper->createInitialUser([
			'username' => $username,
			'email' => $email
		], $password);

		$serverStats = [
			'configured' => 1,
			'enabled' => 0
		];
		if (!$input->getOption('skip-statistics'))
		{
			$question = new ConfirmationQuestion("<question>Send anonymous server statistics (PHP, MySQL, XF versions)? (y/n)</question> ");
			if ($helper->ask($input, $output, $question))
			{
				$serverStats['enabled'] = 1;
			}
		}

		/** @var \XF\Repository\Option $optionRepo */
		$optionRepo = \XF::repository('XF:Option');

		// if applicable, updating collectServerStats will enqueue stats collection automatically
		$optionRepo->updateOptions([
			'boardTitle' => $title,
			'boardUrl' => $url,
			'contactEmailAddress' => $email,
			'defaultEmailAddress' => $email,
			'collectServerStats' => $serverStats
		]);

		$installHelper->completeInstallation();

		$output->writeln("");
		$output->writeln("All finished. Installation has been completed.");
		$output->writeln("");
		$output->writeln("Values set:");
		$output->writeln("\t* Username: $username");
		$output->writeln("\t* Email: $email");
		$output->writeln("\t* Password: " . str_repeat('*', strlen($password)) . ' (Confirmed)');
		$output->writeln("\t* Title: $title");
		$output->writeln("\t* URL: $url");

		return 0;
	}
}