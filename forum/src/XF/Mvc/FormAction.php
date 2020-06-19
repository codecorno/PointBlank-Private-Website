<?php

namespace XF\Mvc;

use XF\Db\AbstractAdapter;
use XF\Mvc\Entity\Entity;
use XF\PrintableException;

class FormAction
{
	protected $setup = [];
	protected $validate = [];
	protected $apply = [];
	protected $complete = [];

	/**
	 * @var AbstractAdapter|null
	 */
	protected $dbTransaction;

	protected $errors = [];

	public function setup(\Closure $setup)
	{
		$this->setup[] = $setup;

		return $this;
	}

	public function setupEntityInput(Entity $entity, array $input)
	{
		$this->setup[] = function (FormAction $form) use ($entity, $input)
		{
			$entity->bulkSet($input);
		};

		return $this;
	}

	public function validate(\Closure $validate)
	{
		$this->validate[] = $validate;

		return $this;
	}

	public function validateEntity(Entity $entity)
	{
		$this->validate[] = function (FormAction $form) use ($entity)
		{
			$entity->preSave();
			$form->logErrors($entity->getErrors());
		};

		return $this;
	}

	public function apply(\Closure $apply)
	{
		$this->apply[] = $apply;

		return $this;
	}

	public function saveEntity(Entity $entity)
	{
		$this->apply[] = function(FormAction $form) use ($entity)
		{
			$entity->save(true, $form->isUsingTransaction() ? false : true);
		};

		return $this;
	}

	public function complete(\Closure $complete)
	{
		$this->complete[] = $complete;

		return $this;
	}

	public function basicEntitySave(Entity $entity, array $input)
	{
		$this->setupEntityInput($entity, $input)
			->validateEntity($entity)
			->saveEntity($entity);

		return $this;
	}

	public function basicValidateServiceSave(\XF\Service\AbstractService $service, \Closure $setup = null)
	{
		if (!method_exists($service, 'validate') || !method_exists($service, 'save'))
		{
			throw new \LogicException("Requires a service that implements the ValidateAndSavableTrait");
		}

		if ($setup)
		{
			$this->setup[] = $setup;
		}

		$this->validate[] = function (FormAction $form) use ($service)
		{
			if (!$service->validate($errors))
			{
				$form->logErrors($errors);
			}
		};

		$this->apply[] = function(FormAction $form) use ($service)
		{
			$service->save();
		};

		return $this;
	}

	public function applyInTransaction(AbstractAdapter $db = null)
	{
		$this->dbTransaction = $db;

		return $this;
	}

	public function isUsingTransaction()
	{
		return $this->dbTransaction ? true : false;
	}

	public function logError($error, $key = null)
	{
		if ($key !== null)
		{
			$this->errors[$key] = $error;
		}
		else
		{
			$this->errors[] = $error;
		}

		return $this;
	}

	public function logErrors(array $errors)
	{
		foreach ($errors AS $key => $error)
		{
			if (is_int($key))
			{
				$this->logError($error);
			}
			else
			{
				$this->logError($error, $key);
			}
		}
	}

	public function run($throwOnError = true)
	{
		foreach ($this->setup AS $setup)
		{
			$setup($this);
		}
		foreach ($this->validate AS $validate)
		{
			$validate($this);
		}

		if ($this->errors)
		{
			if ($throwOnError)
			{
				throw new PrintableException($this->errors);
			}
			return false;
		}

		if ($this->dbTransaction)
		{
			$this->dbTransaction->beginTransaction();
		}

		foreach ($this->apply AS $apply)
		{
			$apply($this);
		}

		if ($this->dbTransaction)
		{
			$this->dbTransaction->commit();
		}

		foreach ($this->complete AS $complete)
		{
			$complete($this);
		}

		return true;
	}
}