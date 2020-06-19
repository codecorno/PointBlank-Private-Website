<?php

namespace XF\Service\User;

class Delete extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Entity\User
	 */
	protected $user;

	protected $originalUsername;

	protected $renameTo = null;

	public function __construct(\XF\App $app, \XF\Entity\User $user)
	{
		parent::__construct($app);

		$this->user = $user;
		$this->originalUsername = $user->username;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function renameTo($name)
	{
		if ($name === $this->user->username)
		{
			$this->renameTo = null;
		}
		else
		{
			$this->renameTo = $name;
		}
	}

	public function delete(&$errors = null)
	{
		$user = $this->user;

		if (!$user->preDelete())
		{
			$errors = $user->getErrors();
			return false;
		}

		$this->db()->beginTransaction();

		if ($this->renameTo)
		{
			$user->reset();
			$user->setOption('admin_edit', true);
			$user->setOption('enqueue_rename_cleanup', false);
			$user->setOption('enqueue_delete_cleanup', false);

			$user->set('username', $this->renameTo);

			if (!$user->preSave())
			{
				$errors = $user->getErrors();
				return false;
			}

			$user->save(true, false);
		}

		$user->delete(true, false);

		$this->runPostDeleteJobs();

		$this->db()->commit();

		return true;
	}

	protected function runPostDeleteJobs()
	{
		$user = $this->user;

		if ($this->renameTo)
		{
			$jobList = [
				[
					'XF:UserRenameCleanUp',
					[
						'originalUserId' => $user->user_id,
						'originalUserName' => $this->originalUsername,
						'newUserName' => $this->renameTo
					]
				],
				[
					'XF:UserDeleteCleanUp',
					[
						'userId' => $user->user_id,
						'username' => $this->renameTo
					]
				]
			];
			$this->app->jobManager()->enqueueUnique('userRenameDelete' . $user->user_id, 'XF:Atomic', [
				'execute' => $jobList
			]);
		}
	}
}