<?php

namespace XF\Spam\Checker;

use GuzzleHttp\Psr7\Request;

class Akismet extends AbstractProvider implements ContentCheckerInterface
{
	protected $client;

	/**
	 * @var array Map of XenForo content_type to Akismet comment_type.
	 *            See https://akismet.com/development/api/#comment-check
	 */
	protected $commentTypes = [
		'thread'                => 'forum-post',
		'post'                  => 'reply',
		'profile_post'          => 'blog',
		'profile_post_comment'  => 'comment',
		'user'                  => 'signup',
		'conversation_message'  => 'message',
		'contact'               => 'contact-form',

		// * - These are (probably) not officially supported by Akismet

		'user_signature'        => 'user-signature',    // *

		'media'                 => 'multi-media',       // *
		'album'                 => 'album',             // *
		'media_comment'         => 'comment',
		'media_album_comment'   => 'comment',

		'resource_rating'       => 'review',            // *
		'resource_update'       => 'blog-post',
	];

	protected function getType()
	{
		return 'Akismet';
	}

	public function check(\XF\Entity\User $user, $message, array $extraParams = [])
	{
		$decision = 'allowed';

		try
		{
			$isSpam = $this->isSpam($user, $message, $extraParams);
			if ($isSpam)
			{
				$decision = 'moderated';

				$this->logDetail('akismet_matched');
			}

			$this->logParam('akismetIsSpam', $isSpam);
			$this->logParam('akismet', $this->getParams($user, $message, $extraParams));
		}
		catch (\GuzzleHttp\Exception\RequestException $e) { $this->app()->logException($e, false, 'Akismet HTTP error: '); }
		catch (\InvalidArgumentException $e) { $this->app()->logException($e, false, 'Akismet service error: '); }

		$this->logDecision($decision);
	}

	public function submitSpam($contentType, $contentIds)
	{
		foreach ($this->getContentSpamCheckParams($contentType, $contentIds) AS $contentId => $params)
		{
			if ($params)
			{
				$this->_submitSpam($params);
			}
		}
	}

	protected function _submitSpam($params)
	{
		if (!empty($params['akismetIsSpam']))
		{
			return; // Akismet already told us this is spam, so do not submit again.
		}

		if (!empty($params['akismet']))
		{
			try
			{
				$params = [
					'headers' => [
						'User-Agent' => $this->getUserAgent()
					],
					'form_params' => $params['akismet'],
				];

				$response = $this->getHttpClient()->post('http://' . $this->getApiKey() . '.rest.akismet.com/1.1/submit-spam', $params);
				$response = trim($response->getBody()->getContents());

				if ($response == 'invalid')
				{
					throw new \InvalidArgumentException("Invalid API key");
				}
			}
			catch (\GuzzleHttp\Exception\RequestException $e) { $this->app()->logException($e, false, 'Akismet HTTP error: '); }
			catch (\InvalidArgumentException $e) { $this->app()->logException($e, false, 'Akismet service error: '); }
		}
	}

	public function submitHam($contentType, $contentIds)
	{
		foreach ($this->getContentSpamCheckParams($contentType, $contentIds) AS $contentId => $params)
		{
			if ($params)
			{
				$this->_submitHam($params);
			}
		}
	}

	protected function _submitHam($params)
	{
		if (empty($params['akismetIsSpam']))
		{
			return; // Akismet already told us this wasn't spam, so do not submit as ham.
		}

		if (!empty($params['akismet']))
		{
			try
			{
				$params = [
					'headers' => [
						'User-Agent' => $this->getUserAgent()
					],
					'form_params' => $params['akismet'],
				];

				$response = $this->getHttpClient()->post('http://' . $this->getApiKey() . '.rest.akismet.com/1.1/submit-ham', $params);
				$response = trim($response->getBody()->getContents());

				if ($response == 'invalid')
				{
					throw new \InvalidArgumentException("Invalid API key");
				}
			}
			catch (\GuzzleHttp\Exception\RequestException $e) { $this->app()->logException($e, false, 'Akismet HTTP error: '); }
			catch (\InvalidArgumentException $e) { $this->app()->logException($e, false, 'Akismet service error: '); }
		}
	}

	protected function getParams(\XF\Entity\User $user, $message, $extraParams)
	{
		$options = $this->app()->options();
		$request = $this->app()->request();

		$params = [
			'blog' => $options->boardUrl,
			'user_ip' => $request->getIp(),
			'user_agent' => $request->getUserAgent(),
			'referrer' => $request->getServer('HTTP_REFERER', ''),
			'comment_type' => 'comment',
			'comment_author' => $user->username,
			'comment_author_email' => $user->email,
			'comment_author_url' => $user->Profile ? $user->Profile->website : '',
			'comment_content' => $message
		];
		if (isset($extraParams['content_type']))
		{
			$params['comment_type'] = $this->getCommentType($extraParams['content_type']);
		}
		if (isset($extraParams['permalink']))
		{
			$params['permalink'] = $extraParams['permalink'];
		}

		return $params;
	}

	protected function isSpam(\XF\Entity\User $user, $message, $extraParams)
	{
		$params = [
			'headers' => [
				'User-Agent' => $this->getUserAgent()
			],
			'form_params' => $this->getParams($user, $message, $extraParams),
		];

		$response = $this->getHttpClient()->post('http://' . $this->getApiKey() . '.rest.akismet.com/1.1/comment-check', $params);
		$response = trim($response->getBody()->getContents());

		if ($response == 'invalid')
		{
			throw new \InvalidArgumentException("Invalid API key");
		}

		return ($response == 'true');
	}

	/**
	 * @return \GuzzleHttp\Client
	 */
	protected function getHttpClient()
	{
		if (!$this->client)
		{
			$this->client = $this->app->http()->client();
		}

		return $this->client;
	}

	/**
	 * @return Request
	 */
	protected function getHttpRequest($url = null, $method = 'POST')
	{
		$client = $this->getHttpClient();
		$request = new Request($method, $url, [
			'headers' => [
				'User-Agent' => $this->getUserAgent()
			]
		]);

		return $request;
	}

	protected function getUserAgent()
	{
		$version = \XF::$version;
		return 'XenForo/' . $version . ' | Akismet/' . $version;
	}

	protected function getCommentType($contentType)
	{
		if (isset($this->commentTypes[$contentType]))
		{
			return $this->commentTypes[$contentType];
		}

		return 'comment';
	}

	protected function getApiKey()
	{
		return trim($this->app()->options()->akismetKey);
	}
}