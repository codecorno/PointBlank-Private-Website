<?php

namespace XF\Searcher;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Manager;

/**
 * @method \XF\Finder\User getFinder()
 */
class User extends AbstractSearcher
{
	protected $allowedRelations = ['Option', 'Profile', 'Privacy'];

	protected $formats = [
		'username' => 'like',
		'email' => 'like',
		'register_date' => 'date',
		'last_activity' => 'date'
	];

	protected $arrayValueKeys = [
		'user_field'
	];

	protected $order = [['username', 'asc']];

	protected $stringSortOrders = ['username'];

	protected function getEntityType()
	{
		return 'XF:User';
	}

	protected function getDefaultOrderOptions()
	{
		$orders = [
			'username' => \XF::phrase('user_name'),
			'register_date' => \XF::phrase('join_date'),
			'last_activity' => \XF::phrase('last_activity'),
			'message_count' => \XF::phrase('messages'),
			'trophy_points' => \XF::phrase('trophy_points'),
			'reaction_score' => \XF::phrase('reaction_score'),
		];

		$stringSortOrders = $this->stringSortOrders;

		\XF::fire('user_searcher_orders', [$this, &$orders, &$stringSortOrders]);

		$this->stringSortOrders = $stringSortOrders;

		return $orders;
	}

	public function isSortOrderNumeric($sortOrder)
	{
		return !in_array($sortOrder, $this->stringSortOrders);
	}

	protected function validateSpecialCriteriaValue($key, &$value, $column, $format, $relation)
	{
		if ($key == 'no_secondary_group_ids' && !$value)
		{
			return false;
		}

		if ($key == 'user_field')
		{
			$exactMatchFields = !empty($value['exact']) ? $value['exact'] : [];
			$customFields = $value;
			unset($customFields['exact']);

			foreach ($customFields AS $fieldId => $fieldValue)
			{
				if ($fieldValue === '' || (is_array($fieldValue) && !$fieldValue))
				{
					unset($customFields[$fieldId]);
				}
			}
			foreach ($exactMatchFields AS $fieldId => $fieldValue)
			{
				if ($fieldValue === '' || (is_array($fieldValue) && !$fieldValue))
				{
					unset($exactMatchFields[$fieldId]);
				}
			}
			if ($exactMatchFields)
			{
				$customFields['exact'] = $exactMatchFields;
			}

			$value = $customFields;
			if (!$customFields)
			{
				return false;
			}
		}

		return null;
	}

	protected function applySpecialCriteriaValue(Finder $finder, $key, $value, $column, $format, $relation)
	{
		if ($key == 'secondary_group_ids' || $key == 'not_secondary_group_ids')
		{
			if (!is_array($value))
			{
				$value = [$value];
			}

			$columnName = $finder->columnSqlName('secondary_group_ids');
			$positiveMatch = ($key == 'secondary_group_ids');
			$parts = [];
			foreach ($value AS $part)
			{
				if ($positiveMatch)
				{
					$parts[] = 'FIND_IN_SET(' . $finder->quote($part) . ', '. $columnName . ')';
				}
				else
				{
					$parts[] = 'FIND_IN_SET(' . $finder->quote($part) . ', '. $columnName . ') = 0';
				}
			}
			if ($parts)
			{
				$joiner = $positiveMatch ? ' OR ' : ' AND ';
				$finder->whereSql(implode($joiner, $parts));
			}
			return true;
		}

		if ($key == 'no_secondary_group_ids' && $value)
		{
			$finder->where('secondary_group_ids', '=', '');
			return true;
		}

		if ($key == 'no_empty_email')
		{
			$finder->where('email', '<>', '');
			return true;
		}

		if ($key == 'not_user_id')
		{
			$finder->where('user_id', '<>', $value);
			return true;
		}

		if ($key == 'user_field')
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

				$finder->with('Profile.CustomFields|' . $fieldId);
				$isExact = !empty($exactMatchFields[$fieldId]);
				$conditions = [];

				foreach ((array)$value AS $possible)
				{
					$columnName = 'Profile.CustomFields|' . $fieldId . '.field_value';
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
		return [
			'userGroups' => $this->em->getRepository('XF:UserGroup')->findUserGroupsForList()->fetch()
		];
	}

	public function getFormDefaults()
	{
		return [
			'user_state' => [
				'valid', 'email_confirm', 'email_confirm_edit', 'email_bounce', 'moderated', 'rejected', 'disabled'
			],
			'is_banned' => [0, 1],
			'is_staff' => [0, 1],
			'no_secondary_group_ids' => 0,
			'message_count' => ['end' => -1],
			'trophy_points' => ['end' => -1],
			'Option' => [
				'is_discouraged' => [0, 1],
			]
		];
	}
}