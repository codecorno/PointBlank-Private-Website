<?php

namespace XF\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\IndexRecord;
use XF\Search\MetadataStructure;
use XF\Search\Query\MetadataConstraint;

class Post extends AbstractData
{
	public function getEntityWith($forView = false)
	{
		$get = ['Thread', 'Thread.Forum'];
		if ($forView)
		{
			$get[] = 'User';

			$visitor = \XF::visitor();
			$get[] = 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id;
		}

		return $get;
	}

	public function getIndexData(Entity $entity)
	{
		/** @var \XF\Entity\Post $entity */

		if (!$entity->Thread || !$entity->Thread->Forum)
		{
			return null;
		}

		/** @var \XF\Entity\Thread $thread */
		$thread = $entity->Thread;

		if ($entity->isFirstPost())
		{
			return $this->searcher->handler('thread')->getIndexData($thread);
		}

		$index = IndexRecord::create('post', $entity->post_id, [
			'message' => $entity->message_,
			'date' => $entity->post_date,
			'user_id' => $entity->user_id,
			'discussion_id' => $entity->thread_id,
			'metadata' => $this->getMetaData($entity)
		]);

		if (!$entity->isVisible())
		{
			$index->setHidden();
		}

		return $index;
	}

	protected function getMetaData(\XF\Entity\Post $entity)
	{
		/** @var \XF\Entity\Thread $thread */
		$thread = $entity->Thread;

		$metadata = [
			'node' => $thread->node_id,
			'thread' => $entity->thread_id
		];
		if ($thread->prefix_id)
		{
			$metadata['prefix'] = $thread->prefix_id;
		}

		return $metadata;
	}

	public function setupMetadataStructure(MetadataStructure $structure)
	{
		$structure->addField('node', MetadataStructure::INT);
		$structure->addField('thread', MetadataStructure::INT);
		$structure->addField('prefix', MetadataStructure::INT);
	}

	public function canIncludeInResults(Entity $entity, array $resultIds)
	{
		/** @var \XF\Entity\Post $entity */
		if (isset($resultIds['thread-' . $entity->thread_id]) && $entity->isFirstPost())
		{
			return false;
		}

		return true;
	}

	public function getResultDate(Entity $entity)
	{
		return $entity->post_date;
	}

	public function getTemplateData(Entity $entity, array $options = [])
	{
		return [
			'post' => $entity,
			'options' => $options
		];
	}

	public function getSearchableContentTypes()
	{
		return ['post', 'thread'];
	}

	public function getSearchFormTab()
	{
		return [
			'title' => \XF::phrase('search_threads'),
			'order' => 10
		];
	}

	public function getSectionContext()
	{
		return 'forums';
	}

	public function getSearchFormData()
	{
		$prefixListData = $this->getPrefixListData();

		return [
			'prefixGroups' => $prefixListData['prefixGroups'],
			'prefixesGrouped' => $prefixListData['prefixesGrouped'],

			'nodeTree' => $this->getSearchableNodeTree()
		];
	}

	/**
	 * @return \XF\Tree
	 */
	protected function getSearchableNodeTree()
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getNodeList());

		// only list nodes that are forums or contain forums
		$nodeTree = $nodeTree->filter(null, function($id, $node, $depth, $children, $tree)
		{
			return ($children || $node->node_type_id == 'Forum');
		});

		return $nodeTree;
	}

	protected function getPrefixListData()
	{
		/** @var \XF\Repository\ThreadPrefix $prefixRepo */
		$prefixRepo = \XF::repository('XF:ThreadPrefix');
		return $prefixRepo->getVisiblePrefixListData();
	}

	public function applyTypeConstraintsFromInput(\XF\Search\Query\Query $query, \XF\Http\Request $request, array &$urlConstraints)
	{
		$minReplyCount = $request->filter('c.min_reply_count', 'uint');
		if ($minReplyCount)
		{
			$query->withSql(new \XF\Search\Query\SqlConstraint(
				'thread.reply_count >= %s',
				$minReplyCount,
				$this->getThreadQueryTableReference()
			));
		}
		else
		{
			unset($urlConstraints['min_reply_count']);
		}

		$prefixes = $request->filter('c.prefixes', 'array-uint');
		$prefixes = array_unique($prefixes);
		if ($prefixes && reset($prefixes))
		{
			$query->withMetadata('prefix', $prefixes);
		}
		else
		{
			unset($urlConstraints['prefixes']);
		}

		$threadId = $request->filter('c.thread', 'uint');
		if ($threadId)
		{
			$query->withMetadata('thread', $threadId)
				->inTitleOnly(false);
		}
		else
		{
			unset($urlConstraints['thread']);

			$nodeIds = $request->filter('c.nodes', 'array-uint');
			$nodeIds = array_unique($nodeIds);
			if ($nodeIds && reset($nodeIds))
			{
				if ($request->filter('c.child_nodes', 'bool'))
				{
					$nodeTree = $this->getSearchableNodeTree();
					$searchNodeIds = array_fill_keys($nodeIds, true);
					$nodeTree->traverse(function($id, $node) use (&$searchNodeIds)
					{
						if (isset($searchNodeIds[$id]) || isset($searchNodeIds[$node->parent_node_id]))
						{
							// if we're in the search node list, the user selected the node explicitly
							// if the parent is in the list, then that node was selected via traversal so we're included too
							$searchNodeIds[$id] = true;
						}

						// we still need to traverse children though, as children may be selected
					});

					$nodeIds = array_unique(array_keys($searchNodeIds));
				}
				else
				{
					unset($urlConstraints['child_nodes']);
				}

				$query->withMetadata('node', $nodeIds);
			}
			else
			{
				unset($urlConstraints['nodes']);
				unset($urlConstraints['child_nodes']);
			}
		}
	}

	public function getTypePermissionConstraints(\XF\Search\Query\Query $query, $isOnlyType)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');
		$nodes = $nodeRepo->getFullNodeList();
		$nodeRepo->loadNodeTypeDataForNodes($nodes);

		$skip = [];
		foreach ($nodes AS $node)
		{
			/** @var \XF\Entity\Node $node */
			if ($node->node_type_id != 'Forum')
			{
				continue;
			}
			if (!$node->canView())
			{
				$skip[] = $node->node_id;
			}
		}

		if ($skip)
		{
			return [
				new MetadataConstraint('node', $skip, MetadataConstraint::MATCH_NONE)
			];
		}
		else
		{
			return [];
		}
	}

	public function getTypeOrder($order)
	{
		if ($order == 'replies')
		{
			return new \XF\Search\Query\SqlOrder('thread.reply_count DESC', $this->getThreadQueryTableReference());
		}
		else
		{
			return null;
		}
	}

	protected function getThreadQueryTableReference()
	{
		return new \XF\Search\Query\TableReference(
			'thread',
			'xf_thread',
			'thread.thread_id = search_index.discussion_id'
		);
	}

	public function getGroupByType()
	{
		return 'thread';
	}

	public function canUseInlineModeration(Entity $entity, &$error = null)
	{
		/** @var \XF\Entity\Post $entity */
		return $entity->canUseInlineModeration($error);
	}
}