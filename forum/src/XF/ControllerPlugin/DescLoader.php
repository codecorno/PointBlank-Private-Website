<?php

namespace XF\ControllerPlugin;

class DescLoader extends AbstractPlugin
{
	public function actionLoadDescription($shortName, $column = 'description')
	{
		$this->assertPostOnly();

		$entity = $this->assertRecordExists($shortName, $this->filter('id', 'str'));
		$description = $entity->{$column};

		$view = $this->view('XF:DescLoader');
		$view->setJsonParam('description', $description);
		return $view;
	}
}