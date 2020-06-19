<?php

namespace XF\NodeType;

class Page extends AbstractHandler
{
	public function setupApiTypeDataEdit(
		\XF\Entity\Node $node, \XF\Entity\AbstractNode $data, \XF\InputFiltererArray $inputFilterer, \XF\Mvc\FormAction $form
	)
	{
		$typeInput = $inputFilterer->filter([
			'log_visits' => '?bool',
			'list_siblings' => '?bool',
			'list_children' => '?bool',
			'advanced_mode' => '?bool'
		]);
		$typeInput = \XF\Util\Arr::filterNull($typeInput);

		/** @var \XF\Entity\Page $data */
		$data->bulkSet($typeInput);
		$data->modified_date = \XF::$time;

		$templateInput = $inputFilterer->filter('template', '?str');
		if ($templateInput !== null)
		{
			$template = $data->getMasterTemplate();
			$template->template = $templateInput;
			$data->addCascadedSave($template);
		}
	}
}