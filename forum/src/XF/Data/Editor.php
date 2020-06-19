<?php

namespace XF\Data;

class Editor
{
	public function getButtonData()
	{
		$buttons = [
			'clearFormatting' => [
				'fa' => 'fa-eraser',
				'title' => \XF::phrase('remove_formatting')
			],
			'bold' => [
				'fa' => 'fa-bold',
				'title' => \XF::phrase('weight_bold')
			],
			'italic' => [
				'fa' => 'fa-italic',
				'title' => \XF::phrase('italic')
			],
			'underline' => [
				'fa' => 'fa-underline',
				'title' => \XF::phrase('underline')
			],
			'strikeThrough' => [
				'fa' => 'fa-strikethrough',
				'title' => \XF::phrase('strike_through')
			],
			'color' => [
				'fa' => 'fa-tint',
				'title' => \XF::phrase('text_color')
			],
			'fontFamily' => [
				'fa' => 'fa-font',
				'title' => \XF::phrase('font_family'),
				'type' => 'dropdown'
			],
			'fontSize' => [
				'fa' => 'fa-text-height',
				'title' => \XF::phrase('font_size'),
				'type' => 'dropdown'
			],
			'insertLink' => [
				'fa' => 'fa-link',
				'title' => \XF::phrase('insert_link')
			],
			'insertImage' => [
				'fa' => 'fa-image',
				'title' => \XF::phrase('insert_image')
			],
			'insertVideo' => [
				'fa' => 'fa-video-plus',
				'title' => \XF::phrase('insert_video')
			],
			'xfSmilie' => [
				'fa' => 'fa-smile',
				'title' => \XF::phrase('smilies')
			],
			'xfMedia' => [
				'fa' => 'fa-video',
				'title' => \XF::phrase('media')
			],
			'xfQuote' => [
				'fa' => 'fa-quote-right',
				'title' => \XF::phrase('quote')
			],
			'xfSpoiler' => [
				'fa' => 'fa-flag',
				'title' => \XF::phrase('spoiler')
			],
			'xfInlineSpoiler' => [
				'fa' => 'fa-flag-checkered',
				'title' => \XF::phrase('inline_spoiler')
			],
			'xfCode' => [
				'fa' => 'fa-code',
				'title' => \XF::phrase('code')
			],
			'xfInlineCode' => [
				'fa' => 'fa-terminal',
				'title' => \XF::phrase('inline_code')
			],
			'align' => [
				'fa' => 'fa-align-left',
				'title' => \XF::phrase('alignment'),
				'type' => 'dropdown'
			],
			'formatOL' => [
				'fa' => 'fa-list-ol',
				'title' => \XF::phrase('ordered_list')
			],
			'formatUL' => [
				'fa' => 'fa-list-ul',
				'title' => \XF::phrase('unordered_list')
			],
			'indent' => [
				'fa' => 'fa-indent',
				'title' => \XF::phrase('indent')
			],
			'outdent' => [
				'fa' => 'fa-outdent',
				'title' => \XF::phrase('outdent')
			],
			'insertTable' => [
				'fa' => 'fa-table',
				'title' => \XF::phrase('insert_table')
			],
			'undo' => [
				'fa' => 'fa-undo',
				'title' => \XF::phrase('undo')
			],
			'redo' => [
				'fa' => 'fa-redo',
				'title' => \XF::phrase('redo')
			],
			'xfDraft' => [
				'fa' => 'fa-save',
				'title' => \XF::phrase('drafts'),
				'type' => 'dropdown'
			],
			'xfBbCode' => [
				'fa' => 'fa-cog',
				'title' => \XF::phrase('toggle_bb_code')
			]
		];

		$buttons = array_merge($buttons, $this->getCustomBbCodeButtons());

		\XF::fire('editor_button_data', [&$buttons, $this]);

		// special cases for button manager
		$buttons['-vs'] = [
			'fa' => 'fa-ellipsis-v-alt',
			'title' => \XF::phrase('vertical_separator'),
			'type' => 'separator'
		];
		$buttons['-hs'] = [
			'fa' => 'fa-ellipsis-h-alt',
			'title' => \XF::phrase('horizontal_separator'),
			'type' => 'separator'
		];

		// get dropdowns
		$buttons = array_merge($buttons, $this->getEditableDropdownData());

		return $buttons;
	}

	public function getCustomBbCodeButtons()
	{
		$bbCodes = \XF::repository('XF:BbCode')->findBbCodesForList()->where('editor_icon_type', '<>', '')->fetch();

		$buttons = [];

		foreach ($bbCodes AS $bbCodeId => $bbCode)
		{
			$key = 'xfCustom_' . $bbCodeId;
			$buttons[$key] = [
				'title' => $bbCode->title
			];

			switch ($bbCode->editor_icon_type)
			{
				case 'fa':
					$buttons[$key]['fa'] = 'fa-' . $bbCode->editor_icon_value;
					break;

				case 'image':
					$buttons[$key]['image'] = $bbCode->editor_icon_value;
					break;

				case '':
					$buttons[$key]['text'] = $bbCode->editor_icon_value;
					break;

			}
		}

		return $buttons;
	}

	public function getEditableDropdownData()
	{
		/** @var \XF\Repository\Editor $editorRepo */
		$editorRepo = \XF::repository('XF:Editor');
		$dropdowns = $editorRepo->findEditorDropdownsForList()->fetch();

		$buttons = [];

		foreach ($dropdowns AS $cmd => $dropdown)
		{
			$buttons[$cmd] = [
				'fa' => $dropdown->icon,
				'title' => $dropdown->title,
				'type' => 'editable_dropdown'
			];
		}

		return $buttons;
	}

	public function getDefaultToolbarButtons($xs = false)
	{
		$toolbar = [
			'clearFormatting',
			'-vs',
			'bold',
			'italic',
			'underline',
			'strikeThrough',
			'-vs',
			'color',
			'fontFamily',
			'fontSize',
			'-vs',
			'insertLink',
			'insertImage',
			'insertVideo',
			'xfSmilie',
			'xfInsert'
		];

		$customButtons = $this->getCustomBbCodeButtons();
		if ($customButtons)
		{
			$toolbar[] = '-vs';
			$toolbar = array_merge($toolbar, array_keys($customButtons));
		}

		$toolbar = array_merge($toolbar, [
			'-vs',
			'align',
			'xfList',
			'insertTable',
			'-vs',
			'undo',
			'redo',
			'-vs',
			'xfDraft',
			'xfBbCode'
		]);

		if ($xs)
		{
			$toolbar = array_values(\XF\Util\Arr::arrayDelete(['fontFamily', 'strikeThrough', 'xfDraft'], $toolbar));
		}

		return $toolbar;
	}

	public function getDefaultDropdowns()
	{
		return [
			'xfInsert' => [
				'xfMedia',
				'xfQuote',
				'xfSpoiler',
				'xfInlineSpoiler',
				'xfCode',
				'xfInlineCode'
			],
			'xfList' => [
				'formatOL',
				'formatUL',
				'indent',
				'outdent'
			]
		];
	}
}