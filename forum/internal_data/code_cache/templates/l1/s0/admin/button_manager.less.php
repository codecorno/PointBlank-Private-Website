<?php
// FROM HASH: 33f1d65db9dca93febd26bc3eb80dbc0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.toolbar-row
{
	border-top: @xf-borderSize solid @xf-borderColorLight;
	border-bottom: @xf-borderSize solid @xf-borderColorLight;
}

.fr-box
{
	&.fr-box--editor
	{
		margin: @xf-paddingLargest 0;

		&.fr-box--editor--toolbarButtons
		{
			max-width: 1200px;
		}

		&.fr-box--editor--toolbarButtonsMD
		{
			max-width: 992px;
		}

		&.fr-box--editor--toolbarButtonsSM
		{
			max-width: 768px;
		}

		&.fr-box--editor--toolbarButtonsXS
		{
			max-width: 480px;
		}

		.fr-toolbar
		{
			min-height: 36px;

			.toolbar-separator
			{
				margin-right: 0;

				+.fr-separator
				{
					margin-left: 0;
				}

				&.fr-command
				{
					&.fr-btn
					{
						background-color: #dfdfdf; // matches separator colouring

						width: 18px;

						i
						{
							margin: 9px 2px;
						}
					}
				}
			}

			.toolbar-addDropdown
			{
				width: auto;
				background-color: xf-intensify(@xf-paletteNeutral1, 8%);

				i
				{
					margin: 9px 4px;
					display: inline-block;
					float: none;
				}

				span
				{
					display: inline-block;
					text-align: center;
					font-size: @xf-fontSizeSmaller;
					font-weight: normal;
					float: none;
					line-height: 24px;
					margin: 0 4px 0 0;
				}
			}
		}

		.fr-separator
		{
			&.fr-hs
			{
				width: 100%;

				&:last-child
				{
					display: none;
				}
			}
		}
	}
}

.gu-mirror
{
	&.fr-btn
	{
		cursor: grabbing;
		touch-action: none;

		background: mix(@xf-paletteNeutral1, @xf-paletteNeutral2);
		width: 32px;
		height: 32px;

		&.toolbar-separator
		{
			background-color: #dfdfdf; // matches separator colouring
			width: 18px;

			i
			{
				margin: 9px 6px;
			}
		}

		i
		{
			margin: 9px 9px;
		}
	}
}

' . $__templater->includeTemplate('public:dragula.less', $__vars);
	return $__finalCompiled;
});