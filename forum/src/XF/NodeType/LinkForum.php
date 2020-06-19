<?php

namespace XF\NodeType;

class LinkForum extends AbstractHandler
{
	public function setupApiTypeDataEdit(
		\XF\Entity\Node $node, \XF\Entity\AbstractNode $data, \XF\InputFiltererArray $inputFilterer, \XF\Mvc\FormAction $form
	)
	{
		$typeInput = $inputFilterer->filter([
			'link_url' => '?str'
		]);
		$typeInput = \XF\Util\Arr::filterNull($typeInput);
		$data->bulkSet($typeInput);
	}
}