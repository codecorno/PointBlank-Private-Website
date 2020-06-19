<?php
// FROM HASH: 19b2895a0e5a20f91d5f44427c13e42d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.addOnList
{
	.block,
	.blockMessage
	{
		&.is-hidden
		{
			display: none;
		}
	}
}

.addOnList-row
{
	&.is-disabled
	{
		.contentRow-figure,
		.contentRow-header,
		.contentRow-lesser
		{
			opacity: 0.5;
		}

		.contentRow-header
		{
			text-decoration: line-through;
		}

		.contentRow-extra
		{
			// this is needed to move this above the semi-transparent layer that\'s created above
			position: relative;
			z-index: 2;
		}
	}

	&.is-hidden
	{
		display: none;
	}

	.contentRow-lesser
	{
		&.no-description
		{
			visibility: hidden;
		}
	}

	.is-match
	{
		text-decoration: underline;
		color: red;
	}
}

';
	return $__finalCompiled;
});