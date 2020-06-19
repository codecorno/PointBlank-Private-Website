<?php
// FROM HASH: e6448e8808d3dfe00247ec4a7b355b06
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Merge template changes' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['template']['title']));
	$__finalCompiled .= '

';
	$__templater->includeCss('public:diff.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['diffs'])) {
		foreach ($__vars['diffs'] AS $__vars['diff']) {
			$__compilerTemp1 .= '
						';
			$__vars['diffHtml'] = $__templater->filter($__vars['diff']['1'], array(array('escape', array()),array('join', array('<br />', )),), false);
			$__compilerTemp1 .= '
						';
			if ($__vars['diff']['0'] == 'c') {
				$__compilerTemp1 .= '
							<li class="diffList-conflict js-conflictContainer">
								';
				$__vars['parentHtml'] = $__templater->filter($__vars['diff']['3'], array(array('escape', array()),array('join', array('<br />', )),), false);
				$__compilerTemp1 .= '
								<div class="diffList-line js-diffParent">';
				$__compilerTemp2 = '';
				if ($__vars['diff']['3']) {
					$__compilerTemp2 .= '
										<span>' . (($__templater->func('trim', array($__vars['parentHtml'], ), false) !== '') ? $__templater->filter($__vars['parentHtml'], array(array('raw', array()),), true) : '&nbsp;') . '</span>' . $__templater->formHiddenVal('', $__templater->filter($__vars['diff']['3'], array(array('join', array('
', )),), false), array(
					)) . '
									';
				} else {
					$__compilerTemp2 .= '
										<i>' . 'Deleted' . '</i>
									';
				}
				$__compilerTemp1 .= trim('
									' . $__compilerTemp2 . '
								') . '</div>
								<div class="diffList-resolve">

									' . $__templater->button('
										&uarr; ' . 'Resolve using parent version' . '
									', array(
					'class' => 'js-resolveButton',
					'data-target' => '.js-diffParent',
				), '', array(
				)) . '
									' . $__templater->button('
										' . 'Resolve using both' . '
									', array(
					'class' => 'js-resolveButton',
					'data-target' => '.js-diffParent, .js-diffCustom',
				), '', array(
				)) . '
									' . $__templater->button('
										' . 'Resolve using custom version' . ' &darr;
									', array(
					'class' => 'js-resolveButton',
					'data-target' => '.js-diffCustom',
				), '', array(
				)) . '

									' . $__templater->formHiddenVal('merged[]', $__templater->filter($__vars['diff']['3'], array(array('join', array('
', )),), false), array(
					'class' => 'js-mergedInput',
				)) . '
								</div>
								<div class="diffList-line js-diffCustom">';
				$__compilerTemp3 = '';
				if ($__vars['diff']['1']) {
					$__compilerTemp3 .= '
										<span>' . (($__templater->func('trim', array($__vars['diffHtml'], ), false) !== '') ? $__templater->filter($__vars['diffHtml'], array(array('raw', array()),), true) : '&nbsp;') . '</span>' . $__templater->formHiddenVal('', $__templater->filter($__vars['diff']['1'], array(array('join', array('
', )),), false), array(
					)) . '
									';
				} else {
					$__compilerTemp3 .= '
										<i>' . 'Deleted' . '</i>
									';
				}
				$__compilerTemp1 .= trim('
									' . $__compilerTemp3 . '
								') . '</div>
							</li>
						';
			} else {
				$__compilerTemp1 .= '
							<li class="diffList-line diffList-line--' . $__templater->escape($__vars['diff']['0']) . '">';
				$__compilerTemp4 = '';
				if ($__vars['diff']['1']) {
					$__compilerTemp4 .= '
									<span>' . (($__templater->func('trim', array($__vars['diffHtml'], ), false) !== '') ? $__templater->filter($__vars['diffHtml'], array(array('raw', array()),), true) : '&nbsp;') . '</span>' . $__templater->formHiddenVal('merged[]', $__templater->filter($__vars['diff']['1'], array(array('join', array('
', )),), false), array(
					)) . '
								';
				} else {
					$__compilerTemp4 .= '
									<i>' . 'Deleted' . '</i>
								';
				}
				$__compilerTemp1 .= trim('
								' . $__compilerTemp4 . '
							') . '</li>
						';
			}
			$__compilerTemp1 .= '
					';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			<div class="block-row block-row--separated">
				<div class="diffListContainer" dir="ltr">
					<ol class="diffList diffList--code diffList--wrapped diffList--editable">
					' . $__compilerTemp1 . '
					</ol>
				</div>
			</div>
			<div class="block-row block-row--separated">
				' . 'You may click on an update to edit it.' . '
			</div>
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Merge',
		'icon' => 'save',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('templates/merge-outdated', $__vars['template'], ), false),
		'id' => 'js-mergeForm',
		'class' => 'block',
	)) . '

';
	$__templater->inlineJs('
	//<script>
		$(function()
		{
			var $form = $(\'#js-mergeForm\');
			var $buttons = $form.find(\'.js-resolveButton\');
			if ($buttons.length)
			{
				$form.find(\':submit\').addClass(\'is-disabled\').prop(\'disabled\', true);
				$buttons.click(function()
				{
					var $this = $(this);
					var $container = $this.closest(\'.js-conflictContainer\');
					var $hidden = $container.find(\'.js-mergedInput\');
					var $target = $container.find($this.data(\'target\'));
					var $firstTarget = $target.first();

					var $selectedInput = $target.find(\'input[type=hidden]\');

					if (!$selectedInput.length)
					{
						// deleted, so need to rename the input so it doesn\'t get sent through
						$hidden.attr(\'name\', \'\');
					}
					else if ($selectedInput.length > 1)
					{
						var val = [];
						$selectedInput.each(function() {
							val.push($(this).val());
						});
						$hidden.val(val.join("\\n"));
					}
					else
					{
						$hidden.val($selectedInput.val());
					}

					$container.children().hide();
					$container.addClass(\'is-resolved\');
					$firstTarget.addClass(\'is-chosen\').show();
					if ($hidden.length)
					{
						$firstTarget.html($(\'<span />\').text($hidden.val()).append(\'<br />\'));
					}

					if (!$buttons.filter(\':visible\').length)
					{
						$form.find(\':submit\').removeClass(\'is-disabled\').prop(\'disabled\', false);
					}

					if ($target.length > 1)
					{
						$firstTarget.click();
					}
				});
			}

			$form.on(\'click\', \'.diffList-line--u, .diffList-conflict.is-resolved .is-chosen\', function() {
				var $this = $(this),
					$html = $this.find(\'span\'),
					$textarea = $this.find(\'textarea\'),
					$input = $this.find(\'input[type=hidden]\');

				if (!$html.length)
				{
					return; // nothing to edit
				}

				if (!$input.length || !$input.attr(\'name\'))
				{
					$input = $this.closest(\'.js-conflictContainer\').find(\'.js-mergedInput\');
				}
				if (!$input)
				{
					return;
				}

				$html.hide();
				if (!$textarea.length)
				{
					$textarea = $(\'<textarea class="input" rows="1" />\').val($input.val());
					$textarea.insertAfter($html);
					XF.Element.applyHandler($textarea, \'textarea-handler\');

					$textarea.on(\'blur\', function() {
						$input.val($textarea.val());
						$html.text($textarea.val()).append(\'<br>\');
						$html.show();
						$textarea.hide();
					});
				}

				$textarea.show().focus();
			});
		});
	//</script>
');
	return $__finalCompiled;
});