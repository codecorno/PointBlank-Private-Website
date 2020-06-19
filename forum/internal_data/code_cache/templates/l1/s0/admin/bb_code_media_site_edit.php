<?php
// FROM HASH: a9ec2f3361ff296027c826f521f41306
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['site'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add BB code media site');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit BB code media site' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['site']['site_title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['site'], 'isUpdate', array()) AND $__templater->method($__vars['site'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('bb-code-media-sites/delete', $__vars['site'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if (!$__templater->method($__vars['site'], 'canEdit', array())) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['site'], 'canEdit', array())) {
		$__compilerTemp1 .= '
						' . 'You are currently editing the "Master" version of this template. Changes may not be reflected in modified child styles.' . '
					';
	} else {
		$__compilerTemp1 .= '
						' . 'You are unable to modify the "Master" version of this template. To make changes, you should edit the relevant template in each style.' . '
					';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'media_site_id',
		'value' => $__vars['site']['media_site_id'],
		'readonly' => ($__templater->method($__vars['site'], 'exists', array()) ? true : false),
		'maxlength' => $__templater->func('max_length', array($__vars['site'], 'media_site_id', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Media site ID',
		'explain' => 'This will be used to identify the media site to load in the BB code. It cannot be changed once set.',
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['site']['addon_id'],
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'site_title',
		'value' => $__vars['site']['site_title'],
		'maxlength' => $__templater->func('max_length', array($__vars['site'], 'site_title', ), false),
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
	), array(
		'label' => 'Site title',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'site_url',
		'value' => $__vars['site']['site_url'],
		'type' => 'url',
		'maxlength' => $__templater->func('max_length', array($__vars['site'], 'site_url', ), false),
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'Site URL',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'supported',
		'value' => '1',
		'selected' => $__vars['site']['supported'],
		'label' => '
					' . 'Show this site on the list of \'supported\' sites shown to visitors' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['site']['active'],
		'hint' => (($__vars['xf']['development'] AND $__vars['site']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Enabled' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextAreaRow(array(
		'name' => 'match_urls',
		'value' => $__vars['site']['match_urls'],
		'autosize' => 'true',
		'code' => 'true',
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'Match URLs',
		'explain' => 'You may use * as a wildcard, and {$id} to point to the media\'s ID. This ID will then be referenced in the embed code. Put each URL on a separate line.',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'embed_html',
		'value' => ($__templater->method($__vars['site'], 'exists', array()) ? $__vars['site']['MasterTemplate']['template'] : ''),
		'mode' => 'html',
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
		'class' => 'codeEditor--short',
	), array(
		'hint' => ($__templater->method($__vars['site'], 'isUpdate', array()) ? $__templater->escape($__templater->method($__vars['site'], 'getTemplateName', array())) : ''),
		'label' => 'Embed template',
		'explain' => '
					' . 'Enter {$id} where you want the user-entered media ID to be used.' . '<br />
					<br />
					' . $__compilerTemp1 . '
				',
	)) . '
		</div>

		<h3 class="block-formSectionHeader">
			<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
				<span class="block-formSectionHeader-aligner">' . 'oEmbed options' . '</span>
			</span>
		</h3>
		<div class="block-body block-body--collapsible">
			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
	), array(array(
		'name' => 'oembed_enabled',
		'selected' => $__vars['site']['oembed_enabled'],
		'label' => 'Use oEmbed',
		'_type' => 'option',
	)), array(
		'explain' => 'oEmbed is an open format for allowing representation of a URL on third party sites. If a media source provides an oEmbed option, you can fetch rich information about a requested resource and render in-depth HTML with it.<br />
<br />
Note: when enabling oEmbed, any HTML entered in the \'Embed template\' field is ignored, as are call-backs defined in \'Embed HTML callback\'. Additionally, XenForo handles only JSON responses from oEmbed sources.<br />
<br />
<a href="https://oembed.com" target="_blank">More information and examples at oEmbed.com</a>',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'oembed_api_endpoint',
		'value' => $__vars['site']['oembed_api_endpoint'],
		'type' => 'url',
		'maxlength' => $__templater->func('max_length', array($__vars['site'], 'oembed_api_endpoint', ), false),
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'oEmbed API endpoint',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'oembed_url_scheme',
		'value' => $__vars['site']['oembed_url_scheme'],
		'type' => 'url',
		'maxlength' => $__templater->func('max_length', array($__vars['site'], 'oembed_url_scheme', ), false),
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'oEmbed URL scheme',
		'explain' => 'Enter a URL that can be requested through oEmbed, including {$id} to represent the matched media ID',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'oembed_retain_scripts',
		'selected' => $__vars['site']['oembed_retain_scripts'],
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
		'label' => 'Retain javascript in returned oEmbed HTML',
		'_type' => 'option',
	)), array(
		'explain' => 'Selecting this option will leave any &lt;script&gt; tags in the returned oEmbed HTML in place. If you deselect this option, any Javascript initialization required by the returned HTML will need to be handled with your own methods.',
	)) . '
		</div>

		<h3 class="block-formSectionHeader">
			<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
				<span class="block-formSectionHeader-aligner">' . 'Advanced options' . '</span>
			</span>
		</h3>
		<div class="block-body block-body--collapsible">
			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
	), array(array(
		'name' => 'match_is_regex',
		'selected' => $__vars['site']['match_is_regex'],
		'label' => 'Use \'Match URLs\' as PCRE regular expressions',
		'_type' => 'option',
	)), array(
		'label' => 'Regular expression matching',
		'explain' => 'You may select this option and enter one or more full regular expressions instead of match URLs. The media ID must be identified using a named sub-pattern called \'id\'.<br />
<br />
Example regular expression with \'id\' named sub-pattern:<br />
<code>#https://www\\.example\\.com/video/(?P&lt;id&gt;\\d+/[a-z0-9_]+)/#siU</code>',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRow('
				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'namePrefix' => 'match_callback',
		'data' => $__vars['site'],
		'readOnly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'URL match callback',
		'explain' => 'Some sites require additional work to be performed on their page URLs in order to extract a media ID that can be used in embedded HTML. This callback can be used to perform that work.<br />
<br />
Your callback should return the unique ID of the media being requested, and have a signature as follows:<br />
<br />
<code>public static function matchCallback($url, $matchedId, \\XF\\Entity\\BbCodeMediaSite $site, $siteId)</code>',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRow('
				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'namePrefix' => 'embed_html_callback',
		'data' => $__vars['site'],
		'readOnly' => (!$__templater->method($__vars['site'], 'canEdit', array())),
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'Embed HTML callback',
		'explain' => 'This optional callback may be used to completely override the standard embedHTML output method, allowing more advanced manipulation.<br />
<br />
Your callback should return an HTML string, and have a signature as follows:<br />
<br />
<code>public static function myCallback($mediaKey, array $site, $siteId)</code>',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
		'html' => '
				' . $__templater->button('Save and exit', array(
		'type' => 'submit',
		'icon' => 'save',
		'name' => 'exit',
	), '', array(
	)) . '
			',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('bb-code-media-sites/save', $__vars['site'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});