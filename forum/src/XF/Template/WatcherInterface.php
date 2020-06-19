<?php

namespace XF\Template;

interface WatcherInterface
{
	public function watchTemplate(Templater $templater, $type, $name);
	public function hasActionedTemplates();
}