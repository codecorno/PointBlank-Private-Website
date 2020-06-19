<?php

namespace XF\Captcha;

use XF\Template\Templater;

abstract class AbstractCaptcha
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * Rendered output cache.
	 */
	protected $_rendered = null;

	public function __construct(\XF\App $app)
	{
		$this->app = $app;
	}

	/**
	 * Renders the CAPTCHA for use in a template. This should only render the CAPTCHA area itself.
	 * The CAPTCHA may be used in a form row or own its own.
	 */
	abstract public function renderInternal(Templater $templater);

	public function render(Templater $templater)
	{
		if ($this->_rendered === null)
		{
			$this->_rendered = $this->renderInternal($templater);
		}

		return $this->_rendered;
	}

	/**
	 * Determines if the CAPTCHA has been passed.
	 *
	 * @return boolean
	 */
	abstract public function isValid();
}