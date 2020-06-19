<?php

namespace XF\Service;

use Minishlink\WebPush\Encryption;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use XF\Entity\User;
use XF\Language;

class PushNotification extends AbstractService
{
	/**
	 * @var User
	 */
	protected $receiver;

	/**
	 * @var Language
	 */
	protected $language;

	/**
	 * @var array
	 */
	protected $subscriptions;

	/**
	 * @var array
	 */
	protected $payloadData = [];

	public function __construct(\XF\App $app, User $receiver)
	{
		parent::__construct($app);
		$this->receiver = $receiver;
		$this->language = $app->language($receiver->language_id);
	}

	public function setNotificationContent($title, $body, $url = null)
	{
		$this->payloadData['title'] = $title;
		$this->payloadData['body'] = $body;
		$this->payloadData['url'] = \XF::canonicalizeUrl($url);
	}

	public function setIconAndBadge($icon, $badge = null)
	{
		$this->payloadData['icon'] = \XF::canonicalizeUrl($icon);

		if ($badge)
		{
			$this->payloadData['badge'] = \XF::canonicalizeUrl($badge);
		}
	}

	public function setDirection($direction)
	{
		$this->payloadData['dir'] = $direction;
	}

	public function setNotificationTag($tag)
	{
		$this->payloadData['tag'] = $tag;
	}

	public function setCustomPayloadData($name, $value)
	{
		$this->payloadData[$name] = $value;
	}

	protected function applyPayloadDefaults()
	{
		$options = $this->app->options();
		$language = $this->language;

		$this->payloadData = array_replace([
			'title' => $language->phrase('notification_from_x', ['boardTitle' => $options->boardTitle])->render(),
			'body' => $language->phrase('you_have_new_notification_at_x', ['boardTitle' => $options->boardTitle])->render(),
			'url' => $options->boardUrl,
			'badge' => $this->getDefaultBadgeForVisitor(),
			'icon' => $this->getDefaultIconForVisitor(),
			'dir' => $language->isRtl() ? 'rtl' : 'ltr',
			'tag' => '',
			'tag_phrase' => $language->phrase('(plus_x_previous)')->render() // {count} is calculated on client
		], $this->payloadData);

		return $this->payloadData;
	}

	public function isPushAvailable()
	{
		$options = $this->app->options();

		return (
			$options->enablePush
			&& $options->pushKeysVAPID['publicKey']
			&& $options->pushKeysVAPID['privateKey']
			&& $this->isReceiverSubscribed()
		);
	}

	public function isReceiverSubscribed()
	{
		$subscriptions = $this->getReceiverSubscriptions();
		return boolval(count($subscriptions));
	}

	protected function getReceiverSubscriptions()
	{
		if ($this->subscriptions === null)
		{
			$this->subscriptions = $this->getUserPushRepository()->getUserSubscriptions($this->receiver);
		}

		return $this->subscriptions;
	}

	public function sendNotifications()
	{
		if (!$this->isPushAvailable())
		{
			return;
		}

		$payload = $this->applyPayloadDefaults();
		$webPush = $this->getWebPushObject();

		$webPush->setDefaultOptions([
			'TTL' => 86400, // if undelivered after 1 day, expire the notification
		]);

		$subscriptions = $this->getReceiverSubscriptions();

		foreach ($subscriptions AS $subscription)
		{
			$authData = json_decode($subscription['data'], true);

			try
			{
				$webPush->setAutomaticPadding($this->getEndpointPadding($subscription['endpoint']));

				$subObj = Subscription::create([
					'endpoint' => $subscription['endpoint'],
					'publicKey' => $authData['key'],
					'authToken' => $authData['token'],
					'contentEncoding' => $authData['encoding']
				]);

				$webPush->sendNotification(
					$subObj,
					json_encode($payload)
				);
			}
			catch (\Exception $e)
			{
				// generally indicates that the payload is too big which at ~3000 bytes shouldn't happen for a typical alert...
				if (\XF::$debugMode)
				{
					\XF::logException($e);
				}
				continue;
			}
		}

		$results = $webPush->flush();
		$this->handleResults($results, $subscriptions);
	}

	protected function getEndpointPadding($endpoint)
	{
		if (strpos($endpoint, 'mozilla') !== false)
		{
			// firefox, at least on Android, has an issue with automatic padding which
			// is used to make encryption more secure, but makes encryption slower.
			// see: https://github.com/web-push-libs/web-push-php/issues/108
			// TODO: ideally won't need this forever if Mozilla ever fix this or if library works-around it
			return 0;
		}

		if (strpos($endpoint, '.ucweb.com') !== false)
		{
			// See https://xenforo.com/community/threads/158252/
			return 0;
		}

		return Encryption::MAX_COMPATIBILITY_PAYLOAD_LENGTH;
	}

	protected function handleResults($results, array $subscriptions)
	{
		$db = $this->db();

		$pushRepo = $this->getUserPushRepository();

		if (is_array($results)) // may have errors
		{
			foreach ($results AS $result)
			{
				if ($result['success'])
				{
					if (isset($result['endpoint']))
					{
						$endpointHash = $pushRepo->getEndpointHash($result['endpoint']);

						$db->update('xf_user_push_subscription', [
							'last_seen' => time()
						], 'endpoint_hash = ?', $endpointHash);
					}
					else
					{
						// Mozilla doesn't appear to send anything other than 'success' in its successful results.
						continue;
					}
				}
				else if (!empty($result['statusCode']))
				{
					switch ($result['statusCode'])
					{
						// 401 and 403 generally represent when something in the request doesn't match, likely
						// due to VAPID keys changing
						case 401:
						case 403:
						case 404:
						case 410:
							if (isset($result['endpoint']))
							{
								$endpointHash = $pushRepo->getEndpointHash($result['endpoint']);
								$db->delete('xf_user_push_subscription', 'endpoint_hash = ?', $endpointHash);
							}
							break;

						case 406:
							// not a server error but rate limiting - future pushes should work
						case 500:
						case 502:
						case 503:
						case 504:
							// these indicate server errors that are likely temporary - future pushes should work
							break;

						default:
							\XF::logError("Push notification failure: " . \GuzzleHttp\json_encode($result));
							break;
					}
				}
				else
				{
					\XF::logError("Push notification failure: " . \GuzzleHttp\json_encode($result));
				}
			}
		}
		else // no errors so mark all as seen
		{
			$endpointHashes = array_map(
				[$pushRepo, 'getEndpointHash'],
				array_column($subscriptions, 'endpoint')
			);

			$db->update('xf_user_push_subscription', [
				'last_seen' => time()
			], 'endpoint_hash IN(' . $db->quote($endpointHashes) . ')');
		}
	}

	/**
	 * @return WebPush
	 */
	protected function getWebPushObject()
	{
		$options = $this->app->options();

		$auth = [
			'VAPID' => [
				'subject' => $options->boardUrl
			] + $options->pushKeysVAPID
		];
		$httpOptions = $this->app->http()->getDefaultClientOptions();

		return new WebPush($auth, [], 10, $httpOptions);
	}

	protected function getDefaultBadgeForVisitor()
	{
		$style = $this->app->style($this->receiver->style_id);
		$badge = $style->getProperty('publicPushBadgeUrl', null);
		if ($badge)
		{
			$badge = \XF::canonicalizeUrl($badge);
		}

		return $badge;
	}

	protected function getDefaultIconForVisitor()
	{
		$style = $this->app->style($this->receiver->style_id);
		$icon = $style->getProperty('publicMetadataLogoUrl', null);
		if ($icon)
		{
			$icon = \XF::canonicalizeUrl($icon);
		}

		return $icon;
	}

	/**
	 * @return \XF\Mvc\Entity\Repository|\XF\Repository\UserPush
	 */
	protected function getUserPushRepository()
	{
		return $this->repository('XF:UserPush');
	}
}