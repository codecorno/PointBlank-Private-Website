<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class UserConfirmation extends Repository
{
	public function getConfirmationRecordOrDefault(\XF\Entity\User $user, $type)
	{
		$confirmation = $this->em->find('XF:UserConfirmation', [$user->user_id, $type]);
		if (!$confirmation)
		{
			$confirmation = $this->em->create('XF:UserConfirmation');
			$confirmation->user_id = $user->user_id;
			$confirmation->confirmation_type = $type;
		}

		return $confirmation;
	}

	public function cleanUpUserConfirmationRecords($cutOff = null)
	{
		$this->db()->delete('xf_user_confirmation', 'confirmation_date <= ?', $cutOff ? $cutOff : time() - 3 * 86400);
	}
}