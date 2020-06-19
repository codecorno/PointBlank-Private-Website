<?php

namespace XF\Searcher;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Manager;

/**
 * @method \XF\Finder\Thread getFinder()
 */
class Thread extends AbstractSearcher
{
	protected $allowedRelations = ['Forum'];

	protected $formats = [
		'title' => 'like',
		'username' => 'like',
		'post_date' => 'date',
		'last_post_date' => 'date'
	];

	protected $arrayValueKeys = [
		'thread_field'
	];

	protected $whitelistOrder = [
		'title' => true,
		'username' => true,
		'post_date' => true,
		'last_post_date' => true,
		'reply_count' => true,
		'view_count' => true,
		'first_post_reaction_score' => true
	];

	protected $order = [['last_post_date', 'desc']];

	protected function getEntityType()
	{
		return 'XF:Thread';
	}

	protected function getDefaultOrderOptions()
	{
		return [
			'last_post_date' => \XF::phrase('last_message'),
			'post_date' => \XF::phrase('start_date'),
			'title' => \XF::phrase('title'),
			'reply_count' => \XF::phrase('replies'),
			'view_count' => \XF::phrase('views'),
			'first_post_reaction_score' => \XF::phrase('first_message_reaction_score')
		];
	}

	protected function applySpecialCriteriaValue(Finder $finder, $key, $value, $column, $format, $relation)
	{
		if ($key == 'prefix_id' && $value == -1)
		{
			// any prefix so skip condition
			return true;
		}

		if ($key == 'node_id' && $value == 0)
		{
			// any node so skip condition
			return true;
		}

		if ($key == 'not_discussion_type')
		{
			$finder->where('discussion_type', '<>', $value);
			return true;
		}

		if ($key == 'thread_field')
		{
			$exactMatchFields = !empty($value['exact']) ? $value['exact'] : [];
			$customFields = array_merge($value, $exactMatchFields);
			unset($customFields['exact']);

			foreach ($customFields AS $fieldId => $value)
			{
				if ($value === '' || (is_array($value) && !$value))
				{
					continue;
				}

				$finder->with('CustomFields|' . $fieldId);
				$isExact = !empty($exactMatchFields[$fieldId]);
				$conditions = [];

				foreach ((array)$value AS $possible)
				{
					$columnName = 'CustomFields|' . $fieldId . '.field_value';
					if ($isExact)
					{
						$conditions[] = [$columnName, '=', $possible];
					}
					else
					{
						$conditions[] = [$columnName, 'LIKE', $finder->escapeLike($possible, '%?%')];
					}
				}

				if ($conditions)
				{
					$finder->whereOr($conditions);
				}
			}
		}

		return false;
	}

	public function getFormData()
	{
		/** @var \XF\Repository\ThreadPrefix $prefixRepo */
		$prefixRepo = $this->em->getRepository('XF:ThreadPrefix');
		$prefixes = $prefixRepo->getPrefixListData();

		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = $this->em->getRepository('XF:Node');
		$forums = $nodeRepo->getNodeOptionsData(false, 'Forum');

		return [
			'prefixes' => $prefixes,
			'forums' => $forums
		];
	}

	public function getFormDefaults()
	{
		return [
			'prefix_id' => -1,
			'node_id' => 0,

			'reply_count' => ['end' => -1],
			'view_count' => ['end' => -1],

			'discussion_state' => ['visible', 'moderated', 'deleted'],
			'discussion_open' => [0, 1],
			'sticky' => [0, 1]
		];
	}
}