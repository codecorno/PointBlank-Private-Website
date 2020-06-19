<?php

namespace XF\InlineMod\Post;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

class Copy extends AbstractMoveCopy
{
	public function getTitle()
	{
		return \XF::phrase('copy_posts...');
	}

	protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
	{
		/** @var \XF\Entity\Post $entity */
		return $entity->canCopy($error);
	}

	public function applyInternal(AbstractCollection $entities, array $options)
	{
		$thread = $this->getTargetThreadFromOptions($options);

		/** @var \XF\Service\Post\Copier $copier */
		$copier = $this->app()->service('XF:Post\Copier', $thread);
		$copier->setExistingTarget($options['thread_type'] == 'existing' ? true : false);

		if ($options['alert'])
		{
			$copier->setSendAlert(true, $options['alert_reason']);
		}

		if ($options['prefix_id'] !== null && $options['thread_type'] !== 'existing')
		{
			$copier->setPrefix($options['prefix_id']);
		}

		$copier->copy($entities);

		$this->returnUrl = $this->app()->router('public')->buildLink('threads', $copier->getTarget());
	}

	public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = $this->app()->repository('XF:Node');
		$nodes = $nodeRepo->getFullNodeList()->filterViewable();

		$viewParams = [
			'posts' => $entities,
			'total' => count($entities),
			'nodeTree' => $nodeRepo->createNodeTree($nodes),
			'first' => $entities->first(),
			'prefixes' => $entities->first()->Thread->Forum->getUsablePrefixes()
		];
		return $controller->view('XF:Public:InlineMod\Post\Copy', 'inline_mod_post_copy', $viewParams);
	}
}