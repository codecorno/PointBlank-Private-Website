<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class Communication extends AbstractController
{
	public function actionIndex()
	{
		return $this->view('XF:Communication', 'communication');
	}
}