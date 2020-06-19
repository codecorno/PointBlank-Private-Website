<?php

namespace XF\Install\Upgrade;

class Version2000037 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Beta 7';
	}

	public function step1()
	{
		$db = $this->db();

		// 200a upgrade assumed messageParticipants was an array - it isn't, it's a comma separated string. This means
		// 'messageParticipants' will now either be an array (because it was converted correctly or fixed manually) or still a string.
		$optionValue = $db->fetchOne('SELECT option_value FROM xf_option WHERE option_id = \'registrationWelcome\'');
		$optionValue = json_decode($optionValue, true);

		if (is_array($optionValue['messageParticipants']))
		{
			return;
		}

		$messageParticipants = @explode(', ', $optionValue['messageParticipants']);
		if ($messageParticipants)
		{
			$users = $db->fetchAllKeyed('SELECT * FROM xf_user WHERE username IN(' . $db->quote($messageParticipants) . ')', 'user_id');
			$messageParticipants = array_keys($users);
		}

		$optionValue['messageParticipants'] = $messageParticipants ?: [];
		$db->query('UPDATE xf_option SET option_value = ? WHERE option_id = \'registrationWelcome\'', json_encode($optionValue));
	}
}