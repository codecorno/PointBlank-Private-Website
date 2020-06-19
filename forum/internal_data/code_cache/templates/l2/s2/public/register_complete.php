<?php
// FROM HASH: 3c0cc67a7719df37aefcec1ad5dc9596
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Registrar-se');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'robots', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

<div class="blockMessage">
	';
	if ($__vars['xf']['visitor']['user_state'] == 'email_confirm') {
		$__finalCompiled .= '
		' . 'Obrigado por se registrar. Para completar o seu registo, você tem que abrir o link no e-mail que lhe foi enviado.' . '
	';
	} else if ($__vars['xf']['visitor']['user_state'] == 'moderated') {
		$__finalCompiled .= '
		' . 'Obrigado por se registrar. Seu registro deve agora ser aprovado por um administrador. Você receberá um e-mail quando uma decisão for tomada.' . '
	';
	} else if ($__vars['facebook']) {
		$__finalCompiled .= '
		' . 'Obrigado por criar uma conta usando o Facebook. A sua conta está agora totalmente ativa.' . '
	';
	} else {
		$__finalCompiled .= '
		' . 'Obrigado por se registrar. Seu registro está completo.' . '
	';
	}
	$__finalCompiled .= '

	<ul>
		';
	if ($__vars['redirect']) {
		$__finalCompiled .= '<li><a href="' . $__templater->func('link', array($__vars['redirect'], ), true) . '">' . 'Retornar à página que você estava visualizando' . '</a></li>';
	}
	$__finalCompiled .= '
		<li><a href="' . $__templater->func('link', array('index', ), true) . '">' . 'Voltar à página principal do fórum' . '</a></li>
		';
	if ($__templater->method($__vars['xf']['visitor'], 'canEditProfile', array())) {
		$__finalCompiled .= '
			<li><a href="' . $__templater->func('link', array('account', ), true) . '">' . 'Editar os detalhes da sua conta' . '</a></li>
		';
	}
	$__finalCompiled .= '
	</ul>
</div>';
	return $__finalCompiled;
});