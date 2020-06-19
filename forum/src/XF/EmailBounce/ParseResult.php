<?php

namespace XF\EmailBounce;

class ParseResult
{
	const TYPE_UNKNOWN = 'unknown';
	const TYPE_BOUNCE = 'bounce';
	const TYPE_DELAY = 'delay';
	const TYPE_CHALLENGE = 'challenge';
	const TYPE_AUTOREPLY = 'autoreply';

	public $deliveryStatusContent;
	public $textContent;
	public $originalContent;

	public $date;

	public $recipient;
	public $recipientTrusted = false;

	public $remoteStatus;
	public $remoteDiagnostics;

	public $messageType;
}