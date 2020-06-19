<?php

namespace XF\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\IndexRecord;
use XF\Search\MetadataStructure;
use XF\Util\Arr;

class ProfilePost extends AbstractData
{
	public function getEntityWith($forView = false)
	{
		$get = ['ProfileUser'];
		if ($forView)
		{
			$get[] = 'ProfileUser.Privacy';
			$get[] = 'User';
		}

		return $get;
	}

	public function getIndexData(Entity $entity)
	{
		/** @var \XF\Entity\ProfilePost $entity */

		if (!$entity->ProfileUser)
		{
			return null;
		}

		$index = IndexRecord::create('profile_post', $entity->profile_post_id, [
			'title' => '',
			'message' => $entity->message_,
			'date' => $entity->post_date,
			'user_id' => $entity->user_id,
			'discussion_id' => $entity->profile_post_id,
			'metadata' => $this->getMetaData($entity)
		]);

		if (!$entity->isVisible())
		{
			$index->setHidden();
		}

		return $index;
	}

	protected function getMetaData(\XF\Entity\ProfilePost $entity)
	{
		$metadata = [];

		$metadata['profile_user'] = $entity->profile_user_id;

		return $metadata;
	}

	public function setupMetadataStructure(MetadataStructure $structure)
	{
		$structure->addField('profile_user', MetadataStructure::INT);
	}

	public function getResultDate(Entity $entity)
	{
		return $entity->post_date;
	}

	public function getSearchableContentTypes()
	{
		return ['profile_post', 'profile_post_comment'];
	}

	public function getTemplateData(Entity $entity, array $options = [])
	{
		return [
			'profilePost' => $entity,
			'options' => $options
		];
	}

	public function getSearchFormTab()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canViewProfilePosts())
		{
			return null;
		}

		return [
			'title' => \XF::phrase('search_profile_posts'),
			'order' => 1000
		];
	}

	public function getSectionContext()
	{
		return 'members';
	}

	public function applyTypeConstraintsFromInput(\XF\Search\Query\Query $query, \XF\Http\Request $request, array &$urlConstraints)
	{
		$profileUser = $request->filter('c.profile_users', 'str');
		if ($profileUser)
		{
			$users = Arr::stringToArray($profileUser, '/,\s*/');
			if ($users)
			{
				/** @var \XF\Repository\User $userRepo */
				$userRepo = \XF::repository('XF:User');
				$matchedUsers = $userRepo->getUsersByNames($users, $notFound);
				if ($notFound)
				{
					$query->error('users',
						\XF::phrase('following_members_not_found_x', ['members' => implode(', ', $notFound)])
					);
				}
				else
				{
					$query->withMetadata('profile_user', $matchedUsers->keys());
					$urlConstraints['profile_users'] = implode(', ', $users);
				}
			}
		}
	}

	public function canUseInlineModeration(Entity $entity, &$error = null)
	{
		/** @var \XF\Entity\ProfilePost $entity */
		return $entity->canUseInlineModeration($error);
	}
}