<?php

namespace XF\NodeType;

abstract class AbstractHandler
{
	protected $nodeTypeId;
	protected $info;

	abstract public function setupApiTypeDataEdit(
		\XF\Entity\Node $node, \XF\Entity\AbstractNode $data, \XF\InputFiltererArray $inputFilterer, \XF\Mvc\FormAction $form
	);

	public function __construct($nodeTypeId, array $info)
	{
		$this->nodeTypeId = $nodeTypeId;
		$this->info = $info;
	}
}