<?php

namespace XF\Service\Banning\Emails;

use XF\Service\AbstractXmlImport;

class Import extends AbstractXmlImport
{
	public function import(\SimpleXMLElement $xml)
	{
		$bannedEmailsCache = (array)$this->app->container('bannedEmails');
		$bannedEmailsCache = array_map('strtolower', $bannedEmailsCache);

		$entries = $xml->entry;
		foreach ($entries AS $entry)
		{
			if (in_array(strtolower((string)$entry['banned_email']), $bannedEmailsCache))
			{
				// already exists
				continue;
			}

			$this->repository('XF:Banning')->banEmail(
				(string)$entry['banned_email'],
				\XF\Util\Xml::processSimpleXmlCdata($entry->reason)
			);
		}
	}
}