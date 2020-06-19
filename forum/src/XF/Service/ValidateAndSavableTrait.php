<?php

namespace XF\Service;

trait ValidateAndSavableTrait
{
	protected $validationComplete = false;
	protected $validationErrors = [];

	abstract protected function _validate();
	abstract protected function _save();

	public function validate(&$errors = [])
	{
		if (!$this->validationComplete)
		{
			$this->validationErrors = $this->_validate();
			if (!is_array($this->validationErrors))
			{
				throw new \LogicException("_validate in " . get_class($this) . " must return an array");
			}
			$this->validationComplete = true;
		}

		$errors = $this->validationErrors;
		return count($errors) == 0;
	}

	public function save()
	{
		if (!$this->validate($errors))
		{
			$error = reset($errors);
			throw new \LogicException(
				"Cannot save with validation errors. Use validate() to ensure there are no errors. "
				. "(First error: $error)"
			);
		}

		return $this->_save();
	}

	public function resetValidation()
	{
		$this->validationComplete = false;
		$this->validationErrors = [];
	}
}