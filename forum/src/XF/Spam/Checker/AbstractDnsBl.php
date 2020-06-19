<?php

namespace XF\Spam\Checker;

abstract class AbstractDnsBl extends AbstractProvider
{
	protected function checkIp($host, $getFinalOctet = true)
	{
		$ip = $this->app()->request()->getIp();
		$dnsBlCache = $this->app()->registry()['dnsBlCache'] ?: [];
		if (!empty($dnsBlCache[$ip]) && $dnsBlCache[$ip]['expiry'] > time())
		{
			return $this->processDecision($dnsBlCache[$ip]['type']); // seen before
		}

		$parts = explode('.', trim($ip));
		if (count($parts) != 4)
		{
			return false;
		}

		$parts = array_map('intval', $parts);
		$parts = array_reverse($parts);

		$query = sprintf($host, implode('.', $parts));

		$result = gethostbyname($query);
		if (!$result)
		{
			return false;
		}

		if ($result === $query)
		{
			// not found
			return false;
		}

		$resultParts = explode('.', $result);
		if (count($resultParts) < 4)
		{
			return false;
		}

		if ($getFinalOctet)
		{
			return $this->getFinalOctetIf($resultParts);
		}
		else
		{
			return $resultParts;
		}
	}

	protected function getFinalOctetIf($resultParts, $part0 = '127', $part1 = '0', $part2 = '0')
	{
		if (!is_array($resultParts) || count($resultParts) != 4)
		{
			return false;
		}

		if ($resultParts[0] == $part0 && $resultParts[1] == $part1 && $resultParts[2] == $part2)
		{
			return intval($resultParts[3]);
		}
		else
		{
			return false;
		}
	}

	protected function processDecision($block, $cacheResult = false)
	{
		$decision = 'allowed';

		if ($block)
		{
			$action = $this->app()->options()->registrationCheckDnsBl['action'];
			if ($action == 'block')
			{
				$decision = 'denied';
			}
			else
			{
				$decision = 'moderated';
			}

			$this->logDetail('dnsbl_matched');
		}

		if ($cacheResult)
		{
			$registry= $this->app()->registry();
			$ip = $this->app()->request()->getIp();
			$dnsBlCache = $registry['dnsBlCache'] ?: [];

			$dnsBlCache[$ip] = ['type' => $block, 'expiry' => time() + 3600];
			foreach ($dnsBlCache AS $key => $data)
			{
				if ($data['expiry'] <= time())
				{
					unset($dnsBlCache[$key]);
				}
			}
			$registry->set('dnsBlCache', $dnsBlCache);
		}

		$this->logDecision($decision);
		return false;
	}
}