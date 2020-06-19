<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Entity;

class Quote extends AbstractPlugin
{
	public function actionQuote(Entity $content, $context, $messageKey = 'message')
	{
		$message = $this->prepareMessage($content, $this->filter('quoteHtml', 'str,no-clean'), $messageKey);
		list($quote, $quoteHtml) = $this->getQuote($content, $message, $context);

		$view = $this->view('XF:Quote\Quote');
		$view->setJsonParams([
			'quote' => $quote,
			'quoteHtml' => $quoteHtml
		]);
		return $view;
	}

	public function actionMultiQuote($contentCollection, array $insertOrder, array $quotes, $context, $messageKey = 'message')
	{
		$output = [];

		foreach ($insertOrder AS $insert)
		{
			if (!isset($insert['id']) || !is_string($insert['id']))
			{
				continue;
			}

			list($messageId, $key) = explode('-', $insert['id'], 2);

			if (!isset($quotes[$messageId][$key]) || !isset($contentCollection[$messageId]))
			{
				continue;
			}

			$content = $contentCollection[$messageId];
			$quote = $quotes[$messageId][$key];

			if (is_string($quote))
			{
				$message = $quote;
			}
			else
			{
				$message = $this->prepareMessage($content, null, $messageKey);
			}

			if (!$message)
			{
				continue;
			}
			list($quote, $quoteHtml) = $this->getQuote($content, $message, $context);

			$output[] = ['quote' => $quote, 'quoteHtml' => $quoteHtml];
		}

		$view = $this->view('XF:Quote\MultiQuote');
		$view->setJsonParams($output);
		return $view;
	}

	public function prepareQuotes(array $quotes)
	{
		return array_map(function($quotes)
		{
			foreach ($quotes AS $i => &$quote)
			{
				if ($quote === null)
				{
					unset($quotes[$i]);
				}
				else if ($quote !== true)
				{
					$quote = $this->prepareMessage(null, $quote);
				}
			}
			return $quotes;
		}, $quotes);
	}

	public function prepareMessage($content = null, $html = null, $messageKey = 'message')
	{
		if ($html)
		{
			$message = $this->app->stringFormatter()->getBbCodeFromSelectionHtml($html);
			$message = $this->plugin('XF:Editor')->convertToBbCode($message);
		}
		else
		{
			if ($content instanceof Entity)
			{
				$message = $content->{$messageKey};
			}
			else
			{
				$message = null;
			}
		}

		return $message;
	}

	public function getQuote(Entity $content, $message, $context)
	{
		if (!($content instanceof \XF\Entity\QuotableInterface))
		{
			throw new \LogicException("Entity $content is not an instanceof \XF\Entity\QuotableInterface");
		}

		$quote = $content->getQuoteWrapper(
			$this->app->stringFormatter()->getBbCodeForQuote($message, $context)
		);
		$quoteHtml = $this->app->bbCode()->render($quote, 'editorHtml', 'editor', $content);

		return [$quote, $quoteHtml];
	}
}