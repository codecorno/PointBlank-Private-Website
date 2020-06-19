<?php

namespace XF\Entity;

use XF\Mvc\Entity\Structure;

/**
 * @property \XF\Entity\ReactionContent[] Reactions
 */
trait ReactionTrait
{
	abstract public function canReact(&$error = null);

	public function isReactedTo()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		/** @var ReactionContent $reactionContent */
		$reactionContent = isset($this->Reactions[$visitor->user_id])
			? $this->Reactions[$visitor->user_id]
			: null;

		return ($reactionContent && $reactionContent->isReactionActive());
	}

	public function getVisitorReactionId()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return null;
		}

		if (!isset($this->Reactions[$visitor->user_id]))
		{
			return null;
		}

		return $this->Reactions[$visitor->user_id]->reaction_id;
	}

	/**
	 * @return null|ReactionContent
	 */
	public function getReactionContent()
	{
		if (!$this->isReactedTo())
		{
			return null;
		}

		return $this->Reactions[\XF::visitor()->user_id];
	}

	public function getReactions()
	{
		$sourceValue = $this->getValueSourceEncoded('reactions');

		if (!$sourceValue)
		{
			return [];
		}

		if (!is_array(@json_decode($sourceValue, true)))
		{
			// legacy data, just a number, convert to an array assuming the number is the count of likes (reaction ID 1)
			return [
				'1' => intval($sourceValue)
			];
		}

		$reactions = $this->reactions_;
		arsort($reactions, SORT_NUMERIC);

		$reactionsCache = $this->app()->container('reactions');

		foreach ($reactions AS $reactionId => $reaction)
		{
			if (!isset($reactionsCache[$reactionId]) || !$reactionsCache[$reactionId]['active'])
			{
				unset($reactions[$reactionId]);
				continue;
			}
		}

		return $reactions;
	}

	public function getReactionUsers()
	{
		$reactionUsers = $this->reaction_users_;
		if ($reactionUsers === null)
		{
			return [];
		}

		$reactionsCache = $this->app()->container('reactions');

		foreach ($reactionUsers AS $key => $user)
		{
			if (!isset($user['reaction_id']))
			{
				// likely legacy data with no reaction_id so this relates to a default like so just carry on.
				continue;
			}

			if (!isset($reactionsCache[$user['reaction_id']]) || !$reactionsCache[$user['reaction_id']]['active'])
			{
				unset($reactionUsers[$key]);
				continue;
			}
		}

		return array_values($reactionUsers); // to ensure array is re-indexed from 0
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 *
	 * @api-out bool $is_reacted_to True if the viewing user has reacted to this content
	 * @api-out int $visitor_reaction_id If the viewer reacted, the ID of the reaction they used
	 */
	protected function addReactionStateToApiResult(\XF\Api\Result\EntityResult $result)
	{
		$visitor = \XF::visitor();

		if ($visitor->user_id)
		{
			$isReactedTo = $this->isReactedTo();
			$result->is_reacted_to = $isReactedTo;
			if ($isReactedTo)
			{
				$result->visitor_reaction_id = $this->getVisitorReactionId();
			}
		}
	}

	public static function addReactableStructureElements(Structure $structure)
	{
		$structure->columns['reaction_score'] = ['type' => self::INT, 'default' => 0, 'api' => true];
		$structure->columns['reactions'] = ['type' => self::JSON_ARRAY, 'default' => []];
		$structure->columns['reaction_users'] = ['type' => self::JSON_ARRAY, 'default' => []];

		$structure->relations['Reactions'] = [
			'entity' => 'XF:ReactionContent',
			'type' => self::TO_MANY,
			'conditions' => [
				['content_type', '=', $structure->contentType],
				['content_id', '=', '$' . $structure->primaryKey]
			],
			'key' => 'reaction_user_id',
			'order' => 'reaction_date'
		];

		$structure->getters['reactions'] = true;
		$structure->getters['reaction_users'] = true;

		$structure->columnAliases['likes'] = 'reaction_score';
		$structure->columnAliases['like_users'] = 'reaction_users';

		if (isset($structure->withAliases['api']))
		{
			$structure->withAliases['api'][] = function()
			{
				$visitorId = \XF::visitor()->user_id;
				if ($visitorId)
				{
					return 'Reactions|' . $visitorId;
				}
			};
		}
	}
}