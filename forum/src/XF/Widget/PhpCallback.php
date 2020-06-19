<?php

namespace XF\Widget;

class PhpCallback extends AbstractWidget
{
	public function render()
	{
		$class = $this->options['callback_class'];
		$class = $this->app->extendClass($class);
		$method = $this->options['callback_method'];

		return call_user_func_array([$class, $method], [$this]);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'callback_class' => 'str',
			'callback_method' => 'str'
		]);
		if (!\XF\Util\Php::validateCallbackPhrased($options['callback_class'], $options['callback_method'], $error))
		{
			return false;
		}
		return true;
	}
}