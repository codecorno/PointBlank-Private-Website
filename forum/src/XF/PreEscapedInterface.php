<?php

namespace XF;

interface PreEscapedInterface
{
	public function getPreEscapeType();
	public function __toString();
}