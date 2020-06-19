<?php

namespace XF\Job;

class TagRecache extends AbstractJob
{
	protected $defaultData = [
		'tagId' => null,
		'start' => 0,
		'batch' => 500,
		'deleteFirst' => false
	];

	public function run($maxRunTime)
	{
		$startTime = microtime(true);

		$db = $this->app->db();
		$em = $this->app->em();

		if (!$this->data['tagId'])
		{
			return $this->complete();
		}

		$matches = $db->fetchAll($db->limit(
				"
				SELECT tag_content_id, content_type, content_id
				FROM xf_tag_content
				WHERE tag_id = ?
					AND tag_content_id > ?
				ORDER BY tag_content_id
			", $this->data['batch']
		), [$this->data['tagId'], $this->data['start']]);
		if (!$matches)
		{
			return $this->complete();
		}

		/** @var \XF\Repository\Tag $tagRepo */
		$tagRepo = $em->getRepository('XF:Tag');

		$db->beginTransaction();

		$done = 0;

		foreach ($matches AS $match)
		{
			if (microtime(true) - $startTime >= $maxRunTime)
			{
				break;
			}

			$this->data['start'] = $match['tag_content_id'];

			if ($this->data['deleteFirst'])
			{
				$db->delete('xf_tag_content', 'tag_content_id = ' . $match['tag_content_id']);
			}

			$tagRepo->rebuildContentTagCache($match['content_type'], $match['content_id']);

			$done++;
		}

		$db->commit();

		$this->data['batch'] = $this->calculateOptimalBatch($this->data['batch'], $done, $startTime, $maxRunTime, 1000);

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('tags');
		return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $this->data['start']);
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}