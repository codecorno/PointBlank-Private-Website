<?php

namespace XF\Alert;

class Post extends AbstractHandler
{
	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}

	public function getOptOutActions()
	{
		return [
			'forumwatch_insert',
			'insert',
			'quote',
			'mention',
			'reaction'
		];
	}

	public function getTemplateName($action)
	{
		if ($action == 'forumwatch_insert')
		{
			$action = 'insert';
		}

		return parent::getTemplateName($action);
	}

	public function getOptOutDisplayOrder()
	{
		return 100;
	}
}