<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Page extends AbstractNode
{
	protected function getNodeTypeId()
	{
		return 'Page';
	}

	protected function getDataParamName()
	{
		return 'page';
	}

	protected function getTemplatePrefix()
	{
		return 'page';
	}

	protected function getViewClassPrefix()
	{
		return 'XF:Page';
	}

	protected function saveTypeData(FormAction $form, \XF\Entity\Node $node, \XF\Entity\AbstractNode $data)
	{
		/** @var \XF\Entity\Page $data */

		$pageInput = $this->filter([
			'log_visits' => 'bool',
			'list_siblings' => 'bool',
			'list_children' => 'bool',
			'callback_class' => 'str',
			'callback_method' => 'str',
			'advanced_mode' => 'bool'
		]);
		$data->bulkSet($pageInput);
		$data->modified_date = \XF::$time;

		$template = $data->getMasterTemplate();
		$templateInput = $this->filter('template', 'str');
		$form->validate(function(FormAction $form) use ($templateInput, $template)
		{
			if (!$template->set('template', $templateInput))
			{
				$form->logErrors($template->getErrors());
			}
		});
		$form->apply(function() use ($template)
		{
			if ($template)
			{
				$template->save();
			}
		});
	}
}