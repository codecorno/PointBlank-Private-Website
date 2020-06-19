<?php
// FROM HASH: fc415b7173d8eea3bba831b8ba9417a9
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '@_attach-thumbSize: xf-option(\'attachmentThumbnailDimensions\', px);

.attachment
{
	display: inline-block;
	.xf-minorBlockContent();
	border-radius: @xf-borderRadiusSmall;
	padding: @xf-paddingMedium;
	width: 165px;
}

.attachment-icon
{
	overflow: hidden;
	text-align: center;
	height: @_attach-thumbSize;
	line-height: @_attach-thumbSize;
	font-size: 0;

	a:hover
	{
		text-decoration: none;
	}

	&.attachment-icon--img
	{
		a,
		img
		{
			display: inline-block;
			vertical-align: middle;
		}

		img
		{
			max-height: @_attach-thumbSize;
			max-width: 100%;
		}
	}

	i
	{
		display: block;
		height: @_attach-thumbSize;

		&:before
		{
			display: inline-block;

			.m-faBase();
			.m-faContent(@fa-var-file);
			font-size: min(.66 * @_attach-thumbSize, 100px);
			vertical-align: middle;

			color: @xf-textColorFeature;

			border-radius: 100%;
		}
	}

	// text files
	&[data-extension="txt"],
	&[data-extension="patch"],
	&[data-extension="diff"]
	{
		i:before
		{
			.m-faContent(@fa-var-file);
		}
	}

	// code files
	&[data-extension="c"],
	&[data-extension="cpp"],
	&[data-extension="h"],
	&[data-extension="htm"],
	&[data-extension="html"],
	&[data-extension="js"],
	&[data-extension="json"],
	&[data-extension="php"],
	&[data-extension="pl"],
	&[data-extension="py"],
	&[data-extension="xml"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-code);
		}
	}

	// archive files
	&[data-extension="7z"],
	&[data-extension="gz"],
	&[data-extension="rar"],
	&[data-extension="tar"],
	&[data-extension="zip"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-archive);
		}
	}

	// image files
	&[data-extension="gif"],
	&[data-extension="jpe"],
	&[data-extension="jpeg"],
	&[data-extension="jpg"],
	&[data-extension="png"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-alt);
		}
	}

	// audio files
	&[data-extension="aac"],
	&[data-extension="mp3"],
	&[data-extension="ogg"],
	&[data-extension="wav"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-audio);
		}
	}

	// video files
	&[data-extension="avi"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-video);
		}
	}

	// special files
	&[data-extension="xls"],
	&[data-extension="xlsx"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-excel);
		}
	}

	&[data-extension="pdf"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-pdf);
		}
	}

	&[data-extension="ppt"],
	&[data-extension="pptx"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-powerpoint);
		}
	}

	&[data-extension="doc"],
	&[data-extension="docx"]
	{
		i:before
		{
			.m-faContent(@fa-var-file-word);
		}
	}
}

.attachment-name
{
	margin-top: @xf-paddingMedium;
	.m-overflowEllipsis();
}

.attachment-details
{
	font-size: @xf-fontSizeSmaller;
	color: @xf-textColorMuted;

	.m-clearFix();
}

.attachment-details-size { float: left; }
.attachment-details-views { float: right; }

.attachmentList
{
	.m-listPlain();
	display: flex;
	flex-wrap: wrap;
	align-items: stretch;
	margin-bottom: -5px;

	.attachment
	{
		margin-right: 5px;
		margin-bottom: 5px;
		min-width: 0;
		display: flex;
		flex-direction: column;
	}

	.attachment-icon
	{
		flex-grow: 1;
		display: flex;
		height: auto;
		line-height: 1;
		align-items: center;

		a
		{
			margin: auto;
		}

		i
		{
			height: auto;
		}
	}
}

.attachUploadList
{
	.m-listPlain();
	.xf-contentAltBase();
	border: @xf-borderSize solid @xf-borderColor;
	border-radius: @xf-borderRadiusSmall;
	margin-bottom: @xf-paddingLarge;

	&.attachUploadList--spaced
	{
		margin-top: @xf-paddingLarge;
	}

	> li
	{
		border-bottom: @xf-borderSize solid @xf-borderColorLight;
		padding: @xf-paddingMedium;

		&:last-child
		{
			border-bottom: none;
		}

		&.is-uploadError
		{
			.contentRow-title span // span needed due to opacity creating new stacking context
			{
				text-decoration: line-through;
				opacity: .5;
			}

			.contentRow-figure
			{
				opacity: .5;
			}
		}
	}
}

.attachUploadList-insertAll,
.attachUploadList-insertRow
{
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorMuted;

	span
	{
		vertical-align: middle;
	}
}

.attachUploadList-figure.contentRow-figure
{
	width: 100px;
	min-height: 50px;

	img,
	video
	{
		max-height: 100px;
		max-width: 100px;
	}
}

.attachUploadList-placeholder
{
	display: block;
	width: 100px;

	&:before
	{
		display: inline-block;
		.m-faBase();
		.m-faContent(@fa-var-file);
		font-size: 60px;
		vertical-align: middle;
		color: @xf-textColorFeature;
		border-radius: 100%;
	}
}

.attachUploadList-progress
{
	position: relative;

	i
	{
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		background: @xf-progressBarColor;
		color: contrast(@xf-progressBarColor);
		border-radius: @xf-borderRadiusMedium;
		padding-right: .2em;
		text-align: right;
		font-style: normal;
		white-space: nowrap;
		min-width: 2em;

		.m-transition(width);
	}
}

.attachUploadList-error
{
	color: @xf-textColorAttention;
}

@media (max-width: @xf-responsiveNarrow)
{
	.attachUploadList-figure.contentRow-figure
	{
		width: 50px;

		img,
		video
		{
			max-height: 50px;
			max-width: 50px;
		}
	}

	.attachUploadList-placeholder
	{
		display: block;
		width: 50px;

		&:before
		{
			font-size: 30px;
		}
	}
}';
	return $__finalCompiled;
});