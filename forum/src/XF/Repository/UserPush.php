<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class UserPush extends Repository
{
	public function validateSubscriptionDetails(array $subscription, &$error = null)
	{
		if (empty($subscription['endpoint']))
		{
			return false;
		}

		if (!preg_match('#https?://#i', $subscription['endpoint']) || strlen($subscription['endpoint']) > 2048)
		{
			return false;
		}

		if (empty($subscription['key']) || empty($subscription['token']) || empty($subscription['encoding']))
		{
			return false;
		}

		if (
			strlen($subscription['key']) > 1024
			|| strlen($subscription['token']) > 256
			|| strlen($subscription['encoding']) > 64
		)
		{
			return false;
		}

		return true;
	}

	public function insertUserPushSubscription(\XF\Entity\User $user, array $subscription)
	{
		$db = $this->db();

		$endpointHash = $this->getEndpointHash($subscription['endpoint']);

		return $db->insert('xf_user_push_subscription', [
			'endpoint_hash' => $endpointHash,
			'endpoint' => $subscription['endpoint'],
			'user_id' => $user->user_id,
			'data' => json_encode([
				'key' => $subscription['key'],
				'token' => $subscription['token'],
				'encoding' => $subscription['encoding']
			]),
			'last_seen' => time()
		], false, '
			user_id = VALUES(user_id),
			data = VALUES(data),
			last_seen = VALUES(last_seen)
		');
	}

	public function deletePushSubscription(array $subscription)
	{
		$db = $this->db();
		$endpointHash = $this->getEndpointHash($subscription['endpoint']);
		return $db->delete('xf_user_push_subscription', 'endpoint_hash = ?', $endpointHash);
	}

	public function deleteUserPushSubscription(\XF\Entity\User $user, array $subscription)
	{
		$db = $this->db();
		$endpointHash = $this->getEndpointHash($subscription['endpoint']);
		return $db->delete('xf_user_push_subscription', 'endpoint_hash = ? AND user_id = ?', [
			$endpointHash, $user->user_id
		]);
	}

	public function limitUserPushSubscriptionCount(\XF\Entity\User $user, $maxAllowed)
	{
		$cutOff = max(0, intval($maxAllowed)); // offset is 0 based, so this will give the max+1 row

		$lastSeenCutOff = $this->db()->fetchOne("
			SELECT last_seen
			FROM xf_user_push_subscription
			WHERE user_id = ?
			ORDER BY last_seen DESC
			LIMIT ?, 1
		", [$user->user_id, $cutOff]);
		if ($lastSeenCutOff)
		{
			$this->db()->delete(
				'xf_user_push_subscription',
				'user_id = ? AND last_seen <= ?',
				[$user->user_id, $lastSeenCutOff]
			);
		}
	}

	public function getUserSubscriptions(\XF\Entity\User $user)
	{
		return $this->db()->fetchAll('
			SELECT *
			FROM xf_user_push_subscription
			WHERE user_id = ?
			ORDER BY endpoint_id
		', $user->user_id);
	}

	public function getEndpointHash($endpoint)
	{
		return md5($endpoint);
	}
}