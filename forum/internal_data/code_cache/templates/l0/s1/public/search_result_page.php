<?php
// FROM HASH: 6bc80d4f7dac49a718564ed77558eb74
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<li class="block-row block-row--separated">
	<div class="contentRow">
		<span class="contentRow-figure">
			' . $__templater->func('avatar', array(null, 's', false, array(
	))) . '
		</span>
		<div class="contentRow-main">
			<h3 class="contentRow-title">
				<a href="' . $__templater->func('link', array('pages', $__vars['page'], ), true) . '">' . $__templater->func('highlight', array($__vars['page']['title'], $__vars['options']['term'], ), true) . '</a>
			</h3>

			<div class="contentRow-snippet">' . $__templater->func('snippet', array($__vars['page']['MasterTemplate']['template'], 300, array('term' => $__vars['options']['term'], 'stripHtml' => true, ), ), true) . '</div>

			<div class="contentRow-minor contentRow-minor--hideLinks">
				<ul class="listInline listInline--bullet">
					<li>' . 'Page' . '</li>
					<li>' . $__templater->func('date_dynamic', array($__vars['page']['modified_date'], array(
	))) . '</li>
				</ul>
			</div>
		</div>
	</div>
</li>';
	return $__finalCompiled;
});