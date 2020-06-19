<?php
// FROM HASH: fba938698068c260c388c5f6cd1f6bff
return array('macros' => array('row_output' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'title' => '!',
		'desc' => '!',
		'example' => '!',
		'anchor' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<li class="bbCodeHelpItem block-row block-row--separated">
		<span class="u-anchorTarget" id="' . $__templater->escape($__vars['anchor']) . '"></span>
		<h3 class="block-textHeader">' . $__templater->escape($__vars['title']) . '</h3>
		<div>' . $__templater->escape($__vars['desc']) . '</div>
		' . $__templater->callMacro(null, 'example_output', array(
		'bbCode' => $__vars['example'],
	), $__vars) . '
	</li>
';
	return $__finalCompiled;
},
'example_output' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'bbCode' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="bbCodeDemoBlock">
		<dl class="bbCodeDemoBlock-item">
			<dt>' . 'Example' . $__vars['xf']['language']['label_separator'] . '</dt>
			<dd>' . $__templater->filter($__vars['bbCode'], array(array('nl2br', array()),), true) . '</dd>
		</dl>
		<dl class="bbCodeDemoBlock-item">
			<dt>' . 'Output' . $__vars['xf']['language']['label_separator'] . '</dt>
			<dd>' . $__templater->func('bb_code', array($__vars['bbCode'], 'help', null, ), true) . '</dd>
		</dl>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('help_bb_codes.less');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<ul class="listPlain block-body">

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[B], [I], [U], [S] - Bold, italics, underline, and strike-through', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Makes the wrapped text bold, italic, underlined, or struck-through.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('This is [B]bold[/B] text.
This is [I]italic[/I] text.
This is [U]underlined[/U] text.
This is [S]struck-through[/S] text.', array(array('preEscaped', array()),), false),
		'anchor' => 'basic',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[COLOR=<span class="block-textHeader-highlight">color</span>], [FONT=<span class="block-textHeader-highlight">name</span>], [SIZE=<span class="block-textHeader-highlight">size</span>] - Text Color, Font, and Size', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Changes the color, font, or size of the wrapped text.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('This is [COLOR=red]red[/COLOR] and [COLOR=#0000cc]blue[/COLOR] text.
This is [FONT=Courier New]Courier New[/FONT] text.
This is [SIZE=1]small[/SIZE] and [SIZE=7]big[/SIZE] text.', array(array('preEscaped', array()),), false),
		'anchor' => 'style',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[URL], [EMAIL] - Linking', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Creates a link using the wrapped text as the target.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[URL]https://www.example.com[/URL]
[EMAIL]example@example.com[/EMAIL]', array(array('preEscaped', array()),), false),
		'anchor' => 'email-url',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[URL=<span class="block-textHeader-highlight">link</span>], [EMAIL=<span class="block-textHeader-highlight">address</span>] - Linking (Advanced)', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Links the wrapped text to the specified web page or email address.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[URL=https://www.example.com]Go to example.com[/URL]
[EMAIL=example@example.com]Email me[/EMAIL]', array(array('preEscaped', array()),), false),
		'anchor' => 'email-url-advanced',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[USER=<span class="block-textHeader-highlight">ID</span>] - Profile Linking', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Links to a user\'s profile. This is generally inserted automatically when mentioning a user.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[USER=' . ($__vars['xf']['visitor']['user_id'] ? $__vars['xf']['visitor']['user_id'] : '1') . ']' . ($__vars['xf']['visitor']['user_id'] ? $__vars['xf']['visitor']['username'] : 'User name') . '[/USER]', array(array('preEscaped', array()),), false),
		'anchor' => 'user-mention',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[IMG] - Image', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Display an image, using the wrapped text as the URL.', array(array('preEscaped', array()),), false),
		'example' => '[IMG]' . $__templater->func('base_url', array(($__templater->func('property', array('publicMetadataLogoUrl', ), false) ?: $__templater->func('property', array('publicLogoUrl', ), false)), true, ), false) . '[/IMG]',
		'anchor' => 'image',
	), $__vars) . '

			<li class="bbCodeHelpItem block-row block-row--separated">
				<span class="u-anchorTarget" id="media"></span>
				<h3 class="block-textHeader">' . '[MEDIA=<span class="block-textHeader-highlight">site</span>] - Embedded Media' . '</h3>
				<div>
					' . 'Embeds media from approved sites into your message. It is recommended that you use the media button in the editor tool bar.' . '<br />
					' . 'Approved sites' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('media_sites', array(), true) . '
				</div>
				<div class="bbCodeDemoBlock">
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Example' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd>[MEDIA=youtube]oHg5SJYRHA0[/MEDIA]</dd>
					</dl>
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Output' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd><i>' . 'An embedded YouTube player would appear here.' . '</i></dd>
					</dl>
				</div>
			</li>

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[LIST] - Lists', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Displays a bulleted or numbered list.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[LIST]
[*]Bullet 1
[*]Bullet 2
[/LIST]
[LIST=1]
[*]Entry 1
[*]Entry 2
[/LIST]', array(array('preEscaped', array()),), false),
		'anchor' => 'list',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[LEFT], [CENTER], [RIGHT] - Text alignment', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Changes the alignment of the wrapped text.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[LEFT]Left-aligned[/LEFT]
[CENTER]Center-aligned[/CENTER]
[RIGHT]Right-aligned[/RIGHT]', array(array('preEscaped', array()),), false),
		'anchor' => 'align',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[QUOTE] - Quoted text', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Displays text that has been quoted from another source. You may also attribute the name of the source.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[QUOTE]Quoted text[/QUOTE]
[QUOTE=A person]Something they said[/QUOTE]', array(array('preEscaped', array()),), false),
		'anchor' => 'quote',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[SPOILER] - Text containing spoilers', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Hides text that may contain spoilers so that it must be clicked by the viewer to be seen.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[SPOILER]Simple spoiler[/SPOILER]
[SPOILER=Spoiler Title]Spoiler with a title[/SPOILER]', array(array('preEscaped', array()),), false),
		'anchor' => 'spoiler',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[ISPOILER] - Inline text containing spoilers', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Allows you to display text inline among normal content which hides text that may contain spoilers and must be clicked by the viewer to be seen.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('You have to click the following [ISPOILER]word[/ISPOILER] to see the content.', array(array('preEscaped', array()),), false),
		'anchor' => 'ispoiler',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[CODE] - Programming code display', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Displays text in one of several programming languages, highlighting the syntax where possible.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('General code:
[CODE]General
code[/CODE]

Rich code:
[CODE=rich][COLOR=red]Rich[/COLOR]
code[/CODE]

PHP code:
[CODE=php]echo $hello . \' world\';[/CODE]

JS code:
[CODE=javascript]var hello = \'world\';[/CODE]', array(array('preEscaped', array()),), false),
		'anchor' => 'code',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[ICODE] - Inline programming code display', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Allows you to display code inline among normal post content. Syntax will not be highlighted.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Inline code blocks [ICODE]are a convenient way[/ICODE] of displaying code inline.', array(array('preEscaped', array()),), false),
		'anchor' => 'icode',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[INDENT] - Text indent', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Indents the wrapped text. This can be nested for larger indentings.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Regular text
[INDENT]Indented text[/INDENT]
[INDENT=2]More indented[/INDENT]', array(array('preEscaped', array()),), false),
		'anchor' => 'indent',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[TABLE] - Tables', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Special markup to display tables in your content.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[TABLE]
[TR]
[TH]Header 1[/TH]
[TH]Header 2[/TH]
[/TR]
[TR]
[TD]Content 1[/TD]
[TD]Content 2[/TD]
[/TR]
[/TABLE]', array(array('preEscaped', array()),), false),
		'anchor' => 'table',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[PLAIN] - Plain text', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Disables BB code translation on the wrapped text.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[PLAIN]This is not [B]bold[/B] text.[/PLAIN]', array(array('preEscaped', array()),), false),
		'anchor' => 'plain',
	), $__vars) . '

			<li class="bbCodeHelpItem block-row block-row--separated">
				<span class="u-anchorTarget" id="attach"></span>
				<h3 class="block-textHeader">' . '[ATTACH] - Attachment insertion' . '</h3>
				<div>' . 'Inserts an attachment at the specified point. If the attachment is an image, a thumbnail or full size version will be inserted. This will generally be inserted by clicking the appropriate button.' . '</div>
				<div class="bbCodeDemoBlock">
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Example' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd>
							' . 'Thumbnail' . $__vars['xf']['language']['label_separator'] . ' [ATTACH]123[/ATTACH]<br />
							' . 'Full size' . $__vars['xf']['language']['label_separator'] . ' [ATTACH=full]123[/ATTACH]
						</dd>
					</dl>
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Output' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd><i>' . 'The contents of the attachments would appear here.' . '</i></dd>
					</dl>
				</div>
			</li>

			';
	if ($__templater->isTraversable($__vars['bbCodes'])) {
		foreach ($__vars['bbCodes'] AS $__vars['bbCode']) {
			if (!$__templater->test($__vars['bbCode']['example'], 'empty', array())) {
				$__finalCompiled .= '
				<li class="bbCodeHelpItem block-row block-row--separated">
					<span class="u-anchorTarget" id="' . $__templater->escape($__vars['bbCode']['bb_code_id']) . '"></span>
					<h3 class="block-textHeader">
						';
				if (($__vars['bbCode']['has_option'] == 'no') OR ($__vars['bbCode']['has_option'] == 'optional')) {
					$__finalCompiled .= '[' . $__templater->filter($__vars['bbCode']['bb_code_id'], array(array('to_upper', array()),), true) . ']';
				}
				$__finalCompiled .= '
						';
				if ($__vars['bbCode']['has_option'] == 'optional') {
					$__finalCompiled .= '<span role="presentation" aria-hidden="true">&middot;</span>';
				}
				$__finalCompiled .= '
						';
				if (($__vars['bbCode']['has_option'] == 'yes') OR ($__vars['bbCode']['has_option'] == 'optional')) {
					$__finalCompiled .= '[' . $__templater->filter($__vars['bbCode']['bb_code_id'], array(array('to_upper', array()),), true) . '=<span class="block-textHeader-highlight">option</span>]';
				}
				$__finalCompiled .= '
						- ' . $__templater->escape($__vars['bbCode']['title']) . '
					</h3>
					';
				$__compilerTemp1 = '';
				$__compilerTemp1 .= $__templater->escape($__vars['bbCode']['description']);
				if (strlen(trim($__compilerTemp1)) > 0) {
					$__finalCompiled .= '
						<div>' . $__compilerTemp1 . '</div>
					';
				}
				$__finalCompiled .= '
					<div class="bbCodeDemoBlock">
						<dl class="bbCodeDemoBlock-item">
							<dt>' . 'Example' . $__vars['xf']['language']['label_separator'] . '</dt>
							<dd>' . $__templater->filter($__vars['bbCode']['example'], array(array('nl2br', array()),), true) . '</dd>
						</dl>
						<dl class="bbCodeDemoBlock-item">
							<dt>' . 'Output' . $__vars['xf']['language']['label_separator'] . '</dt>
							<dd>' . (!$__templater->test($__vars['bbCode']['output'], 'empty', array()) ? $__templater->escape($__vars['bbCode']['output']) : $__templater->func('bb_code', array($__vars['bbCode']['example'], 'help', null, ), true)) . '</dd>
						</dl>
					</div>
				</li>
			';
			}
		}
	}
	$__finalCompiled .= '

		</ul>
	</div>
</div>

' . '

';
	return $__finalCompiled;
});