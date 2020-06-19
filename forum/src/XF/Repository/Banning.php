<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\PrintableException;

class Banning extends Repository
{
	/**
	 * @return Finder
	 */
	public function findUserBansForList()
	{
		return $this->finder('XF:UserBan')
			->setDefaultOrder([['ban_date', 'DESC'], ['User.username']]);
	}

	public function banUser(\XF\Entity\User $user, $endDate, $reason, &$error = null, \XF\Entity\User $banBy = null)
	{
		if ($endDate < time() && $endDate !== 0) // 0 === permanent
		{
			$error = \XF::phraseDeferred('please_enter_a_date_in_the_future');
			return false;
		}

		$banBy = $banBy ?: \XF::visitor();

		/** @var \XF\Entity\UserBan $userBan */
		$userBan = $user->getRelationOrDefault('Ban', false);
		if ($userBan->isInsert())
		{
			$userBan->ban_user_id = $banBy->user_id;
		}

		$userBan->end_date = $endDate;
		if ($userBan->isChanged('end_date'))
		{
			$userBan->triggered = false;
		}
		$userBan->user_reason = $reason;

		if (!$userBan->preSave())
		{
			$errors = $userBan->getErrors();
			$error = reset($errors);
			return false;
		}

		try
		{
			$userBan->save(false);
		}
		catch (\XF\Db\Exception $e) {} // likely a race condition, keep the old value and accept

		return true;
	}

	public function deleteExpiredUserBans($cutOff = null)
	{
		foreach ($this->findExpiredUserBans($cutOff)->fetch() AS $userBan)
		{
			$userBan->delete();
		}
	}

	public function findExpiredUserBans($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = time();
		}

		return $this->finder('XF:UserBan')
			->where('end_date', '>', 0)
			->where('end_date', '<=', $cutOff);
	}

	/**
	 * @return Finder
	 */
	public function findEmailBans()
	{
		return $this->finder('XF:BanEmail')
			->setDefaultOrder('banned_email', 'asc');
	}

	public function banEmail($email, $reason = '', \XF\Entity\User $user = null)
	{
		$user = $user ?: \XF::visitor();

		$emailBan = $this->em->create('XF:BanEmail');
		$emailBan->banned_email = $email;
		$emailBan->reason = $reason;
		$emailBan->create_user_id = $user->user_id;

		return $emailBan->save();
	}

	public function isEmailBanned($email, array $bannedEmails)
	{
		foreach ($bannedEmails AS $bannedEmail)
		{
			$bannedEmailTest = str_replace('\\*', '(.*)', preg_quote($bannedEmail, '/'));
			if (preg_match('/^' . $bannedEmailTest . '$/i', $email))
			{
				return true;
			}
		}

		return false;
	}

	public function getBannedEntryFromEmail($email, array $bannedEmails)
	{
		foreach ($bannedEmails AS $bannedEmail)
		{
			$bannedEmailTest = str_replace('\\*', '(.*)', preg_quote($bannedEmail, '/'));
			if (preg_match('/^' . $bannedEmailTest . '$/i', $email))
			{
				return $bannedEmail;
			}
		}

		return null;
	}

	public function rebuildBannedEmailCache()
	{
		$cache = $this->findEmailBans()->fetch();
		$cache = $cache->pluckNamed('banned_email');

		\XF::registry()->set('bannedEmails', $cache);
		return $cache;
	}

	/**
	 * @return Finder
	 */
	public function findIpMatchesByRange($start, $end)
	{
		return $this->finder('XF:IpMatch')
			->where('start_range', $start)
			->where('end_range', $end);
	}

	/**
	 * @return Finder
	 */
	public function findIpBans()
	{
		return $this->finder('XF:IpMatch')
			->where('match_type', 'banned')
			->setDefaultOrder('start_range', 'asc');
	}

	public function banIp($ip, $reason = '', \XF\Entity\User $user = null)
	{
		$user = $user ?: \XF::visitor();

		list($niceIp, $firstByte, $startRange, $endRange) = $this->getIpRecord($ip);

		$ipBan = $this->em->create('XF:IpMatch');
		$ipBan->ip = $niceIp;
		$ipBan->match_type = 'banned';
		$ipBan->first_byte = $firstByte;
		$ipBan->start_range = $startRange;
		$ipBan->end_range = $endRange;
		$ipBan->reason = $reason;
		$ipBan->create_user_id = $user->user_id;

		return $ipBan->save();
	}

	public function getBannedIpCacheData()
	{
		$data = [];
		foreach ($this->findIpBans()->fetch() AS $ipBan)
		{
			$data[$ipBan->first_byte][] = [$ipBan->start_range, $ipBan->end_range];
		}

		return [
			'version' => time(),
			'data' => $data
		];
	}

	public function rebuildBannedIpCache()
	{
		$cache = $this->getBannedIpCacheData();
		\XF::registry()->set('bannedIps', $cache);
		return $cache;
	}

	/**
	 * @return Finder
	 */
	public function findDiscouragedIps()
	{
		return $this->finder('XF:IpMatch')
			->where('match_type', 'discouraged')
			->setDefaultOrder('start_range', 'asc');
	}

	public function discourageIp($ip, $reason = '', \XF\Entity\User $user = null)
	{
		$user = $user ?: \XF::visitor();

		list($niceIp, $firstByte, $startRange, $endRange) = $this->getIpRecord($ip);

		$discouragedIp = $this->em->create('XF:IpMatch');
		$discouragedIp->ip = $niceIp;
		$discouragedIp->match_type = 'discouraged';
		$discouragedIp->first_byte = $firstByte;
		$discouragedIp->start_range = $startRange;
		$discouragedIp->end_range = $endRange;
		$discouragedIp->reason = $reason;
		$discouragedIp->create_user_id = $user->user_id;

		return $discouragedIp->save();
	}

	public function getDiscouragedIpCacheData()
	{
		$data = [];
		foreach ($this->findDiscouragedIps()->fetch() AS $discouragedIp)
		{
			$data[$discouragedIp->first_byte][] = [$discouragedIp->start_range, $discouragedIp->end_range];
		}

		return [
			'version' => time(),
			'data' => $data
		];
	}

	public function rebuildDiscouragedIpCache()
	{
		$cache = $this->getDiscouragedIpCacheData();
		\XF::registry()->set('discouragedIps', $cache);
		return $cache;
	}

	protected function getIpRecord($ip)
	{
		$results = \XF\Util\Ip::parseIpRangeString($ip);
		if (!$results)
		{
			throw new PrintableException(\XF::phrase('please_enter_valid_ip_or_ip_range'));
		}

		return [
			$results['printable'],
			$results['binary'][0],
			$results['startRange'],
			$results['endRange']
		];
	}
}