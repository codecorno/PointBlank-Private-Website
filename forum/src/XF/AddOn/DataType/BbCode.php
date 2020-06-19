<?php

namespace XF\AddOn\DataType;

class BbCode extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:BbCode';
	}

	public function getContainerTag()
	{
		return 'bb_codes';
	}

	public function getChildTag()
	{
		return 'bb_code';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('bb_code_id')->fetch();

		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdataToNewNode($node, 'replace_html', $entry);
			$this->exportCdataToNewNode($node, 'replace_html_email', $entry);
			$this->exportCdataToNewNode($node, 'replace_text', $entry);

			$container->appendChild($node);
		}

		return $entries->count() ? true : false;
	}

	public function importAddOnData($addOnId, \SimpleXMLElement $container, $start = 0, $maxRunTime = 0)
	{
		$startTime = microtime(true);

		$entries = $this->getEntries($container, $start);
		if (!$entries)
		{
			return false;
		}

		$ids = $this->pluckXmlAttribute($entries, 'bb_code_id');
		$existing = $this->findByIds($ids);

		$i = 0;
		$last = 0;
		foreach ($entries AS $entry)
		{
			$id = $ids[$i++];

			if ($i <= $start)
			{
				continue;
			}

			$entity = isset($existing[$id]) ? $existing[$id] : $this->create();
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$this->importMappedAttributes($entry, $entity);

			$entity->replace_html = $this->getCdataValue($entry->replace_html);
			$entity->replace_html_email = $this->getCdataValue($entry->replace_html_email);
			$entity->replace_text = $this->getCdataValue($entry->replace_text);

			$entity->addon_id = $addOnId;
			$entity->save(true, false);

			if ($this->resume($maxRunTime, $startTime))
			{
				$last = $i;
				break;
			}
		}
		return ($last ?: false);
	}

	public function deleteOrphanedAddOnData($addOnId, \SimpleXMLElement $container)
	{
		$this->deleteOrphanedSimple($addOnId, $container, 'bb_code_id');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\BbCode $repo */
			$repo = $this->em->getRepository('XF:BbCode');
			$repo->rebuildBbCodeCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'bb_code_id',
			'bb_code_mode',
			'has_option',
			'callback_class',
			'callback_method',
			'option_regex',
			'trim_lines_after',
			'plain_children',
			'disable_smilies',
			'disable_nl2br',
			'disable_autolink',
			'allow_empty',
			'allow_signature',
			'editor_icon_type',
			'editor_icon_value',
			'active'
		];
	}

	protected function getMaintainedAttributes()
	{
		return [
			'active',
			'allow_signature'
		];
	}
}