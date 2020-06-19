<?php

namespace XF\Mail;

class Queue
{
	/**
	 * @var \XF\Db\AbstractAdapter
	 */
	protected $db;

	protected $preventJobEnqueue = false;

	public function __construct(\XF\Db\AbstractAdapter $db)
	{
		$this->db = $db;
	}

	public function preventJobEnqueue($prevent)
	{
		$this->preventJobEnqueue = $prevent;
	}

	public function queue(\Swift_Mime_Message $message)
	{
		$this->db->insert('xf_mail_queue', [
			'mail_data' => serialize($message),
			'queue_date' => time()
		]);

		$this->enqueueJob();

		return true;
	}

	public function queueForRetry(\Swift_Mime_Message $message, $queueEntry)
	{
		$sendDate = $this->calculateNextSendDate($queueEntry ? $queueEntry['fail_count'] : 0);
		if (!$sendDate)
		{
			// Mail has failed too many times, delete
			$this->db->delete('xf_mail_queue', 'mail_queue_id = ?', $queueEntry['mail_queue_id']);
			return;
		}

		if ($queueEntry)
		{
			$this->db->update('xf_mail_queue', [
				'send_date' => $sendDate,
				'fail_date' => time(),
				'fail_count' => $queueEntry['fail_count'] + 1
			], 'mail_queue_id = ?', $queueEntry['mail_queue_id']);
		}
		else
		{
			$this->db->insert('xf_mail_queue', [
				'mail_data' => serialize($message),
				'queue_date' => time(),
				'send_date' => $sendDate,
				'fail_date' => time(),
				'fail_count' => 1
			]);
		}

		$this->enqueueJob($sendDate);
	}

	protected function enqueueJob($triggerDate = null)
	{
		if ($this->preventJobEnqueue)
		{
			return;
		}

		if ($triggerDate === null)
		{
			$triggerDate = \XF::$time;
		}

		$jobManager = \XF::app()->jobManager();
		$mailQueueJob = $jobManager->getUniqueJob('MailQueue');

		if (!$mailQueueJob || $mailQueueJob['trigger_date'] > $triggerDate)
		{
			try
			{
				$jobManager->enqueueLater('MailQueue', $triggerDate, 'XF\Job\MailQueue', [], false);
			} catch (\Exception $e)
			{
				// need to just ignore this and let it get picked up later;
				// not doing this could lose email on a deadlock
			}
		}
	}

	protected function calculateNextSendDate($previousFailCount)
	{
		switch ($previousFailCount)
		{
			case 0: $delay = 5 * 60; break; // 5 minutes
			case 1: $delay = 1 * 60 * 60; break; // 1 hour
			case 2: $delay = 2 * 60 * 60; break; // 2 hours
			case 3: $delay = 6 * 60 * 60; break; // 6 hours
			case 4: $delay = 12 * 60 * 60; break; // 12 hours
			default: return null; // give up
		}

		return time() + $delay;
	}

	public function run($maxRunTime)
	{
		$s = microtime(true);
		$db = $this->db;
		$mailer = \XF::mailer();

		do
		{
			$queue = $this->getQueue();

			foreach ($queue AS $id => $record)
			{
				$updated = $db->update('xf_mail_queue', [
					'send_date' => time() + 15 * 60
				], 'mail_queue_id = ? AND send_date = ?', [$id, $record['send_date']]);
				if (!$updated)
				{
					// already been run recently
					continue;
				}

				$message = @unserialize($record['mail_data']);
				if (!($message instanceof \Swift_Mime_Message))
				{
					continue;
				}

				if ($mailer->send($message, null, $record))
				{
					$this->db->delete('xf_mail_queue', 'mail_queue_id = ?', $record['mail_queue_id']);
				}

				if ($maxRunTime && microtime(true) - $s > $maxRunTime)
				{
					break 2;
				}
			}
		}
		while ($queue);
	}

	public function getQueue($limit = 20)
	{
		$db = $this->db;

		return $db->fetchAllKeyed($db->limit('
			SELECT *
			FROM xf_mail_queue
			WHERE send_date <= ?
			ORDER BY send_date, queue_date
		', $limit), 'mail_queue_id', [\XF::$time]);
	}

	public function hasMore(&$nextSendDate = null)
	{
		$nextSendDate = $this->db->fetchOne('
			SELECT MIN(send_date)
			FROM xf_mail_queue
		');

		return $nextSendDate !== null;
	}
}