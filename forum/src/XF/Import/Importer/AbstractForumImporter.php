<?php

namespace XF\Import\Importer;

abstract class AbstractForumImporter extends AbstractCoreImporter
{
	public function canRetainIds()
	{
		if (!parent::canRetainIds())
		{
			return false;
		}

		$db = $this->app->db();

		$maxThreadId = $db->fetchOne("SELECT MAX(thread_id) FROM xf_thread");
		if ($maxThreadId)
		{
			return false;
		}

		$maxPostId = $db->fetchOne("SELECT MAX(post_id) FROM xf_post");
		if ($maxPostId)
		{
			return false;
		}

		$maxNodeId = $db->fetchOne("SELECT MAX(node_id) FROM xf_node");
		if ($maxNodeId > 2)
		{
			// 1 and 2 are default nodes
			return false;
		}

		return true;
	}

	public function resetDataForRetainIds()
	{
		// nodes 1 and 2 are created by default in the installer so we need to remove those if retaining IDs
		$node = $this->em()->find('XF:Node', 1);
		if ($node)
		{
			$node->delete();
		}

		$node = $this->em()->find('XF:Node', 2);
		if ($node)
		{
			$node->delete();
		}
	}

	public function getFinalizeJobs(array $stepsRun)
	{
		$jobs = parent::getFinalizeJobs($stepsRun);

		$jobs[] = 'XF:Thread';
		$jobs[] = 'XF:Forum';
		$jobs[] = 'XF:Poll';

		return $jobs;
	}
}