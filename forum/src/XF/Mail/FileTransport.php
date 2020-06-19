<?php

namespace XF\Mail;

class FileTransport implements \Swift_Transport
{
	/** The event dispatcher from the plugin API */
	private $_eventDispatcher;

	private $_savePath;
	
	/**
	 * Create a new MailTransport with the $log.
	 *
	 * @param \Swift_Events_EventDispatcher $eventDispatcher
	 */
	public function __construct(\Swift_Events_EventDispatcher $eventDispatcher)
	{
		$this->_eventDispatcher = $eventDispatcher;
		$this->_savePath = sys_get_temp_dir();
	}

	public function setSavePath($path)
	{
		$this->_savePath = $path;
	}

	public function getSavePath()
	{
		return $this->_savePath;
	}
	
	/**
	 * Not used.
	 */
	public function isStarted()
	{
		return false;
	}
	
	/**
	 * Not used.
	 */
	public function start()
	{
	}
	
	/**
	 * Not used.
	 */
	public function stop()
	{
	}
	
	public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$failedRecipients = (array) $failedRecipients;

		if ($evt = $this->_eventDispatcher->createSendEvent($this, $message))
		{
			$this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
			if ($evt->bubbleCancelled())
			{
				return 0;
			}
		}

		$count = (
			count((array) $message->getTo())
			+ count((array) $message->getCc())
			+ count((array) $message->getBcc())
		);

		$toHeader = $message->getHeaders()->get('To');
		if (!$toHeader)
		{
			throw new \Swift_TransportException('Cannot send message without a recipient');
		}

		$subjectHeader = $message->getHeaders()->get('Subject');
		$subject = $subjectHeader ? $subjectHeader->getFieldBody() : '';
		$subject = preg_replace('#[^a-z0-9_ -]#', '', strtolower($subject));
		$subject = strtr($subject, ' ', '-');
		$subject = substr($subject, 0, 30);

		$filename = time() . '.' . substr(md5(uniqid()), 0, 6) . '-' . $subject . '.eml';
		$outputFile = $this->_savePath . \XF::$DS . $filename;
		file_put_contents($outputFile, $message->toString());

		return $count;
	}

	/**
	 * Register a plugin.
	 *
	 * @param \Swift_Events_EventListener $plugin
	 */
	public function registerPlugin(\Swift_Events_EventListener $plugin)
	{
		$this->_eventDispatcher->bindEventListener($plugin);
	}
}
