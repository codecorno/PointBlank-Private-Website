<?php

namespace XF\Api\Docs\Renderer;

interface RendererInterface
{
	public function render(array $routeGroupings, array $types);

	public function setTarget($target, &$error = null, $force = false);
}