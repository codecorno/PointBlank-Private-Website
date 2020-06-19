<?php

namespace XF\Install\Controller;

class Index extends AbstractController
{
	public function actionIndex()
	{
		$installHelper = $this->getInstallHelper();

		if ($installHelper->isInstalled())
		{
			return $this->redirect('index.php?upgrade/');
		}
		else
		{
			return $this->redirect('index.php?install/');
		}
	}
}