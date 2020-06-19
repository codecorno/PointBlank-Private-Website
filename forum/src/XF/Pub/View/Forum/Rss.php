<?php

namespace XF\Pub\View\Forum;

class Rss extends \XF\Mvc\View
{
	public function renderRss()
	{
		$app = \XF::app();
		$router = $app->router('public');
		$options = $app->options();
		$forum = $this->params['forum'];

		$indexUrl = $router->buildLink('canonical:index');
		if ($forum)
		{
			$feedLink = $router->buildLink('canonical:forums/index.rss', $forum);
		}
		else
		{
			$feedLink = $router->buildLink('canonical:forums/index.rss', '-');
		}

		if ($forum)
		{
			$title = $forum->title;
			$description = $forum->description;
		}
		else
		{
			$title = $options->boardTitle;
			$description = $options->boardDescription;
		}

		$title = $title ?: $indexUrl;
		$description = $description ?: $title; // required in RSS 2.0 spec

		$feed = new \Zend\Feed\Writer\Feed();

		$feed->setEncoding('utf-8')
			->setTitle($title)
			->setDescription($description)
			->setLink($indexUrl)
			->setFeedLink($feedLink, 'rss')
			->setDateModified(\XF::$time)
			->setLastBuildDate(\XF::$time)
			->setGenerator($options->boardTitle);

		$parser = $app->bbCode()->parser();
		$rules = $app->bbCode()->rules('post:rss');

		$bbCodeCleaner = $app->bbCode()->renderer('bbCodeClean');
		$bbCodeRenderer = $app->bbCode()->renderer('html');

		$formatter = $app->stringFormatter();
		$maxLength = $options->discussionRssContentLength;

		/** @var \XF\Entity\Thread $thread */
		foreach ($this->params['threads'] AS $thread)
		{
			$threadForum = $thread->Forum;
			$entry = $feed->createEntry();

			$title = (empty($thread->title) ? \XF::phrase('title:') . ' ' . $thread->title : $thread->title);
			$entry->setTitle($title)
				->setLink($router->buildLink('canonical:threads', $thread))
				->setDateCreated($thread->post_date)
				->setDateModified($thread->last_post_date);

			if ($threadForum && !$forum)
			{
				$entry->addCategory([
					'term' => $threadForum->title,
					'scheme' => $router->buildLink('canonical:forums', $threadForum)
				]);
			}

			$firstPost = $thread->FirstPost;

			if ($maxLength && $firstPost && $firstPost->message)
			{
				$snippet = $bbCodeCleaner->render($formatter->wholeWordTrim($firstPost->message, $maxLength), $parser, $rules);

				if ($snippet != $firstPost->message)
				{
					$snippet .= "\n\n[URL='" . $router->buildLink('canonical:threads', $thread) . "']$thread->title[/URL]";
				}

				$renderOptions = $firstPost->getBbCodeRenderOptions('post:rss', 'html');
				$renderOptions['noProxy'] = true;

				$content = trim($bbCodeRenderer->render($snippet, $parser, $rules, $renderOptions));
				if (strlen($content))
				{
					$entry->setContent($content);
				}
			}

			$entry->addAuthor([
				'name' => $thread->username,
				'email' => 'invalid@example.com',
				'uri' => $router->buildLink('canonical:members', $thread)
			]);
			if ($thread->reply_count)
			{
				$entry->setCommentCount($thread->reply_count);
			}

			$feed->addEntry($entry);
		}

		return $feed->export('rss', true);
	}
}