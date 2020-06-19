<?php

namespace XF\Service\Tag;

use XF\Mvc\Entity\Entity;

class Changer extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Repository\Tag
	 */
	protected $tagRepo;

	/**
	 * @var \XF\Tag\AbstractHandler
	 */
	protected $handler;

	protected $contentType;
	protected $contentId;

	protected $permissions = [];

	/**
	 * @var \XF\Entity\TagContent[]
	 */
	protected $existingTags = [];

	protected $createTags = [];
	protected $addTags = [];
	protected $removeTags = [];

	protected $invalidCreateTags = [];

	protected $errors = null;

	public function __construct(\XF\App $app, $contentType, Entity $context)
	{
		parent::__construct($app);

		$this->tagRepo = $this->repository('XF:Tag');
		$this->handler = $this->tagRepo->getTagHandler($contentType, true);
		$this->contentType = $contentType;

		$this->permissions = array_merge(
			$this->getDefaultPermissions(),
			$this->handler->getPermissionsFromContext($context)
		);

		$expectedEntityType = $app->getContentTypeEntity($contentType);
		if ($app->em()->entityIsA($context, $expectedEntityType))
		{
			$id = $context->getIdentifierValues();
			if (!$id || count($id) != 1)
			{
				throw new \InvalidArgumentException("Entity does not have an ID or does not have a simple key");
			}
			$this->setContentId(reset($id));
		}
	}

	public function setContentId($id, $newlyCreated = false)
	{
		$id = intval($id);
		if (!$id)
		{
			throw new \InvalidArgumentException("Invalid ID provided");
		}

		if (!$newlyCreated)
		{
			if ($this->createTags || $this->addTags || $this->removeTags)
			{
				throw new \InvalidArgumentException("Content must be set before you attempt to manipulate tags");
			}

			$finder = $this->tagRepo->findContentTags($this->contentType, $id);
			$this->existingTags = $finder->keyedBy('tag_id')->fetch()->toArray();
		}

		$this->contentId = $id;

		return $this;
	}

	public function getContentId()
	{
		return $this->contentId;
	}

	public function getExistingTagsByEditability()
	{
		$editable = [];
		$uneditable = [];

		$editOthers = $this->getPermission('removeOthers');
		$userId = \XF::visitor()->user_id;

		if ($this->existingTags)
		{
			foreach ($this->existingTags AS $id => $contentTag)
			{
				if ($editOthers || $contentTag->add_user_id == $userId)
				{
					$editable[$id] = $contentTag;
				}
				else
				{
					$uneditable[$id] = $contentTag;
				}
			}
		}

		return [
			'editable' => $editable,
			'uneditable' => $uneditable
		];
	}

	public function setEditableTags($tagList)
	{
		if (!is_array($tagList))
		{
			$tagList = $this->splitTags($tagList);
		}

		$editability = $this->getExistingTagsByEditability();
		foreach ($editability['uneditable'] AS $uneditable)
		{
			// sanity check to make sure it doesn't get removed
			if (is_string($uneditable->tag))
			{
				$tagList[] = $uneditable->tag;
			}
		}

		$this->setTags($tagList, true);
	}

	public function setTags($tagList, $ignoreNonRemovable = false)
	{
		$this->errors = null; // need to check after changing

		if (!is_array($tagList))
		{
			$tagList = $this->splitTags($tagList);
		}

		$this->addTagsInternal($tagList, $existingAdded);

		$removeExisting = $this->existingTags;
		foreach ($existingAdded AS $id)
		{
			unset($removeExisting[$id]);
		}

		$visitorUserId = \XF::visitor()->user_id;

		foreach ($removeExisting AS $tag)
		{
			if ($ignoreNonRemovable
				&& !$this->getPermission('removeOthers')
				&& $tag->add_user_id != $visitorUserId
			)
			{
				// can't remove but told to ignore
				continue;
			}

			$this->removeTags[$tag->tag_id] = $tag->tag;
		}
	}

	protected function addTagsInternal(array $tagList, &$existingAdded = [])
	{
		$existingAdded = [];

		$addTags = $this->tagRepo->getTags($tagList, $createTags);
		foreach ($addTags AS $tag)
		{
			$id = $tag->tag_id;
			if (isset($this->existingTags[$id]))
			{
				// tag already applied
				$existingAdded[$id] = $id;
				continue;
			}

			if (isset($this->removeTags[$id]))
			{
				// already removing
				continue;
			}

			$this->addTags[$id] = $tag->tag;
		}

		foreach ($createTags AS $create)
		{
			$this->createTags[$create] = $create;

			if (!$this->tagRepo->isValidTag($create))
			{
				$this->invalidCreateTags[$create] = $create;
			}
		}
	}

	protected function removeTagsInternal(array $removeTags, $ignoreNonRemovable = true)
	{
		$visitorUserId = \XF::visitor()->user_id;

		foreach ($removeTags AS $tag)
		{
			if ($ignoreNonRemovable
				&& !$this->getPermission('removeOthers')
				&& $tag->add_user_id != $visitorUserId
			)
			{
				// can't remove but told to ignore
				continue;
			}

			$this->removeTags[$tag->tag_id] = $tag->tag;

			if (isset($this->addTags[$tag->tag_id]))
			{
				unset($this->addTags[$tag->tag_id]);
			}
		}
	}

	public function addTags($tagList)
	{
		if (!is_array($tagList))
		{
			$tagList = $this->splitTags($tagList);
		}

		$this->addTagsInternal($tagList);
	}

	public function removeTags($tagList, $ignoreNonRemovable = true)
	{
		if (!is_array($tagList))
		{
			$tagList = $this->splitTags($tagList);
		}

		$removeTags = $this->tagRepo->getTags($tagList);
		$this->removeTagsInternal($removeTags, $ignoreNonRemovable);
	}

	protected function splitTags($tagList)
	{
		return $this->tagRepo->splitTagList($tagList);
	}

	protected function getDefaultPermissions()
	{
		$options = $this->app->options();
		$visitor = \XF::visitor();

		return [
			'edit' => $options->enableTagging,
			'create' => $visitor->hasPermission('general', 'createTag'),
			'removeOthers' => false,
			'maxUser' => $visitor->hasPermission('general', 'bypassUserTagLimit') ? 0 : $options->maxContentTagsPerUser,
			'maxTotal' => $options->maxContentTags,
			'minTotal' => 0
		];
	}

	public function getPermissions()
	{
		return $this->permissions;
	}

	public function getPermission($key)
	{
		return $this->permissions[$key];
	}

	public function canEdit()
	{
		return $this->getPermission('edit');
	}

	public function save($performValidations = true)
	{
		if ($performValidations)
		{
			if ($this->errors === null)
			{
				$this->checkForErrors();
			}
			if ($this->errors)
			{
				throw new \LogicException("There are outstanding errors, cannot save.");
			}
		}

		$this->db()->beginTransaction();

		foreach ($this->createTags AS $create)
		{
			$tag = $this->tagRepo->createTag($create);
			if ($tag)
			{
				$this->addTags[$tag->tag_id] = $tag->tag;
			}
		}

		$cache = $this->tagRepo->modifyContentTags(
			$this->contentType, $this->contentId,
			array_keys($this->addTags), array_keys($this->removeTags)
		);

		$this->db()->commit();

		return $cache;
	}

	public function tagsChanged()
	{
		return count($this->addTags) || count($this->removeTags) || count($this->createTags);
	}

	public function getErrors()
	{
		if ($this->errors === null)
		{
			$this->checkForErrors();
		}

		return $this->errors;
	}

	public function hasErrors()
	{
		if ($this->errors === null)
		{
			$this->checkForErrors();
		}

		return count($this->errors) > 0;
	}

	protected function checkForErrors()
	{
		$errors = [];
		$userId = \XF::visitor()->user_id;
		$permissions = $this->permissions;

		$totalTags = 0;
		$totalUser = 0;

		if (!$permissions['edit'])
		{
			$errors['edit'] = \XF::phrase('do_not_have_permission');
		}

		if ($this->createTags && !$permissions['create'])
		{
			$errors['create'] = \XF::phrase(
				'you_may_not_create_new_tags_please_change_x',
				['tags' => implode(', ', $this->createTags)]
			);
		}
		if ($this->invalidCreateTags && $this->getPermission('create'))
		{
			$errors['invalidCreate'] = \XF::phrase(
				'some_tags_not_valid_please_change_x',
				['tags' => implode(', ', $this->invalidCreateTags)]
			);
		}

		foreach ($this->existingTags AS $id => $tag)
		{
			if (isset($this->removeTags[$id]))
			{
				continue;
			}

			$totalTags++;
			if ($tag->add_user_id == $userId)
			{
				$totalUser++;
			}
		}

		foreach ($this->addTags AS $tag)
		{
			$totalTags++;
			$totalUser++;
		}
		foreach ($this->createTags AS $tag)
		{
			$totalTags++;
			$totalUser++;
		}

		$removeFail = [];
		foreach ($this->removeTags AS $id => $tag)
		{
			if (!isset($this->existingTags[$id]))
			{
				continue;
			}

			$existing = $this->existingTags[$id];

			if (!$this->getPermission('removeOthers') && $existing->add_user_id != $userId)
			{
				$removeFail[] = $tag;
			}
			// removed tags are already ignored for totals
		}
		if ($removeFail)
		{
			$errors['create'] = \XF::phrase(
				'you_may_not_remove_following_tags_x',
				['tags' => implode(', ', $removeFail)]
			);
		}

		if ($permissions['maxUser'] > 0 && $totalUser > $permissions['maxUser'])
		{
			$errors['maxUser'] = \XF::phrase(
				'you_may_only_apply_x_tags_to_this_content',
				['count' => $permissions['maxUser']]
			);
		}
		if ($permissions['maxTotal'] > 0 && $totalTags > $permissions['maxTotal'])
		{
			$errors['maxTotal'] = \XF::phrase(
				'this_content_may_only_have_x_tags_in_total',
				['count' => $permissions['maxTotal']]
			);
		}

		$minRequired = $permissions['minTotal'];
		if ($permissions['maxUser'] > 0 && $permissions['maxUser'] < $minRequired)
		{
			// if the user can only add 1 tag but you require 3, they could never continue
			$minRequired = $permissions['maxUser'];
		}
		if ($totalTags < $minRequired)
		{
			$errors['minTotal'] = \XF::phrase(
				'this_content_must_have_at_least_x_tags',
				['min' => $minRequired]
			);
		}

		$this->errors = $errors;
	}
}