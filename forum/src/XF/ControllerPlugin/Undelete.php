<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Undelete extends AbstractPlugin
{
	public function actionUndelete(Entity $entity, $confirmUrl, $contentUrl, $contentTitle, $stateField, $visibleState = 'visible', $deletedState = 'deleted', $template = null, array $params = [])
	{
		if (!method_exists($entity, 'canUndelete'))
		{
			throw $this->exception(
				$this->error('Cannot determine if content can be undeleted. Entity requires a canUndelete method.')
			);
		}

		if ($this->isPost())
		{
			if ($entity->$stateField == $deletedState)
			{
				$entity->$stateField = $visibleState;
				$entity->save();
			}

			return $this->redirect($contentUrl);
		}
		else
		{
			$viewParams = [
				'content' => $entity,
				'confirmUrl' => $confirmUrl,
				'contentUrl' => $contentUrl,
				'contentTitle' => $contentTitle
			] + $params;
			return $this->view('XF:Undelete\Undelete', $template ?: 'public:undelete_confirm', $viewParams);
		}
	}
}