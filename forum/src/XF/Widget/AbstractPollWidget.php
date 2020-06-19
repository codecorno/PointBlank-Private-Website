<?php

namespace XF\Widget;

abstract class AbstractPollWidget extends AbstractWidget
{
	/**
	 * @param $url
	 * @param null $error
	 * @return \XF\Entity\Poll | null
	 */
	abstract public function getPollFromRoutePath($url, &$error = null);

	public function getContent()
	{
		return \XF::app()->findByContentType(
			$this->options['content_type'], $this->options['content_id'], $this->getEntityWith()
		);
	}

	public function getEntityWith()
	{
		return ['Poll'];
	}

	protected function getDefaultTemplateParams($context)
	{
		$params = parent::getDefaultTemplateParams($context);

		if ($context == 'options' && isset($this->options['content_type'], $this->options['content_id']))
		{
			$content = $this->getContent();
			$params['content'] = $content;
			$params['poll'] = $content->Poll;
		}

		return $params;
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		if ($request->filter('poll_id', 'uint') && !$request->filter('change_url', 'str'))
		{
			// Already configured, make no changes.
			$options = $this->options;
			return true;
		}

		$contentUrl = $request->filter('content_url', 'str');

		$routePath = $this->prepareContentUrl($contentUrl);

		$poll = $this->getPollFromRoutePath($routePath, $error);
		if (!$poll)
		{
			return false;
		}
		if (!$poll->canViewContent($error))
		{
			return false;
		}

		$options = [
			'poll_id' => $poll->poll_id,
			'content_type' => $poll->content_type,
			'content_id' => $poll->content_id
		];
		return true;
	}

	/**
	 * Convert the provided content URL into just the route path.
	 *
	 * @param $url
	 * @return string
	 */
	protected function prepareContentUrl($url)
	{
		$indexRoute = $this->app->router('public')->buildLink('full:index');
		if (strpos($url, $indexRoute) == 0)
		{
			$url = ltrim(str_replace($indexRoute, '', $url), '?');
		}
		if (strpos($url, '?') !== false || strpos($url, '&') !== false)
		{
			$position = strpos($url, '?') ?: strpos($url, '&');
			$url = substr($url, $position);
		}
		return $url;
	}
}