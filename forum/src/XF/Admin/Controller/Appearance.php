<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class Appearance extends AbstractController
{
	public function actionIndex()
	{
		return $this->view('XF:Appearance', 'appearance');
	}
}