<?php

namespace XF\ControllerPlugin;

class Toggle extends AbstractPlugin
{
	public function actionToggle($identifier, $column = 'active', array $options = [])
	{
		$this->assertPostOnly();

		$this->toggle($identifier, $column, $options);

		return $this->message(\XF::phrase('your_changes_have_been_saved'));
	}

	public function toggle($identifier, $column = 'active', array $options = [])
	{
		$options = array_replace([
			'input' => null,
			'preSaveCallback' => null
		], $options);

		if (!$options['input'])
		{
			$options['input'] = $column;
		}

		$activeState = $this->request->filter($options['input'], 'array-bool');
		$entities = $this->em->findByIds($identifier, array_keys($activeState));

		foreach ($entities AS $id => $entity)
		{
			if ($entity->getExistingValue($column) != $activeState[$id])
			{
				$entity->{$column} = $activeState[$id];

				if ($options['preSaveCallback'])
				{
					$cb = $options['preSaveCallback'];
					$cb($entity);
				}

				$entity->save();
			}
		}
	}
}