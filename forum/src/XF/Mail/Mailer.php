<?php

namespace XF\Mail;

class Mailer
{
	/**
	 * @var Templater
	 */
	protected $templater;

	/**
	 * @var \Swift_Transport
	 */
	protected $defaultTransport;

	/**
	 * @var Styler|null
	 */
	protected $styler;

	/**
	 * @var Queue|null
	 */
	protected $queue;

	protected $defaultFromEmail;
	protected $defaultFromName;
	protected $defaultReturnPath;
	protected $defaultUseVerp;

	protected $mailClass = 'XF\Mail\Mail';

	public function __construct(Templater $templater, \Swift_Transport $defaultTransport, Styler $styler = null, Queue $queue = null)
	{
		$this->templater = $templater;
		$this->defaultTransport = $defaultTransport;
		$this->styler = $styler;
		$this->queue = $queue;
	}

	public function getMailClass()
	{
		return $this->mailClass;
	}

	public function setMailClass($class)
	{
		$this->mailClass = $class;
	}

	public function setDefaultFrom($email, $name = null)
	{
		if ($email)
		{
			$this->defaultFromEmail = $email;
			$this->defaultFromName = $name;
		}
		else
		{
			$this->defaultFromEmail = null;
			$this->defaultFromName = null;
		}
	}

	public function getDefaultFromEmail()
	{
		return $this->defaultFromEmail;
	}

	public function getDefaultFromName()
	{
		return $this->defaultFromName;
	}

	public function setDefaultReturnPath($email, $useVerp = false)
	{
		if ($email)
		{
			$this->defaultReturnPath = $email;
			$this->defaultUseVerp = $useVerp;
		}
		else
		{
			$this->defaultReturnPath = null;
			$this->defaultUseVerp = null;
		}
	}

	public function getDefaultReturnPath()
	{
		return $this->defaultReturnPath;
	}

	public function getDefaultUseVerp()
	{
		return $this->defaultUseVerp;
	}

	/**
	 * @return \XF\Mail\Mail
	 */
	public function newMail()
	{
		$mailClass = $this->mailClass;
		$mail = new $mailClass($this);
		$this->applyMailDefaults($mail);

		return $mail;
	}

	public function applyMailDefaults(Mail $mail)
	{
		if ($this->defaultFromEmail)
		{
			$mail->setFrom($this->defaultFromEmail, $this->defaultFromName);
		}
		if ($this->defaultReturnPath)
		{
			$mail->setReturnPath($this->defaultReturnPath, $this->defaultUseVerp);
		}
	}

	public function calculateBounceHmac($toEmail)
	{
		return substr(hash_hmac('md5', $toEmail, \XF::config('globalSalt')), 0, 8);
	}

	public function generateTextBody($html)
	{
		if ($this->styler)
		{
			return $this->styler->generateTextBody($html);
		}
		else
		{
			return '';
		}
	}

	public function renderMailTemplate($name, array $params, \XF\Language $language = null, \XF\Entity\User $toUser = null)
	{
		if (!$language)
		{
			$language = \XF::language();
		}

		$defaultParams = $this->getDefaultTemplateParams($language, $toUser);

		$templater = $this->templater;
		$templater->setLanguage($language);
		$templater->addDefaultParam('xf', $defaultParams);
		$templater->pageParams = [];

		$output = $templater->renderTemplate("email:$name", $params);
		$parts = $this->pullComponentsFromTemplateOutput($output);

		if (!$parts['text'] && !$parts['html'])
		{
			throw new \LogicException("Template mail:$name did not render to anything. It must provide either a text or HTML body.");
		}

		$containerTemplate = isset($templater->pageParams['template']) ? $templater->pageParams['template'] : 'MAIL_CONTAINER';
		if ($containerTemplate)
		{
			if (!strpos($containerTemplate, ':'))
			{
				$containerTemplate = 'email:' . $containerTemplate;
			}

			$containerParams = array_replace($templater->pageParams, $parts);

			$containerOutput = $templater->renderTemplate($containerTemplate, $containerParams);
			$containerParts = $this->pullComponentsFromTemplateOutput($containerOutput);
		}
		else
		{
			$containerParts = ['subject' => '', 'html' => '', 'text' => ''];
		}

		$subject = $parts['subject'] && $containerParts['subject'] ? $containerParts['subject'] : $parts['subject'];
		$html = $parts['html'] && $containerParts['html'] ? $containerParts['html'] : $parts['html'];
		$text = $parts['text'] && $containerParts['text'] ? $containerParts['text'] : $parts['text'];

		if ($this->styler)
		{
			$html = $this->styler->styleHtml($html, $containerTemplate ? true : false, $language);
		}

		if (isset($templater->pageParams['headers']) && is_array($templater->pageParams['headers']))
		{
			$headers = $templater->pageParams['headers'];
		}
		else
		{
			$headers = [];
		}

		return [
			'subject' => $subject,
			'html' => $html,
			'text' => $text,
			'headers' => $headers
		];
	}

	protected function getDefaultTemplateParams(\XF\Language $language, \XF\Entity\User $toUser = null)
	{
		return [
			'language' => $language,
			'isRtl' => $language->isRtl(),
			'options' => \XF::options(),
			'toUser' => $toUser
		];
	}

	protected function pullComponentsFromTemplateOutput($output)
	{
		if (preg_match('#<mail:subject>(.*)</mail:subject>#siU', $output, $match))
		{
			$subject = trim(htmlspecialchars_decode($match[1], ENT_QUOTES));
			$output = preg_replace('#<mail:subject>.*</mail:subject>#siU', '', $output);
		}
		else
		{
			$subject = '';
		}

		if (preg_match('#<mail:text>(.*)</mail:text>#siU', $output, $match))
		{
			$text = trim(htmlspecialchars_decode($match[1], ENT_QUOTES));
			$output = preg_replace('#<mail:text>.*</mail:text>#siU', '', $output);
		}
		else
		{
			$text = '';
		}

		if (preg_match('#<mail:html>(.*)</mail:html>#siU', $output, $match))
		{
			$html = trim($match[1]);
		}
		else
		{
			$html = trim($output);
		}

		if (!$text && $html)
		{
			$text = $this->generateTextBody($html);
		}

		return [
			'subject' => $subject,
			'html' => $html,
			'text' => $text
		];
	}

	public function send(\Swift_Mime_Message $message, \Swift_Transport $transport = null, array $queueEntry = null, $allowRetry = true)
	{
		$to = $message->getTo();
		$toEmails = $to ? implode(', ', array_keys($to)) : '[unknown]';

		if (!$transport)
		{
			$transport = $this->defaultTransport;
		}

		if (!$transport->isStarted())
		{
			try
			{
				$transport->start();
			}
			catch (\Exception $e)
			{
				if ($this->queue && $allowRetry)
				{
					$this->queue->queueForRetry($message, $queueEntry);
				}

				\XF::logException($e, false, "Email to {$toEmails} failed:");
				return 0;
			}
		}

		$sent = 0;

		try
		{
			$sent = $transport->send($message, $failedRecipients);
			if (!$sent)
			{
				throw new \Swift_TransportException('Unable to send mail.');
			}
		}
		catch (\Exception $e)
		{
			if ($this->queue && $allowRetry)
			{
				$this->queue->queueForRetry($message, $queueEntry);
			}

			\XF::logException($e, false, "Email to {$toEmails} failed:");
		}

		return $sent;
	}

	public function queue(\Swift_Mime_Message $message)
	{
		if (!$this->queue)
		{
			// Queue object not passed in (may be disabled in config.php) so skip straight to send
			return $this->send($message);
		}
		return $this->queue->queue($message);
	}

	public function getDefaultTransport()
	{
		return $this->defaultTransport;
	}

	public function setDefaultTransport(\Swift_Transport $transport)
	{
		$this->defaultTransport = $transport;
	}

	public static function getTransportFromOption($type, array $config)
	{
		switch ($type)
		{
			case 'smtp';
				$transport = \Swift_SmtpTransport::newInstance($config['smtpHost']);

				if (!empty($config['smtpPort']) && intval($config['smtpPort']) != 0)
				{
					$transport->setPort(intval($config['smtpPort']));
				}
				if (!empty($config['smtpAuth']) && $config['smtpAuth'] != 'none')
				{
					$transport->setUsername(
						!empty($config['smtpLoginUsername']) ? $config['smtpLoginUsername'] : ''
					);
					$transport->setPassword(
						!empty($config['smtpLoginPassword']) ? $config['smtpLoginPassword'] : ''
					);
				}
				if (!empty($config['smtpEncrypt']) && $config['smtpEncrypt'] != 'none')
				{
					$transport->setEncryption($config['smtpEncrypt']);
				}

				$transport->registerPlugin(new \Swift_Plugins_AntiFloodPlugin(99));

				return $transport;

			case 'file':
				$transport = new \XF\Mail\FileTransport(
					\Swift_DependencyContainer::getInstance()->lookup('transport.eventdispatcher')
				);
				if (!empty($config['path']))
				{
					$transport->setSavePath($config['path']);
				}

				return $transport;

			case 'sendmail':
			default:
				if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
				{
					$iniSmtpHost = ini_get('SMTP');
					$iniSmtpPort = ini_get('smtp_port');
					$transport = \Swift_SmtpTransport::newInstance($iniSmtpHost ?: 'localhost', $iniSmtpPort ?: 25);
				}
				else
				{
					$sendmailPath = \XF::app()->config('sendmailPath');
					if (!$sendmailPath)
					{
						$sendmailPath = ini_get('sendmail_path');
					}

					if ($sendmailPath && !preg_match('# -(t|bs)#', $sendmailPath))
					{
						// SwiftMailer requires -t or -bs, so if there isn't one, add -t automatically to prevent errors
						$sendmailPath .= ' -t';
					}

					if (preg_match('/(.*)-f\s?[^@]+@[^\s]+(.*)$/', $sendmailPath, $matches))
					{
						// if the sendmail path already contains the -f parameter, SwiftMailer won't override it in which
						// case, we should remove it by default so it can be set automatically to the appropriate value
						$sendmailPath = trim(rtrim($matches[1]) . $matches[2]);
					}

					if (!$sendmailPath)
					{
						$sendmailPath = '/usr/sbin/sendmail -t -i';
					}

					$transport = \Swift_SendmailTransport::newInstance($sendmailPath);
				}

				return $transport;
		}
	}
}