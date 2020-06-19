!function($, window, document, _undefined)
{
	"use strict";

	XF.CodeEditor = XF.Element.newHandler({
		options: {
			indentUnit: 4,
			indentWithTabs: true,
			lineNumbers: true,
			lineWrapping: false,
			autoCloseBrackets: true,
			mode: null,
			config: null,
			submitSelector: null,
			scrollbarStyle: 'simple'
		},

		editor: null,
		$wrapper: null,

		init: function()
		{
			// this is checking the parent node because we have css that will force hide this textarea
			if (this.$target[0].parentNode.scrollHeight)
			{
				this.initEditor();
			}
			else
			{
				this.$target.oneWithin('toggle:shown overlay:showing tab:shown', XF.proxy(this, 'initEditor'));
			}
		},

		initEditor: function()
		{
			var $textarea = this.$target,
				lang = {},
				config = {};

			if ($textarea.data('cmInitialized'))
			{
				return;
			}

			try
			{
				config = $.parseJSON(this.options.config);
			}
			catch (e)
			{
				config = this.options.config;
			}

			try
			{
				lang = $.parseJSON($('.js-codeEditorLanguage').first().html()) || {};
			}
			catch (e)
			{
				console.error(e);
				lang = {};
			}

			this.editor = CodeMirror.fromTextArea($textarea.get(0), $.extend({
				mode: this.options.mode,
				indentUnit: this.options.indentUnit,
				indentWithTabs: this.options.indentWithTabs,
				lineNumbers: this.options.lineNumbers,
				lineWrapping: this.options.lineWrapping,
				autoCloseBrackets: this.options.autoCloseBrackets,
				readOnly: $textarea.prop('readonly'),
				autofocus: $textarea.prop('autofocus'),
				scrollbarStyle: this.options.scrollbarStyle,
				phrases: lang
			}, config));

			this.$wrapper = $(this.editor.getWrapperElement());

			// Sync the textarea classes to CodeMirror
			this.$wrapper.addClass($textarea.attr('class')).attr('dir', 'ltr');
			$textarea.attr('class', '');
			this.editor.refresh();

			XF.layoutChange();

			this.editor.on('keydown', XF.proxy(this, 'keydown'));

			var $form = $textarea.closest('form');
			$form.on('ajax-submit:before', XF.proxy(this, 'onSubmit'));

			$textarea.trigger('code-editor:init', this.editor);

			$textarea.data('cmInitialized', true);
		},

		onSubmit: function(e)
		{
			this.editor.save();
		},

		keydown: function(editor, e)
		{
			// macOS: Cmd + Ctrl + F | other: F11
			if ((XF.isMac() && e.metaKey && e.ctrlKey && e.key == 'f')
				|| (!XF.isMac() && e.key == 'F11')
			)
			{
				e.preventDefault();

				editor.setOption("fullScreen", !editor.getOption("fullScreen"));
			}

			// Escape (exit full screen)
			if (e.key == 'Escape')
			{
				e.stopPropagation();

				if (editor.getOption("fullScreen"))
				{
					editor.setOption("fullScreen", false);
				}
			}

			// (ctrl|meta)+(s|enter) submits the associated form
			if ((e.key == 's' || e.key == 'Enter') && (XF.isMac() ? e.metaKey : e.ctrlKey))
			{
				e.preventDefault();

				var $textarea = $(editor.getTextArea()),
					$form = $textarea.closest('form'),
					selector = this.options.submitSelector,
					$submit = $form.find(selector);

				if (selector && $submit.length)
				{
					$form.find(selector).click();
				}
				else
				{
					$form.submit();
				}
			}
		}
	});

	XF.CodeEditorSwitcherContainer = XF.Element.newHandler({
		options: {
			switcher: '.js-codeEditorSwitcher',
			templateSuffixMode: 0
		},

		$switcher: null,

		editor: null,
		loading: false,

		init: function()
		{
			this.$target.on('code-editor:init', XF.proxy(this, 'initEditor'));
		},

		initEditor: function(e, editor)
		{
			var $switcher = this.$target.find(this.options.switcher);
			if (!$switcher.length)
			{
				console.warn('Switcher container has no switcher: %o', this.$target);
				return;
			}
			this.$switcher = $switcher;

			if ($switcher.is('select, :radio'))
			{
				$switcher.on('change', XF.proxy(this, 'change'));
			}
			else if ($switcher.is('input:not(:checkbox :radio)'))
			{
				$switcher.on('blur', XF.proxy(this, 'blurInput'));

				// Trigger after a short delay to get the existing template's mode and apply
				setTimeout(function()
				{
					$switcher.trigger('blur');
				}, 100);
			}
			else
			{
				console.warn('Switcher only works for text inputs, radios and selects.');
				return;
			}

			this.editor = editor;
		},

		change: function(e)
		{
			var language = this.$switcher.find(":selected").val();

			this.switchLanguage(language);
		},

		blurInput: function(e)
		{
			var language = this.$switcher.val();

			if (this.options.templateSuffixMode)
			{
				language = language.toLowerCase();

				if (language.indexOf('.less') > 0)
				{
					language = 'less';
				}
				else if (language.indexOf('.css') > 0)
				{
					language = 'css';
				}
				else
				{
					language = 'html';
				}
			}

			this.switchLanguage(language);
		},

		switchLanguage: function(language)
		{
			if (this.loading)
			{
				return;
			}

			var self = this,
				editor = this.editor,
				$textarea = $(editor.getTextArea());

			editor.save();

			if ($textarea.data('lang') == language)
			{
				return;
			}

			setTimeout(function()
			{
				var url;
				if ($('html').data('app') == 'public')
				{
					url = 'index.php?misc/code-editor-mode-loader';
				}
				else
				{
					url = 'admin.php?templates/code-editor-mode-loader';
				}

				XF.ajax('post', XF.canonicalizeUrl(url), {
					language: language
				}, XF.proxy(self, 'handleAjax')).always(function() { self.loading = false; });
			}, 200);
		},

		handleAjax: function(data)
		{
			if (data.errors || data.exception)
			{
				return;
			}

			if (data.redirect)
			{
				XF.redirect(data.redirect);
			}

			var editor = this.editor,
				$textarea = $(editor.getTextArea());

			XF.setupHtmlInsert(data.html, function($html, container)
			{
				var mode = '';

				if (data.mime)
				{
					mode = data.mime;
				}
				else if (data.mode)
				{
					mode = data.mode;
				}

				editor.setOption('mode', mode);
				$textarea.data('lang', data.language);
				$textarea.data('config', JSON.stringify(data.config));
				if (data.config)
				{
					$.each(data.config, function(key, value)
					{
						editor.setOption(key, value);
					});
				}
			});
		}
	});

	XF.Element.register('code-editor', 'XF.CodeEditor');
	XF.Element.register('code-editor-switcher-container', 'XF.CodeEditorSwitcherContainer');
}
(jQuery, window, document);