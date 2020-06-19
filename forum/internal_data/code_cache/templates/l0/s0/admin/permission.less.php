<?php
// FROM HASH: 1131555a009c02a622996975775110c6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.formRow.formRow--permissionQuickSet
{
	> dt,
	> dd
	{
		padding-bottom: 0;
	}
}

.permissionChoices
{
	.m-listPlain();
	.m-clearFix();

	> li
	{
		float: left;
		margin-left: 1em;

		&:first-child
		{
			margin-left: 0;
		}
	}
}

.permissionChoices-choice
{
	border: 1px solid transparent;
	border-radius: @xf-borderRadiusMedium;

	&.is-selected
	{
		box-shadow: 1px 1px 5px rgba(0,0,0, .25);

		&.permissionChoices-choice--inherit
		{
			border-color: #c0c0c0;
			background: #ededed;
			color: #3c3c3c;
		}

		&.permissionChoices-choice--yes
		{
			border-color: #82ce85;
			background: #daf3d8;
			color: #3d793f;
		}

		&.permissionChoices-choice--no
		{
			border-color: #ecb25e;
			background: #fdf0cf;
			color: #9a6e30;
		}

		&.permissionChoices-choice--never
		{
			border-color: #eaa4a1;
			background: #fde9e9;
			color: #c84448;
		}
	}
}

.permissionChoices--flag
{
	label
	{
		display: block;
		min-width: 90px;
		text-align: center;
		padding: 0 @xf-paddingMedium;
	}

	.iconic i
	{
		position: relative;
		vertical-align: top;
	}
}

.permissionChoices--int
{
	.permissionChoices-choice--yes,
	.permissionChoices-choice--inherit
	{
		label
		{
			display: inline-block;
			line-height: 27px;
			padding: 0 @xf-paddingMedium;
			min-width: 90px;
			text-align: center;
		}

		.iconic i
		{
			position: relative;
			vertical-align: top;
		}
	}

	.inputGroup.inputGroup--joined
	{
		position: relative;

		.inputGroup-text
		{
			&.inputNumber-button
			{
				&.inputNumber-button--up
				{
					border-left: 0;
				}
			}
		}

		.input--number
		{
			display: inline;
			width: 80px;
			padding-top: @xf-paddingSmall;
			padding-bottom: @xf-paddingSmall;
		}
	}

	.permissionChoices-choice--int
	{
		&.is-disabled .inputGroup-text
		{
			.xf-inputDisabled(background);
		}

		&.is-disabled .permissionChoices-choiceIntLabel
		{
			display: block;
		}

		&.is-selected .input[type=number],
		&.is-selected .input[type=tel]
		{
			.xf-inputFocus();
		}
	}
}

.permissionChoices-choiceIntLabel
{
	display: none;
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
}

@media (max-width: @xf-responsiveNarrow)
{
	.permissionChoices--flag,
	.permissionChoices--int .permissionChoices-choice--inherit
	{
		label
		{
			min-width: 0;
		}
	}
}';
	return $__finalCompiled;
});