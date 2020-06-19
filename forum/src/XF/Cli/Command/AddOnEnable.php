<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddOnEnable extends Command
{
	use AddOnActionTrait;

	protected function configure()
	{
		$this
			->setName('xf:addon-enable')
			->setAliases(['xf-addon:enable'])
			->setDescription('Enable the specified add-on. If no add-on ID is provided, all disabled add-ons will be enabled.')
			->addArgument(
				'id',
				InputArgument::OPTIONAL,
				'Add-On ID'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$addOnManager = \XF::app()->addOnManager();

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$id = $input->getArgument('id');
		if ($id)
		{
			$addOn = $this->checkEditableAddOn($id, $error);
			if (!$addOn)
			{
				$output->writeln('<error>' . $error . '</error>');
				return 1;
			}

			if ($addOn->isActive())
			{
				$output->writeln('This add-on is already enabled.');
				return 0; // the effect is the same as success so lets treat it like that
			}

			$question = new ConfirmationQuestion('<question>' . \XF::phrase('please_confirm_that_you_want_to_enable_following_add_on:') . ' (' . $addOn->title . ' ' . $addOn->version_string . ') (y/n)</question>');
			$response = $helper->ask($input, $output, $question);
			if (!$response)
			{
				return 1;
			}

			$addOns = [$addOn];
			$clearDisabledCache = false;
		}
		else
		{
			$addOns = $addOnManager->getInstalledAddOns();

			$question = new ConfirmationQuestion('<question>' . \XF::phrase('you_sure_you_want_to_re_enable_previously_disabled_add_ons') . '</question>');
			$response = $helper->ask($input, $output, $question);
			if (!$response)
			{
				return 1;
			}
			$clearDisabledCache = true;
		}

		$db = \XF::db();
		$db->beginTransaction();

		foreach ($addOns AS $addOnId => $addOn)
		{
			if (!$addOn->canEdit() || $addOn->isActive())
			{
				unset($addOns[$addOnId]);
				continue;
			}

			$installed = $addOn->getInstalledAddOn();
			$installed->setOption('rebuild_active_change', false);
			$installed->active = true;
			$installed->save(true, false);
		}

		$db->commit();

		foreach ($addOns AS $addOn)
		{
			$this->runSubAction($output, $addOn, 'active-change');
		}

		if ($clearDisabledCache)
		{
			$addOnRepo = \XF::repository('XF:AddOn');
			$addOnRepo->setDisabledAddOnsCache([]);
		}

		return 0;
	}
}