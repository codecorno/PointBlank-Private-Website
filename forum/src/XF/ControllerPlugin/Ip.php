<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Ip extends AbstractPlugin
{
	public function actionIp(Entity $content, array $breadcrumbs = [], $options = [])
	{
		$options = array_merge([
			'id' => 'ip_id',
			'view' => 'XF:Ip\Ip',
			'template' => 'content_ip_view',
			'extraViewParams' => []
		], $options);

		$visitor = \XF::visitor();

		if (!$visitor->canViewIps())
		{
			return $this->error(\XF::phrase('no_ip_information_available'));
		}

		$ip = null;
		if (!empty($content[$options['id']]))
		{
			$ip = $this->em()->find('XF:Ip', $content[$options['id']]);
		}

		if (!$ip)
		{
			return $this->error(\XF::phrase('no_ip_information_available'));
		}

		$viewParams = [
			'ip' => $ip,
			'content' => $content,
			'breadcrumbs' => $breadcrumbs
		];
		return $this->view($options['view'], $options['template'], $viewParams + $options['extraViewParams']);
	}
}