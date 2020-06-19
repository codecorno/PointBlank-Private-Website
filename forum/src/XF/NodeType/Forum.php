<?php

namespace XF\NodeType;

class Forum extends AbstractHandler
{
	public function setupApiTypeDataEdit(
		\XF\Entity\Node $node, \XF\Entity\AbstractNode $data, \XF\InputFiltererArray $inputFilterer, \XF\Mvc\FormAction $form
	)
	{
		$forumInput = $inputFilterer->filter([
			'allow_posting' => '?bool',
			'allow_poll' => '?bool',
			'moderate_threads' => '?bool',
			'moderate_replies' => '?bool',
			'count_messages' => '?bool',
			'find_new' => '?bool',
			'allowed_watch_notifications' => '?str',
			'default_sort_order' => '?str',
			'default_sort_direction' => '?str',
			'list_date_limit_days' => '?uint',
			'default_prefix_id' => '?uint',
			'require_prefix' => '?bool',
			'min_tags' => '?uint'
		]);
		$forumInput = \XF\Util\Arr::filterNull($forumInput);

		/** @var \XF\Entity\Forum $data */
		$data->bulkSet($forumInput);
	}
}