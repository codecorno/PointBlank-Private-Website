<?php
// FROM HASH: 3054dfb40d98514167969289792c1ef1
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="quote">
	';
	if ($__vars['name']) {
		$__finalCompiled .= '<div class="quote-name">' . '' . $__templater->escape($__vars['name']) . ' said' . $__vars['xf']['language']['label_separator'] . '</div>';
	}
	$__finalCompiled .= '
	' . $__templater->escape($__vars['content']) . '
</div>';
	return $__finalCompiled;
});