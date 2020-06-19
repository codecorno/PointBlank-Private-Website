<?php
// FROM HASH: b2f8272ca0a3f5d14228a3c8af828630
return array('macros' => array('head' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'app' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['cssUrls'] = array('public:normalize.css', 'public:core.less', $__vars['app'] . ':app.less', );
	$__finalCompiled .= '

	' . $__templater->includeTemplate('font_awesome_setup', $__vars) . '

	<link rel="stylesheet" href="' . $__templater->func('css_url', array($__vars['cssUrls'], ), true) . '" />

	<!--XF:CSS-->
	';
	if ($__vars['xf']['fullJs']) {
		$__finalCompiled .= '
		<script src="' . $__templater->func('js_url', array('xf/preamble.js', ), true) . '"></script>
	';
	} else {
		$__finalCompiled .= '
		<script src="' . $__templater->func('js_url', array('xf/preamble.min.js', ), true) . '"></script>
	';
	}
	$__finalCompiled .= '
	';
	if ($__templater->func('property', array('nlHeaderScriptInsert', ), false) != null) {
		$__finalCompiled .= '
		' . $__templater->func('property', array('nlHeaderScriptInsert', ), true) . '
	';
	}
	$__finalCompiled .= '
	';
	$__templater->includeCss('znl_loader.less');
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'body' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'app' => '!',
		'jsState' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->func('core_js') . '
	<!--XF:JS-->
	<script>
		jQuery.extend(true, XF.config, {
			// ' . '
			userId: ' . $__templater->escape($__vars['xf']['visitor']['user_id']) . ',
			enablePush: ' . ($__vars['xf']['options']['enablePush'] ? 'true' : 'false') . ',
			pushAppServerKey: \'' . $__templater->escape($__vars['xf']['options']['pushKeysVAPID']['publicKey']) . '\',
			url: {
				fullBase: \'' . $__templater->filter($__templater->func('base_url', array(null, true, ), false), array(array('escape', array('js', )),), true) . '\',
				basePath: \'' . $__templater->filter($__templater->func('base_url', array(null, false, ), false), array(array('escape', array('js', )),), true) . '\',
				css: \'' . $__templater->filter($__templater->func('css_url', array(array('__SENTINEL__', ), false, ), false), array(array('escape', array('js', )),), true) . '\',
				keepAlive: \'' . $__templater->filter($__templater->func('link_type', array($__vars['app'], 'login/keep-alive', ), false), array(array('escape', array('js', )),), true) . '\'
			},
			cookie: {
				path: \'' . $__templater->filter($__vars['xf']['cookie']['path'], array(array('escape', array('js', )),), true) . '\',
				domain: \'' . $__templater->filter($__vars['xf']['cookie']['domain'], array(array('escape', array('js', )),), true) . '\',
				prefix: \'' . $__templater->filter($__vars['xf']['cookie']['prefix'], array(array('escape', array('js', )),), true) . '\',
				secure: ' . ($__vars['xf']['cookie']['secure'] ? 'true' : 'false') . '
			},
			csrf: \'' . $__templater->filter($__templater->func('csrf_token', array(), false), array(array('escape', array('js', )),), true) . '\',
			js: {\'<!--XF:JS:JSON-->\'},
			css: {\'<!--XF:CSS:JSON-->\'},
			time: {
				now: ' . $__templater->escape($__vars['xf']['time']) . ',
				today: ' . $__templater->escape($__vars['xf']['timeDetails']['today']) . ',
				todayDow: ' . $__templater->escape($__vars['xf']['timeDetails']['todayDow']) . ',
				tomorrow: ' . $__templater->escape($__vars['xf']['timeDetails']['tomorrow']) . ',
				yesterday: ' . $__templater->escape($__vars['xf']['timeDetails']['yesterday']) . ',
				week: ' . $__templater->escape($__vars['xf']['timeDetails']['week']) . '
			},
			borderSizeFeature: \'' . $__templater->func('property', array('borderSizeFeature', ), true) . '\',
			fontAwesomeWeight: \'' . $__templater->func('fa_weight', array(), true) . '\',
			enableRtnProtect: ' . ($__vars['xf']['enableRtnProtect'] ? 'true' : 'false') . ',
			enableFormSubmitSticky: ' . ($__templater->func('property', array('formSubmitSticky', ), false) ? 'true' : 'false') . ',
			uploadMaxFilesize: ' . $__templater->escape($__vars['xf']['uploadMaxFilesize']) . ',
			allowedVideoExtensions: ' . $__templater->filter($__vars['xf']['allowedVideoExtensions'], array(array('json', array()),array('raw', array()),), true) . ',
			shortcodeToEmoji: ' . ($__vars['xf']['options']['shortcodeToEmoji'] ? 'true' : 'false') . ',
			visitorCounts: {
				conversations_unread: \'' . $__templater->filter($__vars['xf']['visitor']['conversations_unread'], array(array('number', array()),), true) . '\',
				alerts_unread: \'' . $__templater->filter($__vars['xf']['visitor']['alerts_unread'], array(array('number', array()),), true) . '\',
				total_unread: \'' . $__templater->filter($__vars['xf']['visitor']['conversations_unread'] + $__vars['xf']['visitor']['alerts_unread'], array(array('number', array()),), true) . '\',
				title_count: ' . ($__templater->func('in_array', array($__vars['xf']['options']['displayVisitorCount'], array('title_count', 'title_and_icon', ), ), false) ? 'true' : 'false') . ',
				icon_indicator: ' . ($__templater->func('in_array', array($__vars['xf']['options']['displayVisitorCount'], array('icon_indicator', 'title_and_icon', ), ), false) ? 'true' : 'false') . '
			},
			jsState: ' . ($__vars['jsState'] ? $__templater->filter($__vars['jsState'], array(array('json', array()),array('raw', array()),), true) : '{}') . ',
			publicMetadataLogoUrl: \'' . ($__templater->func('property', array('publicMetadataLogoUrl', ), false) ? $__templater->func('base_url', array($__templater->func('property', array('publicMetadataLogoUrl', ), false), true, ), true) : '') . '\',
			publicPushBadgeUrl: \'' . ($__templater->func('property', array('publicPushBadgeUrl', ), false) ? $__templater->func('base_url', array($__templater->func('property', array('publicPushBadgeUrl', ), false), true, ), true) : '') . '\'
		});

		jQuery.extend(XF.phrases, {
			// ' . '
			date_x_at_time_y: "' . $__templater->filter('{date} at {time}', array(array('escape', array('js', )),), true) . '",
			day_x_at_time_y:  "' . $__templater->filter('{day} às {time}', array(array('escape', array('js', )),), true) . '",
			yesterday_at_x:   "' . $__templater->filter('Ontem às {time}', array(array('escape', array('js', )),), true) . '",
			x_minutes_ago:    "' . $__templater->filter('{minutes} minutos atrás', array(array('escape', array('js', )),), true) . '",
			one_minute_ago:   "' . $__templater->filter('1 minuto atrás', array(array('escape', array('js', )),), true) . '",
			a_moment_ago:     "' . $__templater->filter('Um momento atrás', array(array('escape', array('js', )),), true) . '",
			today_at_x:       "' . $__templater->filter('Hoje às {time}', array(array('escape', array('js', )),), true) . '",
			in_a_moment:      "' . $__templater->filter('In a moment', array(array('escape', array('js', )),), true) . '",
			in_a_minute:      "' . $__templater->filter('In a minute', array(array('escape', array('js', )),), true) . '",
			in_x_minutes:     "' . $__templater->filter('In {minutes} minutes', array(array('escape', array('js', )),), true) . '",
			later_today_at_x: "' . $__templater->filter('Later today at {time}', array(array('escape', array('js', )),), true) . '",
			tomorrow_at_x:    "' . $__templater->filter('Tomorrow at {time}', array(array('escape', array('js', )),), true) . '",

			day0: "' . $__templater->filter('Domingo', array(array('escape', array('js', )),), true) . '",
			day1: "' . $__templater->filter('Segunda-feira', array(array('escape', array('js', )),), true) . '",
			day2: "' . $__templater->filter('Terça-feira', array(array('escape', array('js', )),), true) . '",
			day3: "' . $__templater->filter('Quarta-feira', array(array('escape', array('js', )),), true) . '",
			day4: "' . $__templater->filter('Quinta-feira', array(array('escape', array('js', )),), true) . '",
			day5: "' . $__templater->filter('Sexta-feira', array(array('escape', array('js', )),), true) . '",
			day6: "' . $__templater->filter('Sábado', array(array('escape', array('js', )),), true) . '",

			dayShort0: "' . $__templater->filter('Dom', array(array('escape', array('js', )),), true) . '",
			dayShort1: "' . $__templater->filter('Seg', array(array('escape', array('js', )),), true) . '",
			dayShort2: "' . $__templater->filter('Ter', array(array('escape', array('js', )),), true) . '",
			dayShort3: "' . $__templater->filter('Qua', array(array('escape', array('js', )),), true) . '",
			dayShort4: "' . $__templater->filter('Qui', array(array('escape', array('js', )),), true) . '",
			dayShort5: "' . $__templater->filter('Sex', array(array('escape', array('js', )),), true) . '",
			dayShort6: "' . $__templater->filter('Sab', array(array('escape', array('js', )),), true) . '",

			month0: "' . $__templater->filter('Janeiro', array(array('escape', array('js', )),), true) . '",
			month1: "' . $__templater->filter('Fevereiro', array(array('escape', array('js', )),), true) . '",
			month2: "' . $__templater->filter('Março', array(array('escape', array('js', )),), true) . '",
			month3: "' . $__templater->filter('Abril', array(array('escape', array('js', )),), true) . '",
			month4: "' . $__templater->filter('Maio', array(array('escape', array('js', )),), true) . '",
			month5: "' . $__templater->filter('Junho', array(array('escape', array('js', )),), true) . '",
			month6: "' . $__templater->filter('Julho', array(array('escape', array('js', )),), true) . '",
			month7: "' . $__templater->filter('Agosto', array(array('escape', array('js', )),), true) . '",
			month8: "' . $__templater->filter('Setembro', array(array('escape', array('js', )),), true) . '",
			month9: "' . $__templater->filter('Outubro', array(array('escape', array('js', )),), true) . '",
			month10: "' . $__templater->filter('Novembro', array(array('escape', array('js', )),), true) . '",
			month11: "' . $__templater->filter('Dezembro', array(array('escape', array('js', )),), true) . '",

			active_user_changed_reload_page: "' . $__templater->filter('The active user has changed. Reload the page for the latest version.', array(array('escape', array('js', )),), true) . '",
			server_did_not_respond_in_time_try_again: "' . $__templater->filter('The server did not respond in time. Please try again.', array(array('escape', array('js', )),), true) . '",
			oops_we_ran_into_some_problems: "' . $__templater->filter('Ops! Nós encontramos alguns problemas.', array(array('escape', array('js', )),), true) . '",
			oops_we_ran_into_some_problems_more_details_console: "' . $__templater->filter('Ops! Nós encontramos alguns problemas. Por favor, tente novamente mais tarde. Mais detalhes de erro podem estar no console do navegador.', array(array('escape', array('js', )),), true) . '",
			file_too_large_to_upload: "' . $__templater->filter('The file is too large to be uploaded.', array(array('escape', array('js', )),), true) . '",
			uploaded_file_is_too_large_for_server_to_process: "' . $__templater->filter('The uploaded file is too large for the server to process.', array(array('escape', array('js', )),), true) . '",
			files_being_uploaded_are_you_sure: "' . $__templater->filter('Files are still being uploaded. Are you sure you want to submit this form?', array(array('escape', array('js', )),), true) . '",
			attach: "' . $__templater->filter('Anexar arquivos', array(array('escape', array('js', )),), true) . '",
			rich_text_box: "' . $__templater->filter('Caixa de texto rica', array(array('escape', array('js', )),), true) . '",
			close: "' . $__templater->filter('Fechar', array(array('escape', array('js', )),), true) . '",
			link_copied_to_clipboard: "' . $__templater->filter('Link copied to clipboard.', array(array('escape', array('js', )),), true) . '",
			text_copied_to_clipboard: "' . $__templater->filter('Text copied to clipboard.', array(array('escape', array('js', )),), true) . '",
			loading: "' . $__templater->filter('Carregando' . $__vars['xf']['language']['ellipsis'], array(array('escape', array('js', )),), true) . '",

			processing: "' . $__templater->filter('Processando', array(array('escape', array('js', )),), true) . '",
			\'processing...\': "' . $__templater->filter('Processando' . $__vars['xf']['language']['ellipsis'], array(array('escape', array('js', )),), true) . '",

			showing_x_of_y_items: "' . $__templater->filter('Mostrando {count} de {total} itens', array(array('escape', array('js', )),), true) . '",
			showing_all_items: "' . $__templater->filter('Mostrando todos os itens', array(array('escape', array('js', )),), true) . '",
			no_items_to_display: "' . $__templater->filter('No items to display', array(array('escape', array('js', )),), true) . '",

			push_enable_notification_title: "' . $__templater->filter('Push notifications enabled successfully at ' . $__vars['xf']['options']['boardTitle'] . '', array(array('escape', array('js', )),), true) . '",
			push_enable_notification_body: "' . $__templater->filter('Thank you for enabling push notifications!', array(array('escape', array('js', )),), true) . '"
		});
	</script>

	<form style="display:none" hidden="hidden">
		<input type="text" name="_xfClientLoadTime" value="" id="_xfClientLoadTime" title="_xfClientLoadTime" tabindex="-1" />
	</form>

	';
	if ($__templater->method($__vars['xf']['visitor'], 'canSearch', array()) AND ($__templater->method($__vars['xf']['request'], 'getFullRequestUri', array()) === $__templater->func('link', array('full:index', ), false))) {
		$__finalCompiled .= '
		<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "WebSite",
			"url": "' . $__templater->filter($__templater->func('link', array('canonical:index', ), false), array(array('escape', array('js', )),), true) . '",
			"potentialAction": {
				"@type": "SearchAction",
				"target": "' . (($__templater->filter($__templater->func('link', array('canonical:search/search', ), false), array(array('escape', array('js', )),), true) . ($__vars['xf']['options']['useFriendlyUrls'] ? '?' : '&')) . 'keywords={search_keywords}') . '",
				"query-input": "required name=search_keywords"
			}
		}
		</script>
	';
	}
	$__finalCompiled .= '
	' . $__templater->includeTemplate('nl_functions_js', $__vars) . '
	';
	if ($__templater->func('property', array('nlFooterScriptInsert', ), false) != null) {
		$__finalCompiled .= '
		' . $__templater->func('property', array('nlFooterScriptInsert', ), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});