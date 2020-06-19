<?php

namespace XF;

use XF\Entity\User;
use XF\Mvc\Entity\Entity;

class Draft implements \ArrayAccess
{
	/**
	 * @var \XF\Entity\Draft|null
	 */
	protected $draft;

	public function __construct(\XF\Entity\Draft $draft = null)
	{
		$this->draft = $draft;
	}

	/**
	 * @return \XF\Entity\Draft|null
	 */
	public function getDraftEntity()
	{
		return $this->draft;
	}

	public function getDraftKey()
	{
		return $this->draft ? $this->draft->draft_key : null;
	}

	public function exists()
	{
		return $this->draft && $this->draft->exists();
	}

	public function save()
	{
		if ($this->draft)
		{
			try
			{
				return $this->draft->save();
			}
			catch (\XF\Db\DuplicateKeyException $e)
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public function delete()
	{
		if ($this->draft && $this->draft->exists())
		{
			return $this->draft->delete();
		}
		else
		{
			return false;
		}
	}

	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function offsetGet($key)
	{
		if (!$this->draft)
		{
			return null;
		}

		switch ($key)
		{
			case 'message':
			case 'extra_data':
				return $this->draft->getValue($key);

			default:
				$extraData = $this->draft->extra_data;
				return isset($extraData[$key]) ? $extraData[$key] : null;
		}
	}

	public function __set($key, $value)
	{
		$this->offsetSet($key, $value);
	}

	public function offsetSet($key, $value)
	{
		if (!$this->draft)
		{
			return;
		}

		switch ($key)
		{
			case 'message':
			case 'extra_data':
				$this->draft->set($key, $value);
				break;

			default:
				$this->draft->setExtraData($key, $value);
		}
	}

	public function set($key, $value)
	{
		$this->offsetSet($key, $value);
	}

	public function bulkSet(array $values)
	{
		foreach ($values AS $key => $value)
		{
			$this->offsetSet($key, $value);
		}
	}

	public function offsetExists($key)
	{
		if (!$this->draft)
		{
			return false;
		}

		switch ($key)
		{
			case 'message':
			case 'extra_data':
				return true;

			default:
				return array_key_exists($key, $this->draft->extra_data);
		}
	}

	public function offsetUnset($key)
	{
		if (!$this->draft)
		{
			return;
		}

		switch ($key)
		{
			case 'message':
				$this->draft->message = '';
				break;

			case 'extra_data':
				$this->draft->extra_data = [];
				break;

			default:
				$this->draft->unsetExtraData($key);
		}
	}

	/**
	 * @param Entity $entity
	 * @param string $relationKey
	 * @param User|null $user
	 *
	 * @return \XF\Draft
	 */
	public static function createFromEntity(Entity $entity, $relationKey, User $user = null)
	{
		if (!$entity->isValidRelation($relationKey))
		{
			throw new \LogicException("Invalid draft relation key '{$relationKey}'");
		}

		$draftCondition = $entity->structure()->relations[$relationKey]['conditions'][0];
		if (!$draftCondition
			|| !is_array($draftCondition)
			|| $draftCondition[0] != 'draft_key'
			|| $draftCondition[1] != '='
		)
		{
			throw new \LogicException("Could not find expected draft_key condition; expected [draft_key, =, ...]");
		}

		$user = $user ?: \XF::visitor();
		if (!$user->user_id)
		{
			// guests can never have a draft
			$draft = null;
		}
		else
		{
			$relation = $entity->getRelation($relationKey);
			if (isset($relation[$user->user_id]))
			{
				$draft = $relation[$user->user_id];
			}
			else
			{
				$draftKey = '';
				foreach (array_slice($draftCondition, 2) AS $v)
				{
					if ($v && $v[0] == '$')
					{
						$v = $entity->getValue(substr($v, 1));
						if ($v === null)
						{
							return null;
						}
						$draftKey .= $v;
					}
					else
					{
						$draftKey .= $v;
					}
				}

				$draft = \XF::em()->instantiateEntity('XF:Draft');
				$draft->user_id = $user->user_id;
				$draft->draft_key = $draftKey;
			}
		}

		$class = \XF::extendClass('XF\Draft');
		return new $class($draft);
	}

	/**
	 * @param string $draftKey
	 * @param User|null $user
	 *
	 * @return \XF\Draft
	 */
	public static function createFromKey($draftKey, User $user = null)
	{
		$user = $user ?: \XF::visitor();

		$draft = \XF::repository('XF:Draft')->getDraftByKeyAndUser($draftKey, $user);
		if (!$draft && $user->user_id)
		{
			$draft = \XF::em()->instantiateEntity('XF:Draft');
			$draft->user_id = $user->user_id;
			$draft->draft_key = $draftKey;
		}

		$class = \XF::extendClass('XF\Draft');
		return new $class($draft);
	}
}