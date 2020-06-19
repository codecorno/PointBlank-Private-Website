<?php
// FROM HASH: 2c5a523767f6909f0aaabc92a2abd725
return array('macros' => array('metadata' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'siteName' => '',
		'title' => '',
		'description' => '',
		'type' => '',
		'shareUrl' => '',
		'canonicalUrl' => '',
		'imageUrl' => '',
		'twitterCard' => 'summary',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if (!$__templater->test($__vars['siteName'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'site_name', array(
			'siteName' => $__vars['siteName'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['title'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'title', array(
			'title' => $__vars['title'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['description'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'description', array(
			'description' => $__vars['description'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['type'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'type', array(
			'type' => $__vars['type'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['shareUrl'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'share_url', array(
			'shareUrl' => $__vars['shareUrl'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['canonicalUrl'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'canonical_url', array(
			'canonicalUrl' => $__vars['canonicalUrl'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
	';
	if (!$__templater->test($__vars['imageUrl'], 'empty', array())) {
		$__finalCompiled .= '
		' . $__templater->callMacro(null, 'image_url', array(
			'imageUrl' => $__vars['imageUrl'],
			'twitterCard' => $__vars['twitterCard'],
		), $__vars) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'site_name' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'siteName' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('<meta property="og:site_name" content="' . $__templater->filter($__vars['siteName'], array(array('for_attr', array()),), true) . '" />');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_site_name',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'title' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'title' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('
		<meta property="og:title" content="' . $__templater->filter($__vars['title'], array(array('for_attr', array()),), true) . '" />
		<meta property="twitter:title" content="' . $__templater->filter($__templater->func('snippet', array($__vars['title'], 70, ), false), array(array('for_attr', array()),), true) . '" />
	');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_title',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'description' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'description' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('
		<meta name="description" content="' . $__templater->filter($__templater->func('snippet', array($__templater->filter($__vars['description'], array(array('strip_tags', array()),), false), 160, ), false), array(array('for_attr', array()),), true) . '" />
		<meta property="og:description" content="' . $__templater->filter($__templater->func('snippet', array($__templater->filter($__vars['description'], array(array('strip_tags', array()),), false), 300, ), false), array(array('for_attr', array()),), true) . '" />
		<meta property="twitter:description" content="' . $__templater->filter($__templater->func('snippet', array($__templater->filter($__vars['description'], array(array('strip_tags', array()),), false), 200, ), false), array(array('for_attr', array()),), true) . '" />
	');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_description',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'type' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('<meta property="og:type" content="' . $__templater->escape($__vars['type']) . '" />');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_type',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'share_url' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'shareUrl' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('<meta property="og:url" content="' . $__templater->escape($__vars['shareUrl']) . '" />');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_share_url',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'canonical_url' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'canonicalUrl' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('<link rel="canonical" href="' . $__templater->escape($__vars['canonicalUrl']) . '" />');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_canonical_url',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'image_url' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'imageUrl' => '!',
		'twitterCard' => 'summary',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['meta'] = $__templater->preEscaped('
		<meta property="og:image" content="' . $__templater->escape($__vars['imageUrl']) . '" />
		<meta property="twitter:image" content="' . $__templater->escape($__vars['imageUrl']) . '" />
		<meta property="twitter:card" content="' . $__templater->escape($__vars['twitterCard']) . '" />
	');
	$__finalCompiled .= '
	' . $__templater->callMacro(null, 'output', array(
		'option' => 'meta_image_url',
		'meta' => $__vars['meta'],
		'output' => $__vars['output'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'output' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'option' => '!',
		'meta' => '!',
		'output' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['output']) {
		$__finalCompiled .= $__templater->filter($__vars['meta'], array(array('raw', array()),), true);
	} else {
		$__templater->setPageParam('head.' . $__vars['option'], $__templater->preEscaped($__templater->filter($__vars['meta'], array(array('raw', array()),), true)));
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});