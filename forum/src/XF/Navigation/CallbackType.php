<?php

namespace XF\Navigation;

class CallbackType extends AbstractType
{
	public function getTitle()
	{
		return \XF::phrase('callback');
	}
	
	public function validateConfigInput(\XF\Entity\Navigation $nav, array $config, Compiler $compiler, &$error = null, &$errorField = null)
	{
		$input = \XF::app()->inputFilterer()->filterArray($config, [
			'callback_class' => 'str',
			'callback_method' => 'str',
			'context' => 'str',
		]);

		if (!\XF\Util\Php::validateCallbackPhrased($input['callback_class'], $input['callback_method'], $error))
		{
			$errorField = 'callback_class';
			return false;
		}

		return [
			'callback' => [$input['callback_class'], $input['callback_method']],
			'context' => $input['context']
		];
	}

	public function compileCode(\XF\Entity\Navigation $nav, Compiler $compiler)
	{
		$config = $nav->type_config;

		$class = $config['callback'][0];
		if ($class && $class[0] != '\\')
		{
			$class = "\\" . $class;
		}
		$method = $config['callback'][1];

		$tempVar = '$__callbackTemp';

		$setupCode = "\t{$tempVar} = [" . $compiler->getStringCode($class) . ', ' . $compiler->getStringCode($method) . "];";

		$contextCode = $compiler->getStringCode($config['context']);
		$navDataCode = var_export([
			'navigation_id' => $nav->navigation_id
		], true);
		$selectedVar = $compiler->getSelectedVar();
		$dataExpression = "is_callable({$tempVar}) ? call_user_func({$tempVar}, {$navDataCode}, {$contextCode}, {$selectedVar}) : null";

		return new CompiledEntry($nav->navigation_id, $dataExpression, $setupCode);
	}
}