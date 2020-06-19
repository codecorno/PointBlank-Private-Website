<?php
// FROM HASH: 7681d54a2dfeed6d24f1003049f015c3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Transmogrification reset control');
	$__finalCompiled .= '

';
	$__templater->inlineCss('
	.transmogrifier
	{
		border: none;
		padding: 10px 0;
		width: 310px;
		margin: 30px auto;

		background-color: yellow;
		background-image: repeating-linear-gradient(-45deg, transparent, transparent 15px, rgb(0,0,0) 15px, rgb(0,0,0) 30px);
	}

	.transmogrifier div
	{
		padding: 20px 0;
		background-color: #f0f0f0;
		border: 1px solid black;
		width: 280px;
		text-align: center;
		margin: auto;
	}

	.transmogrifier button
	{
		width: 240px;
		background-color: white;
		border: 2px outset black;
		color: red;
		font-size: 12pt;
		font-weight: bold;
		text-transform: uppercase;
		padding: 10px;
		box-shadow: 2px 2px 10px 0px rgba(0,0,0,0.5);
	}

	.transmogrifier button:active
	{
		background-color: #ffff33;
		-webkit-box-shadow: 0 0 0 0 transparent;
		-moz-box-shadow: 0 0 0 0 transparent;
		-khtml-box-shadow: 0 0 0 0 transparent;
		box-shadow: 0 0 0 0 transparent;
		text-shadow: 0 0 0 transparent, 0 0 10px white;
	}

	.transmogrifier button:hover
	{
		background-color: #ffff77;
	}
');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Are you <em>sure</em> you want to issue a transmogrification reset imperative?' . '
				<div class="transmogrifier">
					<div>' . $__templater->button('Reset transmogrifier', array(
		'type' => 'submit',
		'icon' => 'confirm',
	), '', array(
	)) . '</div>
				</div>
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('tools/transmogrifier-reset', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});