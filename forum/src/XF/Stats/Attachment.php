<?php

namespace XF\Stats;

class Attachment extends AbstractHandler
{
	public function getStatsTypes()
	{
		return [
			'attachment' => \XF::phrase('attachments'),
			'attachment_disk_usage' => \XF::phrase('attachment_disk_usage')
		];
	}

	public function getData($start, $end)
	{
		$db = $this->db();

		$attachments = $db->fetchPairs(
			$this->getBasicDataQuery('xf_attachment_data', 'upload_date', 'attach_count > ?'),
			[$start, $end, 0]
		);

		$attachmentDiskUsage = $db->fetchPairs(
			$this->getBasicDataQuery('xf_attachment_data', 'upload_date', 'attach_count > ?', 'SUM(file_size)'),
			[$start, $end, 0]
		);

		return [
			'attachment' => $attachments,
			'attachment_disk_usage' => $attachmentDiskUsage
		];
	}

	public function adjustStatValue($statsType, $counter)
	{
		if ($statsType == 'attachment_disk_usage')
		{
			return round($counter / 1048576, 2); // megabytes
		}
		return parent::adjustStatValue($statsType, $counter);
	}
}