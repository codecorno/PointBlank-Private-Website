<?php

namespace XF\Mvc\Entity;

interface Proxyable
{
	public static function instantiateProxied(array $values);
}