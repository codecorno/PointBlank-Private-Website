<?php

namespace XF\Alert;

use XF\Entity\UserAlert;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	public function canViewContent(Entity $entity, &$error = null)
	{
		if (method_exists($entity, 'canView'))
		{
			return $entity->canView($error);
		}

		throw new \LogicException("Could not determine content viewability; please override");
	}

	public function canViewAlert(UserAlert $alert, &$error = null)
	{
		return true;
	}

	/**
	 * For alerts which are pushed, you can create a push specific template which should be text only.
	 * If no value is provided or the template does not exist, the push contents will come from a
	 * rendered version of the normal alert template with HTML tags stripped.
	 *
	 * @param $action
	 * @return string|null
	 */
	public function getPushTemplateName($action)
	{
		return 'public:push_' . $this->contentType . '_' . $action;
	}

	public function getTemplateName($action)
	{
		return 'public:alert_' . $this->contentType . '_' . $action;
	}

	public function getTemplateData($action, UserAlert $alert, Entity $content = null)
	{
		if (!$content)
		{
			$content = $alert->Content;
		}

		return [
			'alert' => $alert,
			'user' => $alert->User,
			'extra' => $alert->extra_data,
			'content' => $content
		];
	}

	public function render(UserAlert $alert, Entity $content = null)
	{
		if (!$content)
		{
			$content = $alert->Content;
			if (!$content)
			{
				return '';
			}
		}

		$action = $alert->action;
		$template = $this->getTemplateName($action);
		$data = $this->getTemplateData($action, $alert, $content);

		return \XF::app()->templater()->renderTemplate($template, $data);
	}

	public function isAlertRenderable(UserAlert $alert)
	{
		$template = $this->getTemplateName($alert->action);
		return \XF::app()->templater()->isKnownTemplate($template);
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}

	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * An array of alert actions which can be opted out of for this type.
	 *
	 * @return array
	 */
	public function getOptOutActions()
	{
		return [];
	}

	/**
	 * The display order of this type's alert opt outs.
	 *
	 * @return int
	 */
	public function getOptOutDisplayOrder()
	{
		return 0;
	}

	/**
	 *
	 *
	 * @return array
	 */
	public function getOptOutsMap()
	{
		$optOuts = $this->getOptOutActions();
		if (!$optOuts)
		{
			return [];
		}

		return array_combine($optOuts, array_map(function($action)
		{
			return \XF::phrase('alert_opt_out.' . $this->contentType . '_' . $action);
		}, $optOuts));
	}
}