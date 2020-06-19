<?php

namespace XF\EmailBounce;

use Zend\Mail\Storage\Message;

class Parser
{
	protected $verpBase;
	protected $verpHmacKey;

	protected $statusMap = [
		'9.9.9' => ['type' => 'soft', 'desc' => 'unknown_status'],
		'*.1.0' => ['type' => 'soft', 'desc' => 'unknown_address_status'],
		'*.1.1' => ['type' => 'hard', 'desc' => 'bad_destination_mailbox'],
		'*.1.2' => ['type' => 'hard', 'desc' => 'bad_destination_system'],
		'*.1.3' => ['type' => 'hard', 'desc' => 'bad_destination_mailbox_syntax'],
		'*.1.4' => ['type' => 'hard', 'desc' => 'destination_mailbox_ambiguous'],
		'*.1.5' => ['type' => 'none', 'desc' => 'destination_address_valid'],
		'*.1.6' => ['type' => 'hard', 'desc' => 'mailbox_moved'],
		'*.1.7' => ['type' => 'hard', 'desc' => 'bad_sender_mailbox_syntax'],
		'*.1.8' => ['type' => 'hard', 'desc' => 'bad_sender_system'],
		'*.2.0' => ['type' => 'soft', 'desc' => 'unknown_mailbox_status'],
		'*.2.1' => ['type' => 'hard', 'desc' => 'mailbox_disabled'],
		'*.2.2' => ['type' => 'soft', 'desc' => 'mailbox_full'],
		'*.2.3' => ['type' => 'soft', 'desc' => 'message_length_too_long'],
		'*.2.4' => ['type' => 'soft', 'desc' => 'mailing_list_expansion_problem'],
		'*.3.0' => ['type' => 'soft', 'desc' => 'unknown_system_issue'],
		'*.3.1' => ['type' => 'soft', 'desc' => 'mail_system_full'],
		'*.3.2' => ['type' => 'soft', 'desc' => 'system_not_accepting_messages'],
		'*.3.3' => ['type' => 'soft', 'desc' => 'system_not_capable_features'],
		'*.3.4' => ['type' => 'soft', 'desc' => 'message_too_big'],
		'*.3.5' => ['type' => 'soft', 'desc' => 'system_incorrectly_configured'],
		'*.4.0' => ['type' => 'soft', 'desc' => 'unknown_routing_status'],
		'*.4.1' => ['type' => 'soft', 'desc' => 'no_answer_from_host'],
		'*.4.2' => ['type' => 'soft', 'desc' => 'bad_connection'],
		'*.4.3' => ['type' => 'soft', 'desc' => 'directory_server_failure'],
		'*.4.4' => ['type' => 'soft', 'desc' => 'unable_to_route'],
		'*.4.5' => ['type' => 'soft', 'desc' => 'mail_system_congestion'],
		'*.4.6' => ['type' => 'soft', 'desc' => 'routing_loop'],
		'*.4.7' => ['type' => 'soft', 'desc' => 'delivery_time_expired'],
		'*.4.9' => ['type' => 'soft', 'desc' => 'routing_error'],
		'*.5.*' => ['type' => 'soft', 'desc' => 'protocol_status_issue'],
		'*.6.*' => ['type' => 'soft', 'desc' => 'media_status_issue'],
		'*.7.*' => ['type' => 'soft', 'desc' => 'security_status_issue'],
		'*.*.*' => ['type' => 'soft', 'desc' => 'unknown_issue'],
	];

	protected $generalBounceStrings = [
		'This is a permanent error',
		'could not be delivered',
		'could not deliver message',
		'#Delivery.+failed\s+permanently#',
		'did not reach the following',
		'was undeliverable',
		'was not delivered to',
		'permanent fatal errors',
		'error while attempting',
		'automatically rejected',
		'recipient(s) could not be reached',

		// need to include these to trigger bounce behavior - this is really only the case
		// for REALLY bad bounce messages
		'mailbox exceeds allowed',
		'not a valid user here'
	];

	protected $generalDelayStrings = [
		'message that you sent has not yet been delivered',
		'message has not yet been delivered',
		'delivery attempts will continue'
	];

	protected $invalidMailboxStrings = [
		'deactivated mailbox',
		'mail-box not found',
		'mailbox not found',
		'mailbox currently suspended',
		'mailbox suspended',
		'does not exist',
		'mailbox not available',
		'mailbox unavailable',
		'no mail box available',
		'no mailbox here',
		'addressee unknown',
		'unknown user',
		'user unknown',
		'user is unknown',
		'user not found',
		'not a known user',
		'not our customer',
		'recipient rejected',
		'deactivated mailbox',
		'no such user',
		'no such person',
		'no such address',
		'no mailbox found',
		'no longer on server',
		'not a valid mailbox',
		'invalid mailbox',
		'#account.+is\s+disabled#',
		'#no\s+mailbox.+currently\s+available#',
		'account has been disabled',
		'is not an active address',
		'not listed in domino directory',
		'not a valid user here',
		'name is not recognized',
		'mail receiving disabled',
		'couldn\'t be found is is invalid',
		'doesn\'t have an account',
		'#doesn\'t\s+have.+account#',
		'addresses failed'
	];

	protected $quotaStrings = [
		'mailbox exceeds allowed',
		'mailbox size limit exceeded',
		'quota exceeded',
		'full mailbox',
		'mailbox is full',
		'mailbox full',
		'mailfolder is full',
		'over quota'
	];

	protected $challengeStrings = [
		'boxbe.com',
		'bluebottle.com',
		'click on the following link',
		'requires confirmation',
		'sender not pre-approved',
		'spamarrest.com',
		'to complete this verification',
		'verification process',
		'antispam.uol.com.br'
	];

	protected $autoReplyStrings = [
		'from the office',
		'out of office',
		'out of the office',
		'on holiday',
		'on vacation',
		'out of town',
		'when I return',

		'message has been received',
		'reply as soon as possible',
		'acknowledge the receipt',

		'automated reply',
		'automated response',
		'automate response',
		'autoreply',
		'autoresponder',
	];

	public function __construct($verpBase, $verpHmacKey)
	{
		$this->verpBase = $verpBase;
		$this->verpHmacKey = $verpHmacKey;
	}

	public function parseMessage($message)
	{
		if (is_string($message))
		{
			$rawMessage = $message;
			$message = new Message(['raw' => $message]);

			try
			{
				$message->countParts();
			}
			catch (\Zend\Mail\Exception\RuntimeException $e)
			{
				// Workaround https://github.com/zendframework/zend-mime/pull/7
				// This was fixed in ZF 2.5.2, but that has higher requirements than us so we can't currently use that version.
				if (
					$e->getMessage() == 'Malformed header detected'
					&& isset($message->contentType)
					&& preg_match('/boundary=("[^"]+"|(?:[^\s]+|$))/is', $message->contentType, $boundaryMatch)
				)
				{
					$boundary = trim($boundaryMatch[1], '"');
					$rawMessage = preg_replace(
						'/(\r?\n--' . preg_quote($boundary, '/') . '\r?\n)(\r?\n)/',
						'$1X-XF-Temp-Header: ignore$2$2',
						$rawMessage
					);
					$message = new Message(['raw' => $rawMessage]);
				}
			}
		}
		else if (!($message instanceof Message))
		{
			throw new \InvalidArgumentException("Message must be a string containing headers and message or Message object");
		}

		$result = new ParseResult();

		$this->extractDate($message, $result);
		$this->extractContent($message, $result);
		$this->extractRecipient($message, $result);

		if ($result->deliveryStatusContent)
		{
			$this->parseDeliveryStatus($message, $result);
		}
		else
		{
			$this->parseFromTextContent($message, $result);
		}

		return $result;
	}

	protected function extractDate(Message $message, ParseResult $result)
	{
		$timestamp = \XF::$time;

		if (isset($message->date))
		{
			try
			{
				$dt = new \DateTime($message->date);
				$timestamp = intval($dt->format('U'));
			}
			catch (\Exception $e) {}
		}

		$result->date = $timestamp;
	}

	protected function extractContent(Message $message, ParseResult $result)
	{
		$textContent = null;
		$originalContent = null;
		$deliveryStatus = null;

		if ($message->isMultipart())
		{
			foreach ($message AS $part)
			{
				/** @var \Zend\Mail\Storage\Part $part */
				if (!isset($part->contentType))
				{
					if (!$textContent)
					{
						$content = trim($part->getContent());
						if (strlen($content) && preg_match('/./su', $content))
						{
							// if it's valid UTF-8, it's probably textual
							$textContent = $content;
						}
					}

					continue;
				}

				$contentType = $part->contentType;
				if (preg_match('#^message/delivery-status#i', $contentType))
				{
					$deliveryStatus = $part->getContent();
				}
				else if (preg_match('#^message/rfc822#i', $contentType))
				{
					$originalContent = $part->getContent();
				}
				else if (preg_match('#^text/rfc822-headers#i', $contentType) && !$originalContent)
				{
					$originalContent = $part->getContent();
				}
				else if (preg_match('#^text/plain#i', $contentType))
				{
					$textContent = $part->getContent();
				}
			}
		}

		if ($textContent === null)
		{
			$textContent = $message->getContent();
		}
		if ($originalContent === null)
		{
			list($textContent, $originalContent) = $this->splitOriginalFromText($textContent);
		}

		$result->deliveryStatusContent = $deliveryStatus ? trim($deliveryStatus) : null;
		$result->textContent = trim($textContent);
		$result->originalContent = trim($originalContent);
	}

	protected function splitOriginalFromText($textContent)
	{
		$originalContent = '';

		if (preg_match('/^(.*)--- message header[^\r\n]*(.*)$/siU', $textContent, $match))
		{
			$textContent = $match[1];
			$originalContent = $match[2];
		}
		else if (preg_match('/^(.*)\n\s*Original message[^\r\n]*(.*)$/siU', $textContent, $match))
		{
			$textContent = $match[1];
			$originalContent = $match[2];
		}
		else if (preg_match('/^(.*)\n\s*-- The header and [^\r\n]*(.*)$/siU', $textContent, $match))
		{
			$textContent = $match[1];
			$originalContent = $match[2];
		}
		else if (preg_match('/^(.*)---[^\r\n]*(.*)$/siU', $textContent, $match))
		{
			$textContent = $match[1];
			$originalContent = $match[2];
		}

		$textContent = trim($textContent);
		$originalContent = trim($originalContent);

		return [$textContent, $originalContent];
	}

	protected function extractRecipient(Message $message, ParseResult $result)
	{
		if ($this->verpBase)
		{
			if (preg_match('#\n\s*X-To-Validate\s*:\s*([a-z0-9]+)\+([^\s]+)(\s|$)#i', $result->originalContent, $match))
			{
				$email = $match[2];
				$hmac = hash_hmac('md5', $email, $this->verpHmacKey);

				$result->recipientTrusted = (substr($hmac, 0, strlen($match[1])) === $match[1]);
				$result->recipient = $email;
				return;
			}

			if ($this->verpBase && isset($message->to))
			{
				$matchRegex = str_replace('@', '\+([a-z0-9]+)\+([^@=]+=[^@=]+)@', preg_quote($this->verpBase, '#'));
				if (preg_match("#$matchRegex#i", $message->to, $verpMatch))
				{
					$verpEmail = str_replace('=', '@', $verpMatch[2]);
					$hmac = hash_hmac('md5', $verpEmail, $this->verpHmacKey);

					$result->recipientTrusted = (substr($hmac, 0, strlen($verpMatch[1])) === $verpMatch[1]);
					$result->recipient = $verpEmail;
					return;
				}
			}
		}
		else
		{
			// no VERP enabled, so we need to trust the recipient that we find
			$result->recipientTrusted = true;
		}

		if (isset($message->xFailedRecipients))
		{
			$result->recipient = $message->xFailedRecipients;
		}
		else if (preg_match('#\n\s*To\s*:[^\n<]*?<([^>@]+@[^>]+)>(\s|,|$)#i', $result->originalContent, $match))
		{
			$result->recipient = $match[1];
		}
		else if (preg_match('#\n\s*To\s*:([^>@]+@[^\s]+)(\r?\n|,|$)#i', $result->originalContent, $match))
		{
			$result->recipient = $match[1];
		}
	}

	protected function parseDeliveryStatus(Message $message, ParseResult $result)
	{
		$statusContent = preg_replace('#\r?\n\r?\n#', "\n", $result->deliveryStatusContent);
		try
		{
			\Zend\Mime\Decode::splitMessage($statusContent, $headers, $null);
		}
		catch (\Zend\Mail\Exception\ExceptionInterface $e)
		{
			// try to force this to be valid
			$statusContent = preg_replace('/[\x80-\xFF]/', '', $statusContent);
			\Zend\Mime\Decode::splitMessage($statusContent, $headers, $null);
		}

		/** @var \Zend\Mail\Headers $headers */

		$statusFields = [];
		foreach ($headers AS $header)
		{
			$statusFields[strtolower($header->getFieldName())] = $header->getFieldValue();
		}

		if (!empty($statusFields['action']))
		{
			$remoteAction = strtolower($statusFields['action']);
			if ($remoteAction == 'failed')
			{
				$result->messageType = ParseResult::TYPE_BOUNCE;
			}
			else if ($remoteAction == 'delayed')
			{
				$result->messageType = ParseResult::TYPE_DELAY;
			}
		}

		if (!empty($statusFields['status'])
			&& preg_match('/(\d\.\d\.\d)/', $statusFields['status'], $match)
		)
		{
			$result->remoteStatus = $match[1];
		}

		if ($result->messageType == ParseResult::TYPE_BOUNCE && !$result->remoteStatus)
		{
			$result->remoteStatus = '9.9.9';
		}

		if (!empty($statusFields['diagnostic-code']))
		{
			$result->remoteDiagnostics = preg_replace('#^.+;\s*#U', '', $statusFields['diagnostic-code']);

			if (!$result->remoteStatus || $this->isStatusAmbiguous($result->remoteStatus))
			{
				if (preg_match('/(\D|^)(\d\.\d\.\d)(\D|$)/', $result->remoteDiagnostics, $match))
				{
					$result->remoteStatus = $match[2];
				}
			}
		}

		$diagnostics = $result->remoteDiagnostics;
		$isBounce = ($result->messageType == ParseResult::TYPE_BOUNCE);

		if ($isBounce && $this->isMailboxInvalidError($diagnostics))
		{
			$result->remoteStatus = '5.1.1';
		}
		else if (
			$isBounce
			&& $this->isStatusAmbiguous($result->remoteStatus)
			&& $this->isMailboxQuotaError($diagnostics)
		)
		{
			$result->remoteStatus = '5.2.2';
		}
		else if ($this->isChallengeMessage($message, $result))
		{
			$result->messageType = ParseResult::TYPE_CHALLENGE;
			$result->remoteStatus = null;
		}
	}

	protected function parseFromTextContent(Message $message, ParseResult $result)
	{
		if (!$result->messageType)
		{
			$this->parseTextForBounce($message, $result);
		}
		if (!$result->messageType)
		{
			$this->parseTextForChallenge($message, $result);
		}
		if (!$result->messageType)
		{
			$this->parseTextForAutoReply($message, $result);
		}
	}

	protected function parseTextForBounce(Message $message, ParseResult $result)
	{
		$textContent = $result->textContent;

		if ($this->matchesStringList($textContent, $this->generalBounceStrings))
		{
			$result->messageType = ParseResult::TYPE_BOUNCE;
			$result->remoteStatus = '9.9.9'; // failure of some sort - treat it as a soft bounce
		}
		else if ($this->matchesStringList($textContent, $this->generalDelayStrings))
		{
			$result->messageType = ParseResult::TYPE_DELAY;
			$result->remoteStatus = '9.9.9';
		}

		if ($result->messageType)
		{
			if (preg_match('/\d\d\d(?:\s+|\s*,\s*)(\d\.\d\.\d)([^\r\n]*)(\r|\n|$)/', $textContent, $codeMatch))
			{
				$result->remoteStatus = $codeMatch[1];
				$result->remoteDiagnostics = trim($codeMatch[2]);
			}
			else if (preg_match('/#(\d\.\d\.\d)/', $textContent, $codeMatch))
			{
				$result->remoteStatus = $codeMatch[1];
				$result->remoteDiagnostics = '';
			}
		}

		if ($result->messageType == ParseResult::TYPE_BOUNCE)
		{
			if ($this->isMailboxInvalidError($textContent))
			{
				$result->remoteStatus = '5.1.1';
			}
			else if ($this->isMailboxQuotaError($textContent))
			{
				$result->remoteStatus = '5.2.2';
			}
		}

		if (
			preg_match('/^Hi\.\s+This\s+is\s+the/i', $textContent) // qmail
			&& preg_match('/\n<([^>\s)]+@[^>\s)]+)>:?\s*([^\r\n]*)([\r\n]|$)/i', $textContent, $match)
		)
		{
			$result->remoteDiagnostics = trim($match[2]);
		}
	}

	protected function parseTextForChallenge(Message $message, ParseResult $result)
	{
		if ($this->isChallengeMessage($message, $result))
		{
			$result->messageType = ParseResult::TYPE_CHALLENGE;
		}
	}

	protected function parseTextForAutoReply(Message $message, ParseResult $result)
	{
		if (
			isset($message->xAutorespond)
			|| isset($message->xAutoreply)
			|| (isset($message->precedence) && $message->precedence == 'auto_reply')
			|| (isset($message->precedence) && $message->precedence == 'junk')
			|| (isset($message->xPrecedence) && $message->xPrecedence == 'auto_reply')
			|| (isset($message->autoSubmitted) && preg_match('/auto-/i', $message->autoSubmitted))
		)
		{
			$result->messageType = ParseResult::TYPE_AUTOREPLY;
		}
		else if ($this->matchesStringList($result->textContent, $this->autoReplyStrings))
		{
			$result->messageType = ParseResult::TYPE_AUTOREPLY;
		}
	}

	public function isMailboxInvalidError($content)
	{
		return $this->matchesStringList($content, $this->invalidMailboxStrings);
	}

	public function isMailboxQuotaError($content)
	{
		return $this->matchesStringList($content, $this->quotaStrings);
	}

	public function isChallengeMessage(Message $message, ParseResult $result)
	{
		if (
			($result->remoteStatus == '4.7.0' || !$result->remoteStatus)
			&& $this->isChallengeMessageContent($result->textContent, $message)
		)
		{
			return true;
		}

		if (
			$this->isStatusAmbiguous($result->remoteStatus)
			&& $this->isChallengeMessageContent($result->remoteDiagnostics, $message)
		)
		{
			return true;
		}

		return false;
	}

	protected function isChallengeMessageContent($content, Message $message)
	{
		if ($this->matchesStringList($content, $this->challengeStrings))
		{
			return true;
		}

		if (isset($message->xUolSrv) && $message->xUolSrv == 'T')
		{
			return true;
		}

		return false;
	}

	protected function matchesStringList($content, array $strings)
	{
		if (!$content)
		{
			return false;
		}

		$list = $this->convertStringListToRegex($strings);
		return preg_match('#' . $list . '#si', $content);
	}

	protected function convertStringListToRegex(array $strings, $delim = '#')
	{
		$options = [];
		foreach ($strings AS $string)
		{
			if ($string[0] == $delim)
			{
				$options[] = '(' . substr($string, 1, -1) . ')';
			}
			else
			{
				$string = preg_quote($string, $delim);
				$options[] = str_replace(' ', '\s+', $string);
			}
		}

		return '(' . implode('|', $options) . ')';
	}

	public function isStatusAmbiguous($status)
	{
		if (!$status)
		{
			return true;
		}

		switch ($status)
		{
			case '5.0.0':
			case '4.0.0':
			case '9.9.9':
				return true;

			default:
				return false;
		}
	}

	public function getStatusDetailsFromCode($statusCode)
	{
		if (!$statusCode || !preg_match('#^\d\.\d\.\d$#', $statusCode))
		{
			return null;
		}

		foreach ($this->statusMap AS $code => $details)
		{
			$code = str_replace('\*', '\d+', preg_quote($code, '/'));
			if (preg_match("/^{$code}$/", $statusCode))
			{
				return $details;
			}
		}

		return $this->statusMap['*.*.*'];
	}

	public function getBounceTypeFromStatus($statusCode)
	{
		if (!$statusCode || !preg_match('#^\d\.\d\.\d$#', $statusCode))
		{
			return null;
		}

		foreach ($this->statusMap AS $code => $details)
		{
			$code = str_replace('\*', '\d+', preg_quote($code, '/'));
			if (preg_match("/^{$code}$/", $statusCode))
			{
				return $details['type'];
			}
		}

		return $this->statusMap['*.*.*']['type'];
	}
}