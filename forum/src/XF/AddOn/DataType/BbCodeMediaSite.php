<?php

namespace XF\AddOn\DataType;

class BbCodeMediaSite extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:BbCodeMediaSite';
	}

	public function getContainerTag()
	{
		return 'bb_code_media_sites';
	}

	public function getChildTag()
	{
		return 'site';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('media_site_id')->fetch();

		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdataToNewNode($node, 'match_urls', $entry);
			$this->exportCdataToNewNode($node, 'embed_html', $entry);

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

		$ids = $this->pluckXmlAttribute($entries, 'media_site_id');
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

			/** @var \XF\Entity\BbCodeMediaSite $entity */
			$entity = isset($existing[$id]) ? $existing[$id] : $this->create();

			if ($addOnId == 'XF' && $entity->media_site_id && $entity->addon_id != 'XF')
			{
				continue;
			}

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$this->importMappedAttributes($entry, $entity);

			$entity->match_urls = $this->getCdataValue($entry->match_urls);

			$template = $entity->getMasterTemplate();
			$template->template = $this->getCdataValue($entry->embed_html);
			$entity->addCascadedSave($template);

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
		$this->deleteOrphanedSimple($addOnId, $container, 'media_site_id');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\BbCodeMediaSite $repo */
			$repo = $this->em->getRepository('XF:BbCodeMediaSite');
			$repo->rebuildBbCodeMediaSiteCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'media_site_id',
			'site_title',
			'site_url',
			'match_is_regex',
			'match_callback_class',
			'match_callback_method',
			'embed_html_callback_class',
			'embed_html_callback_method',
			'supported',
			'active',
			'oembed_enabled',
			'oembed_api_endpoint',
			'oembed_url_scheme',
			'oembed_retain_scripts',
		];
	}

	protected function getMaintainedAttributes()
	{
		return [
			'supported',
			'active'
		];
	}
}