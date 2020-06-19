<?php

namespace XF\Authentication;

abstract class AbstractAuth
{
	protected $data = [];

	public function __construct(array $data = [])
	{
		$this->data = $data;
		$this->setup();
	}

	/**
	 * Initial setup based on the passed data (if available).
	 */
	protected function setup() {}

	/**
	 * Perform authentication against the given password
	 *
	 * @param integer $userId The user ID we're trying to authenticate as. This may not be needed, but can be used to "upgrade" auth schemes.
	 * @param string $password Password (plain text)
	 *
	 * @return bool True if the authentication is successful
	 */
	abstract public function authenticate($userId, $password);

	/**
	* Generate new authentication data for the given password
	*
	* @param string $password Password (plain text)
	*
	* @return false|array The result will be stored in a binary result
	*/
	abstract public function generate($password);

	/**
	 * Return the unique identifier that can be resolved back to this
	 * class.
	 *
	 * @return mixed
	 */
	abstract public function getAuthenticationName();

	/**
	 * Method which can return the default options for the authentication provider (if applicable).
	 *
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return [];
	}

	/**
	 * Returns true if the auth method provides a password. A user can switch away
	 * from this auth by requesting a password be emailed to him/her. An example of
	 * this situation is FB registrations.
	 *
	 * @return boolean
	 */
	public function hasPassword()
	{
		return true;
	}

	/**
	 * Indicates whether or not this authentication method can be upgraded to a different, better method.
	 *
	 * @return boolean
	 */
	public function isUpgradable()
	{
		return true;
	}
}
