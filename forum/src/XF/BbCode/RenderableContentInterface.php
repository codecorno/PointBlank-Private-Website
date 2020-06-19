<?php

namespace XF\BbCode;

interface RenderableContentInterface
{
	public function getBbCodeRenderOptions($context, $type);
}