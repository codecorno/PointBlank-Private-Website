<?php
// FROM HASH: 9990829ec922485425685e54453df444
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['forum'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar fórum');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Editar fórum' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['node']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['forum'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('forums/delete', $__vars['node'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__templater->test($__vars['availableFields'], 'empty', array())) {
		$__compilerTemp1 .= '
				<hr class="formRowSep" />

				';
		$__compilerTemp2 = array();
		if ($__templater->isTraversable($__vars['availableFields'])) {
			foreach ($__vars['availableFields'] AS $__vars['fieldId'] => $__vars['field']) {
				$__compilerTemp2[] = array(
					'value' => $__vars['fieldId'],
					'label' => $__templater->escape($__vars['field']['title']),
					'labelclass' => ($__vars['field']['required'] ? 'u-appendAsterisk' : ''),
					'_type' => 'option',
				);
			}
		}
		$__compilerTemp1 .= $__templater->formCheckBoxRow(array(
			'name' => 'available_fields',
			'value' => $__vars['forum']['field_cache'],
			'listclass' => 'field listColumns',
		), $__compilerTemp2, array(
			'label' => 'Campos disponíveis',
			'explain' => '* Os campos com estrela são necessários para novos tópicos quando selecionados. Outros campos são opcionais.',
			'hint' => '
						' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '.field.listColumns',
			'label' => 'Selecionar todos',
			'_type' => 'option',
		))) . '
					',
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				<hr class="formRowSep" />

				' . $__templater->formRow('
					' . $__templater->filter('Nenhuma', array(array('parens', array()),), true) . ' <a href="' . $__templater->func('link', array('custom-thread-fields/add', ), true) . '" target="_blank">' . 'Adicionar campo' . '</a>
				', array(
			'label' => 'Campos disponíveis',
		)) . '
			';
	}
	$__compilerTemp3 = '';
	if (!$__templater->test($__vars['prefixesGrouped'], 'empty', array())) {
		$__compilerTemp3 .= '
				<hr class="formRowSep" />

				';
		$__compilerTemp4 = array();
		if ($__templater->isTraversable($__vars['prefixGroups'])) {
			foreach ($__vars['prefixGroups'] AS $__vars['prefixGroupId'] => $__vars['prefixGroup']) {
				if ($__vars['prefixesGrouped'][$__vars['prefixGroupId']]) {
					$__compilerTemp4[] = array(
						'check-all' => 'true',
						'listclass' => 'listColumns',
						'label' => ($__vars['prefixGroupId'] ? $__vars['prefixGroup']['title'] : 'Ungrouped'),
						'_type' => 'optgroup',
						'options' => array(),
					);
					end($__compilerTemp4); $__compilerTemp5 = key($__compilerTemp4);
					if ($__templater->isTraversable($__vars['prefixesGrouped'][$__vars['prefixGroupId']])) {
						foreach ($__vars['prefixesGrouped'][$__vars['prefixGroupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
							$__compilerTemp4[$__compilerTemp5]['options'][] = array(
								'value' => $__vars['prefixId'],
								'selected' => $__vars['forum']['prefix_cache'][$__vars['prefixId']],
								'label' => '<span class="label ' . $__templater->escape($__vars['prefix']['css_class']) . '">' . $__templater->escape($__vars['prefix']['title']) . '</span>',
								'_type' => 'option',
							);
						}
					}
				}
			}
		}
		$__compilerTemp3 .= $__templater->formCheckBoxRow(array(
			'name' => 'available_prefixes',
			'listclass' => 'prefix',
			'data-xf-init' => 'checkbox-select-disabler',
			'data-select' => 'select[name=default_prefix_id]',
		), $__compilerTemp4, array(
			'rowtype' => 'explainOffset',
			'label' => 'Prefixos disponíveis',
			'explain' => 'Select all prefixes that should be available for use within this forum',
			'hint' => '
						' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '.prefix',
			'label' => 'Selecionar todos',
			'_type' => 'option',
		))) . '
					',
		)) . '

				';
		$__compilerTemp6 = array(array(
			'value' => '-1',
			'label' => 'Nenhuma',
			'_type' => 'option',
		));
		if ($__templater->isTraversable($__vars['prefixGroups'])) {
			foreach ($__vars['prefixGroups'] AS $__vars['prefixGroupId'] => $__vars['prefixGroup']) {
				if (($__templater->func('count', array($__vars['prefixesGrouped'][$__vars['prefixGroupId']], ), false) > 0)) {
					$__compilerTemp6[] = array(
						'label' => (($__vars['prefixGroupId'] > 0) ? $__vars['prefixGroup']['title'] : 'Ungrouped'),
						'_type' => 'optgroup',
						'options' => array(),
					);
					end($__compilerTemp6); $__compilerTemp7 = key($__compilerTemp6);
					if ($__templater->isTraversable($__vars['prefixesGrouped'][$__vars['prefixGroupId']])) {
						foreach ($__vars['prefixesGrouped'][$__vars['prefixGroupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
							$__compilerTemp6[$__compilerTemp7]['options'][] = array(
								'value' => $__vars['prefixId'],
								'disabled' => (!$__templater->func('in_array', array($__vars['prefixId'], $__vars['forum']['prefix_cache'], ), false)),
								'label' => $__templater->escape($__vars['prefix']['title']),
								'_type' => 'option',
							);
						}
					}
				}
			}
		}
		$__compilerTemp3 .= $__templater->formSelectRow(array(
			'name' => 'default_prefix_id',
			'value' => $__vars['forum']['default_prefix_id'],
		), $__compilerTemp6, array(
			'label' => 'Prefixo de tópico padrão',
			'explain' => 'Você pode especificar um prefixo de tópico para ser automaticamente selecionado quando os visitantes criarem novos tópicos neste fórum. O prefixo selecionado <b>deve</b> ser selecionado na lista \'Prefixos disponíveis\' acima.',
		)) . '

				' . $__templater->formCheckBoxRow(array(
			'name' => 'require_prefix',
			'value' => $__vars['forum']['require_prefix'],
		), array(array(
			'value' => '1',
			'label' => 'Require users to select a prefix',
			'hint' => 'Se selecionado, os usuários serão obrigados a selecionar um prefixo ao criar um tópico ou atualizar seu título. Isso não será aplicado para moderadores ou quando mover um tópico.',
			'_type' => 'option',
		)), array(
		)) . '

			';
	} else {
		$__compilerTemp3 .= '

				<hr class="formRowSep" />

				' . $__templater->formRow('
					' . $__templater->filter('Nenhuma', array(array('parens', array()),), true) . ' <a href="' . $__templater->func('link', array('thread-prefixes/add', ), true) . '" target="_blank">' . 'Adicionar prefixo' . '</a>
				', array(
			'label' => 'Prefixos disponíveis',
		)) . '

				' . $__templater->formHiddenVal('default_thread_prefix', '0', array(
		)) . '
				' . $__templater->formHiddenVal('require_prefix', '0', array(
		)) . '

			';
	}
	$__compilerTemp8 = '';
	if (!$__templater->test($__vars['availablePrompts'], 'empty', array())) {
		$__compilerTemp8 .= '

				<hr class="formRowSep" />

				';
		$__compilerTemp9 = array();
		if ($__templater->isTraversable($__vars['promptGroups'])) {
			foreach ($__vars['promptGroups'] AS $__vars['promptGroupId'] => $__vars['promptGroup']) {
				if ($__vars['promptsGrouped'][$__vars['promptGroupId']]) {
					$__compilerTemp9[] = array(
						'check-all' => 'true',
						'listclass' => '_listColumns',
						'label' => ($__vars['promptGroupId'] ? $__vars['promptGroup']['title'] : 'Ungrouped'),
						'_type' => 'optgroup',
						'options' => array(),
					);
					end($__compilerTemp9); $__compilerTemp10 = key($__compilerTemp9);
					if ($__templater->isTraversable($__vars['promptsGrouped'][$__vars['promptGroupId']])) {
						foreach ($__vars['promptsGrouped'][$__vars['promptGroupId']] AS $__vars['promptId'] => $__vars['prompt']) {
							$__compilerTemp9[$__compilerTemp10]['options'][] = array(
								'value' => $__vars['promptId'],
								'selected' => $__vars['forum']['prompt_cache'][$__vars['promptId']],
								'label' => $__templater->escape($__vars['prompt']['title']),
								'_type' => 'option',
							);
						}
					}
				}
			}
		}
		$__compilerTemp8 .= $__templater->formCheckBoxRow(array(
			'name' => 'available_prompts',
			'listclass' => 'prompt',
		), $__compilerTemp9, array(
			'rowtype' => 'explainOffset',
			'label' => 'Available prompts',
			'explain' => 'Users will be prompted to post a new thread in this forum using one of the prompts selected here. The prompt appears in the thread title input box, before a title is entered. If no prompts are selected, the default prompt phrase (<a href="' . $__templater->func('link', array('phrases/edit-by-name', array(), array('title' => 'thread_prompt.default', ), ), true) . '"><code>thread_prompt.default</code></a>) is used.',
			'hint' => '
						' . $__templater->formCheckBox(array(
			'standalone' => 'true',
		), array(array(
			'check-all' => '.prompt',
			'label' => 'Selecionar todos',
			'_type' => 'option',
		))) . '
					',
		)) . '

			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->callMacro('node_edit_macros', 'title', array(
		'node' => $__vars['node'],
	), $__vars) . '
			' . $__templater->callMacro('node_edit_macros', 'description', array(
		'node' => $__vars['node'],
	), $__vars) . '

			<hr class="formRowSep" />
			' . $__templater->callMacro('node_edit_macros', 'node_name', array(
		'node' => $__vars['node'],
	), $__vars) . '
			' . $__templater->callMacro('node_edit_macros', 'position', array(
		'node' => $__vars['node'],
		'nodeTree' => $__vars['nodeTree'],
	), $__vars) . '
			' . $__templater->callMacro('node_edit_macros', 'navigation', array(
		'node' => $__vars['node'],
		'navChoices' => $__vars['navChoices'],
	), $__vars) . '
			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'allow_posting',
		'selected' => $__vars['forum']['allow_posting'],
		'label' => 'Permitir que novas mensagens sejam postadas neste fórum',
		'hint' => 'Se desativado, os usuários não poderão postar novas mensagens ou editar ou excluir suas próprias mensagens. Os moderadores ainda poderão gerenciar os conteúdos deste fórum.',
		'_type' => 'option',
	),
	array(
		'name' => 'allow_poll',
		'selected' => $__vars['forum']['allow_poll'],
		'label' => 'Permitir que enquetes sejam criadas neste fórum',
		'hint' => 'Se desabilitado, os usuários não terão a opção de criar uma enquete ao publicar um tópico ou adicioná-lo mais tarde. Se um tópico com uma pesquisa for movido para este fórum, ele manterá a enquete.',
		'_type' => 'option',
	),
	array(
		'name' => 'moderate_threads',
		'selected' => $__vars['forum']['moderate_threads'],
		'label' => 'Moderar novos tópicos postados neste fórum',
		'hint' => 'Se ativado, um moderador terá que aprovar manualmente os tópicos postados neste fórum.',
		'_type' => 'option',
	),
	array(
		'name' => 'moderate_replies',
		'selected' => $__vars['forum']['moderate_replies'],
		'label' => 'Moderar respostas postadas neste fórum',
		'hint' => 'Se ativado, um moderador terá que aprovar manualmente as respostas enviadas para os tópicos neste fórum.',
		'_type' => 'option',
	),
	array(
		'name' => 'count_messages',
		'selected' => $__vars['forum']['count_messages'],
		'label' => 'Contagem de mensagens publicadas neste fórum em relação ao total de usuários',
		'hint' => 'Se desabilitado, as mensagens postadas (diretamente) neste fórum não contribuirão para a contagem total de mensagens do usuário de postagem.',
		'_type' => 'option',
	),
	array(
		'name' => 'find_new',
		'selected' => $__vars['forum']['find_new'],
		'label' => 'Incluir tópicos deste fórum quando os usuários clicarem em "Novos posts"',
		'hint' => 'Se desativado, os tópicos deste fórum nunca aparecerão na lista de postagens novas / não lidas.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'min_tags',
		'value' => $__vars['forum']['min_tags'],
		'min' => '0',
		'max' => '100',
	), array(
		'label' => 'Minimum required tags',
		'explain' => 'This will require users to provide at least this many tags when creating a thread.',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'allowed_watch_notifications',
		'value' => $__vars['forum']['allowed_watch_notifications'],
	), array(array(
		'value' => 'all',
		'label' => 'Novas mensagens',
		'_type' => 'option',
	),
	array(
		'value' => 'thread',
		'label' => 'Novos tópicos',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Nenhuma',
		'_type' => 'option',
	)), array(
		'label' => 'Forum watch notification limit',
		'explain' => 'Você pode limitar a quantidade de notificações que podem ser acionadas por um usuário após um fórum aqui. Por exemplo, se você selecionar "novos tópicos", os usuários só poderão escolher entre notificações ou notificações quando um novo tópico for publicado. Isso pode ser usado para limitar a sobrecarga do sistema de seguir fórum em fóruns ocupados.',
	)) . '

			' . $__templater->formRow('

				<div class="inputPair">
					' . $__templater->formSelect(array(
		'name' => 'default_sort_order',
		'value' => $__vars['forum']['default_sort_order'],
		'class' => 'input--inline',
	), array(array(
		'value' => 'last_post_date',
		'label' => 'Última mensagem',
		'_type' => 'option',
	),
	array(
		'value' => 'post_date',
		'label' => 'Start date',
		'_type' => 'option',
	),
	array(
		'value' => 'title',
		'label' => 'Título',
		'_type' => 'option',
	),
	array(
		'value' => 'reply_count',
		'label' => 'Respostas',
		'_type' => 'option',
	),
	array(
		'value' => 'view_count',
		'label' => 'Visualizações',
		'_type' => 'option',
	))) . '
					' . $__templater->formSelect(array(
		'name' => 'default_sort_direction',
		'value' => $__vars['forum']['default_sort_direction'],
		'class' => 'input--inline',
	), array(array(
		'value' => 'desc',
		'label' => 'Decrescente',
		'_type' => 'option',
	),
	array(
		'value' => 'asc',
		'label' => 'Crescente',
		'_type' => 'option',
	))) . '
				</div>
			', array(
		'rowtype' => 'input',
		'label' => 'Default sort order',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'list_date_limit_days',
		'value' => $__vars['forum']['list_date_limit_days'],
	), array(array(
		'value' => '0',
		'label' => 'Nenhuma',
		'_type' => 'option',
	),
	array(
		'value' => '7',
		'label' => '' . '7' . ' dias',
		'_type' => 'option',
	),
	array(
		'value' => '14',
		'label' => '' . '14' . ' dias',
		'_type' => 'option',
	),
	array(
		'value' => '30',
		'label' => '' . '30' . ' dias',
		'_type' => 'option',
	),
	array(
		'value' => '60',
		'label' => '' . '2' . ' meses',
		'_type' => 'option',
	),
	array(
		'value' => '90',
		'label' => '' . '3' . ' meses',
		'_type' => 'option',
	),
	array(
		'value' => '182',
		'label' => '' . '6' . ' meses',
		'_type' => 'option',
	),
	array(
		'value' => '365',
		'label' => '1 ano',
		'_type' => 'option',
	)), array(
		'label' => 'Limite de data da lista de tópicos',
		'explain' => 'Isso pode ser usado em fóruns ocupados para melhorar o desempenho somente listando tópicos atualizados recentemente por padrão.',
	)) . '

			' . $__templater->callMacro('node_edit_macros', 'style', array(
		'node' => $__vars['node'],
		'styleTree' => $__vars['styleTree'],
	), $__vars) . '

			' . $__compilerTemp1 . '

			' . $__compilerTemp3 . '

			' . $__compilerTemp8 . '

		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('forums/save', $__vars['node'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});