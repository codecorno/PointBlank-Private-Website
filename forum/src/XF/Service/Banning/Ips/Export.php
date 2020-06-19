<?php

namespace XF\Service\Banning\Ips;

use XF\Mvc\Entity\Entity;
use XF\Service\AbstractXmlExport;

class Export extends AbstractXmlExport
{
	public function getRootName()
	{
		return 'banned_ips';
	}

	protected function exportEntry(Entity $entity, \DOMElement $node)
	{
		$reasonNode = $node->ownerDocument->createElement('reason');
		$this->exportCdata($reasonNode, $entity->reason);
		$node->appendChild($reasonNode);
	}

	protected function getAttributes()
	{
		return [
			'ip', 'match_type'
		];
	}
}