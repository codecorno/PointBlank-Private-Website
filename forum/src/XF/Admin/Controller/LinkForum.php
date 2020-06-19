<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class LinkForum extends AbstractNode
{
	protected function getNodeTypeId()
	{
		return 'LinkForum';
	}

	protected function getDataParamName()
	{
		return 'link';
	}

	protected function getTemplatePrefix()
	{
		return 'link_forum';
	}

	protected function getViewClassPrefix()
	{
		return 'XF:LinkForum';
	}

	protected function saveTypeData(FormAction $form, \XF\Entity\Node $node, \XF\Entity\AbstractNode $data)
	{
		$input = $this->filter([
			'link_url' => 'str'
		]);
		$data->bulkSet($input);
	}
}