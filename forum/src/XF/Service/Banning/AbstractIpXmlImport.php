<?php

namespace XF\Service\Banning;

use XF\Service\AbstractXmlImport;

abstract class AbstractIpXmlImport extends AbstractXmlImport
{
	abstract protected function getMethod();

	public function import(\SimpleXMLElement $xml)
	{
		$banMethod = $this->getMethod();
		$banningRepo = $this->repository('XF:Banning');

		if (!\XF\Util\Php::validateCallbackPhrased($banningRepo, $banMethod, $error))
		{
			throw new \XF\PrintableException($error);
		}

		$entries = $xml->entry;

		$ips = [];
		$type = null;

		foreach ($entries AS $entry)
		{
			$ips[] = (string)$entry['ip'];
			$type = (string)$entry['match_type'];
		}

		$existing = $this->finder('XF:IpMatch')
			->where('ip', $ips)
			->where('match_type', $type)
			->keyedBy('ip')
			->fetch();

		foreach ($entries AS $entry)
		{
			if (isset($existing[(string)$entry['ip']]))
			{
				// already exists
				continue;
			}

			$banningRepo->$banMethod((string)$entry['ip'], \XF\Util\Xml::processSimpleXmlCdata($entry->reason));
		}
	}
}