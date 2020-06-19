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
			<dt>' . 'Exemplo' . $__vars['xf']['language']['label_separator'] . '</dt>
			<dd>' . $__templater->filter($__vars['bbCode'], array(array('nl2br', array()),), true) . '</dd>
		</dl>
		<dl class="bbCodeDemoBlock-item">
			<dt>' . 'Saída' . $__vars['xf']['language']['label_separator'] . '</dt>
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
		'title' => $__templater->filter('[B], [I], [U], [S] - Negrito, itálico, sublinhado e riscado', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Torna o texto encadernado em negrito, itálico, sublinhado ou riscado.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Este é o texto [B]negrito[/B].
Este é o texto [I]itálico[/I].
Este é o texto [U]sublinhado[/U].
Este é o texto [S]riscado[/S].', array(array('preEscaped', array()),), false),
		'anchor' => 'basic',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[COLOR=<span class="block-textHeader-highlight">color</span>], [FONT=<span class="block-textHeader-highlight">name</span>], [SIZE=<span class="block-textHeader-highlight">size</span>] - Cor do texto, Fonte e Tamanho', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Altera a cor, a fonte ou o tamanho do texto encapsulado.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Este é o texto [COLOR=red]vermelho[/COLOR] e [COLOR=#0000cc]azul[/COLOR].
Este é o texto [FONT=Courier New]Courier New[/FONT].
Este é o texto [SIZE=1]pequeno[/SIZE] e [SIZE=7]grande[/SIZE].', array(array('preEscaped', array()),), false),
		'anchor' => 'style',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[URL], [EMAIL] - Vinculando', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Cria um link usando o texto encapsulado como o destino.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[URL]http://www.exemplo.com.br[/URL]
[EMAIL]exemplo@exemplo.com.br[/EMAIL]', array(array('preEscaped', array()),), false),
		'anchor' => 'email-url',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[URL=<span class="block-textHeader-highlight">link</span>], [EMAIL=<span class="block-textHeader-highlight">endereço</span>] - Vinculando (Avançado)', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Vincula o texto encadernado à página da Web especificada ou ao endereço de e-mail.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[URL=http://www.exemplo.com.br]Ir para exemplo.com.br[/URL]
[EMAIL=exemplo@exemplo.com.br]Me mande um e-mail
[/EMAIL]', array(array('preEscaped', array()),), false),
		'anchor' => 'email-url-advanced',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[USER=<span class="block-textHeader-highlight">ID</span>] - Vinculando Perfil', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Links para o perfil de um usuário. Isso geralmente é inserido automaticamente ao mencionar um usuário.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[USER=' . ($__vars['xf']['visitor']['user_id'] ? $__vars['xf']['visitor']['user_id'] : '1') . ']' . ($__vars['xf']['visitor']['user_id'] ? $__vars['xf']['visitor']['username'] : 'Nome de usuário') . '[/USER]', array(array('preEscaped', array()),), false),
		'anchor' => 'user-mention',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[IMG] - Imagem', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Exibe uma imagem, usando o texto encapsulado como o URL.', array(array('preEscaped', array()),), false),
		'example' => '[IMG]' . $__templater->func('base_url', array(($__templater->func('property', array('publicMetadataLogoUrl', ), false) ?: $__templater->func('property', array('publicLogoUrl', ), false)), true, ), false) . '[/IMG]',
		'anchor' => 'image',
	), $__vars) . '

			<li class="bbCodeHelpItem block-row block-row--separated">
				<span class="u-anchorTarget" id="media"></span>
				<h3 class="block-textHeader">' . '[MEDIA=<span class="block-textHeader-highlight">site</span>] - Mídia incorporada' . '</h3>
				<div>
					' . 'Mídia incorporada de sites aprovados em sua mensagem. Recomenda-se que você use o botão de mídia na barra de ferramentas do editor.' . '<br />
					' . 'Sites aprovados' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->func('media_sites', array(), true) . '
				</div>
				<div class="bbCodeDemoBlock">
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Exemplo' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd>[MEDIA=youtube]oHg5SJYRHA0[/MEDIA]</dd>
					</dl>
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Saída' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd><i>' . 'An embedded YouTube player would appear here.' . '</i></dd>
					</dl>
				</div>
			</li>

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[LIST] - Listas', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Exibe uma lista com marcadores ou numerada.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[LIST]
[*]Ponto 1
[*]Ponto 2
[/LIST]
[LIST=1]
[*]Entrada 1
[*]Entrada 2
[/LIST]', array(array('preEscaped', array()),), false),
		'anchor' => 'list',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[LEFT], [CENTER], [RIGHT] - Alinhamento de texto', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Altera o alinhamento do texto encapsulado.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[LEFT]Alinhado à esquerda[/LEFT]
[CENTER]Centralizado[/CENTER]
[RIGHT]Alinhado à direita[/RIGHT]', array(array('preEscaped', array()),), false),
		'anchor' => 'align',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[QUOTE] - Texto citado', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Exibe o texto que foi citado de outra fonte. Você também pode atribuir o nome da fonte.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[QUOTE]Texto citado[/QUOTE]
[QUOTE=Uma pessoa]Algo que foi dito[/QUOTE]', array(array('preEscaped', array()),), false),
		'anchor' => 'quote',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[SPOILER] - Texto contendo spoilers', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Esconde texto que pode conter spoilers para que ele deve ser clicado pelo visualizador para ser visto.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[SPOILER]Spoiler simples[/SPOILER]
[SPOILER=Título do Spoiler]Spoiler com um título[/SPOILER]', array(array('preEscaped', array()),), false),
		'anchor' => 'spoiler',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[ISPOILER] - Inline text containing spoilers', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Allows you to display text inline among normal content which hides text that may contain spoilers and must be clicked by the viewer to be seen.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('You have to click the following [ISPOILER]word[/ISPOILER] to see the content.', array(array('preEscaped', array()),), false),
		'anchor' => 'ispoiler',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[CODE] - Exibição de código de programação', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Exibe texto em uma das várias linguagens de programação, destacando a sintaxe sempre que possível.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Código geral:
[CODE]Código
geral[/CODE]

Código Rich:
[CODE=rich][COLOR=red]Rich[/COLOR]
código[/CODE]

Código PHP:
[CODE=php]echo $ola . \' mundo\';[/CODE]

Código JS:
[CODE=js]var ola = \'mundo\';[/CODE]', array(array('preEscaped', array()),), false),
		'anchor' => 'code',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[ICODE] - Exibição de códigos de programação em linha', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Permite que você exiba o código inline entre o conteúdo normal do post. A sintaxe não será realçada.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Os blocos de código em linha [ICODE] são uma maneira conveniente [/ICODE] de exibir código inline.', array(array('preEscaped', array()),), false),
		'anchor' => 'icode',
	), $__vars) . '

			' . $__templater->callMacro(null, 'row_output', array(
		'title' => $__templater->filter('[INDENT] - Recuo de texto', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Recua o texto envolvido. Isso pode ser aninhado para recuos maiores.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('Texto regular
[INDENT]Texto recuado
[INDENT]Mais recuado[/INDENT]
[/INDENT]', array(array('preEscaped', array()),), false),
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
		'title' => $__templater->filter('[PLAIN] - Texto simples', array(array('preEscaped', array()),), false),
		'desc' => $__templater->filter('Desativa a tradução de BBcode no texto encapsulado.', array(array('preEscaped', array()),), false),
		'example' => $__templater->filter('[PLAIN]Este não é [B]texto em negrito[/B].[/PLAIN]', array(array('preEscaped', array()),), false),
		'anchor' => 'plain',
	), $__vars) . '

			<li class="bbCodeHelpItem block-row block-row--separated">
				<span class="u-anchorTarget" id="attach"></span>
				<h3 class="block-textHeader">' . '[ATTACH] - Inserção de anexos' . '</h3>
				<div>' . 'Insere um anexo no ponto especificado. Se o anexo for uma imagem, será inserida uma miniatura ou uma versão em tamanho normal. Isso geralmente será inserido clicando no botão apropriado.' . '</div>
				<div class="bbCodeDemoBlock">
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Exemplo' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd>
							' . 'Miniatura' . $__vars['xf']['language']['label_separator'] . ' [ATTACH]123[/ATTACH]<br />
							' . 'Tamanho completo' . $__vars['xf']['language']['label_separator'] . ' [ATTACH=full]123[/ATTACH]
						</dd>
					</dl>
					<dl class="bbCodeDemoBlock-item">
						<dt>' . 'Saída' . $__vars['xf']['language']['label_separator'] . '</dt>
						<dd><i>' . 'O conteúdo dos anexos apareceria aqui.' . '</i></dd>
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
							<dt>' . 'Exemplo' . $__vars['xf']['language']['label_separator'] . '</dt>
							<dd>' . $__templater->filter($__vars['bbCode']['example'], array(array('nl2br', array()),), true) . '</dd>
						</dl>
						<dl class="bbCodeDemoBlock-item">
							<dt>' . 'Saída' . $__vars['xf']['language']['label_separator'] . '</dt>
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