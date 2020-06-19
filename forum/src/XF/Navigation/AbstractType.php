<?php

namespace XF\Navigation;

abstract class AbstractType
{
	protected $typeId;

	public function __construct($typeId)
	{
		$this->typeId = $typeId;
	}

	abstract public function getTitle();
	abstract public function validateConfigInput(\XF\Entity\Navigation $nav, array $config, Compiler $compiler, &$error = null, &$errorField = null);

	/**
	 * @param \XF\Entity\Navigation $nav
	 * @param Compiler $compiler
	 * @return CompiledEntry
	 */
	abstract public function compileCode(\XF\Entity\Navigation $nav, Compiler $compiler);

	public function renderEditForm(\XF\Entity\Navigation $nav, array $config, $formPrefix)
	{
		$params = array_replace([
			'navigation' => $nav,
			'config' => $config,
			'formPrefix' => $formPrefix
		], $this->getExtraEditParams($nav, $config));

		return \XF::app()->templater()->renderTemplate('admin:navigation_edit_type_' . $this->typeId, $params);
	}

	protected function getExtraEditParams(\XF\Entity\Navigation $nav, array $config)
	{
		return [];
	}
}