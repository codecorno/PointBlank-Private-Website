<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class FindNew extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$path = strval($params->path);
		if (!strlen($path))
		{
			return $this->redirectPermanently($this->buildLink('whats-new'));
		}

		$parts = explode('/', $path);
		if (count($parts) == 1)
		{
			if ($parts[0] == 'threads')
			{
				// very legacy link
				return $this->redirect($this->buildLink('whats-new/posts'));
			}

			return $this->redirect($this->buildLink('whats-new/' . $parts[0]));
		}

		list($id, $type) = $parts;

		return $this->redirect($this->buildLink(
			'whats-new/' . $type,
			['find_new_id' => $id]
		));
	}
}