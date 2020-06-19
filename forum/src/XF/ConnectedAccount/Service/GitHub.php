<?php

namespace XF\ConnectedAccount\Service;

class GitHub extends \OAuth\OAuth2\Service\GitHub
{
	/**
	 * Read access to a user’s profile
	 */
	const SCOPE_READ_USER = 'read:user';
}