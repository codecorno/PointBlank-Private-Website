<?php

namespace XF\Job;

class Gravatar extends AbstractRebuildJob
{
	protected $defaultData = [
		'posters_only' => true,
		'import_table_name' => ''
	];

	protected function setupData(array $data)
	{
		// TODO: make these errors more friendly?

		if (!$this->app->options()->gravatarEnable)
		{
			throw new \XF\PrintableException("Gravatar support is switched off. Turn it on in 'Options:  User options' to continue.");
		}

		$db = $this->app->db();

		if (empty($data['import_table']))
		{
			unset($data['import_table_name']);
		}
		else if (!empty($data['import_table_name']))
		{
			if (!$importTable = $db->fetchOne("SHOW TABLES LIKE {$db->quote($data['import_table_name'])}"))
			{
				throw new \XF\PrintableException("The database does not contain a table called '{$data['import_table_name']}'.");
			}
		}

		return parent::setupData($data);
	}

	protected function getNextIds($start, $batch)
	{
		$db = $this->app->db();

		$importJoin = '';
		if (!empty($this->defaultData['import_table_name']))
		{
			$importJoin = "INNER JOIN {$this->defaultData['import_table_name']} AS iTable ON (iTable.content_type = 'user' AND iTable.new_id = xf_user.user_id)";
		}

		$postersCondition = $this->defaultData['posters_only'] ? 'AND user.message_count > 0' : '';

		return $db->fetchAllColumn($db->limit(
			"
				SELECT xf_user.user_id
				FROM xf_user
				{$importJoin}
				WHERE xf_user.user_id > ?
				AND xf_user.avatar_date = 0 AND xf_user.gravatar = ''
				ORDER BY xf_user.user_id
			", $batch
		), $start);
	}

	protected function rebuildById($id)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->app->finder('XF:User')->where('user_id', $id)->fetchOne();

		if ($this->app->validator('Gravatar')->isValid($user->email))
		{
			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->app->service('XF:User\Avatar', $user);

			$avatarService->setGravatar($user->email);
		}
	}

	protected function getStatusType()
	{
		return \XF::phrase('gravatar');
	}
}