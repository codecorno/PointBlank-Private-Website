<?php

namespace XF\Pub\Controller;

class Index extends AbstractController
{
	public function actionIndex()
	{
		return $this->reroutePath($this->app->router()->getIndexRoute());
	}
}