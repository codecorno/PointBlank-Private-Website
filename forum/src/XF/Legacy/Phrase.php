<?php

namespace XF\Legacy;

class Phrase
{
	/**
	 * @var \XF\Phrase
	 */
	protected $phrase;

	public function __construct($phraseName, array $params = [], $insertParamsEscaped = true)
	{
		$this->phrase = \XF::phrase($phraseName, $params);
	}

	public function setParams(array $params)
	{
		$this->phrase->setParams($params);
	}

	public function getParams()
	{
		return $this->phrase->getParams();
	}

	public function getPhraseName()
	{
		return $this->phrase->getName();
	}

	public function render($phraseNameOnInvalid = null)
	{
		return $this->phrase->render('html', [
			'nameOnInvalid' => $phraseNameOnInvalid
		]);
	}

	public function __toString()
	{
		return $this->phrase->__toString();
	}
}