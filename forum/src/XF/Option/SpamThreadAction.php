<?php

namespace XF\Option;

class SpamThreadAction extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList());

		return self::getTemplate('admin:option_template_spamThreadAction', $option, $htmlParams, [
			'nodeTree' => $nodeTree
		]);
	}

	public static function verifyOption(array &$value, \XF\Entity\Option $option)
	{
		if ($value['action'] == 'move')
		{
			if ($value['node_id'])
			{
				$node = \XF::em()->find('XF:Node', $value['node_id']);
				if ($node && $node->node_type_id === 'Forum')
				{
					return true;
				}
			}

			$option->error(\XF::phrase('please_specify_valid_spam_forum'), $option->option_id);
			return false;
		}

		return true;
	}
}