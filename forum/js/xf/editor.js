!function($, window, document, _undefined)
{
	"use strict";

	XF.isEditorEnabled = function()
	{
		return XF.LocalStorage.get('editorDisabled') ? false : true;
	};
	XF.setIsEditorEnabled = function(enabled)
	{
		if (enabled)
		{
			XF.LocalStorage.remove('editorDisabled');
		}
		else
		{
			XF.LocalStorage.set('editorDisabled', '1', true);
		}
	};

	XF.Editor = XF.Element.newHandler({
		options: {
			maxHeight: .70,
			minHeight: 250, // default set in Templater->formEditor() $controlOptions['data-min-height']
			buttonsRemove: '',
			attachmentTarget: true,
			deferred: false,
			attachmentUploader: '.js-attachmentUpload',
			attachmentContextInput: 'attachment_hash_combined'
		},

		edMinHeight: 63, // Froala seems to force height to a minimum of 63

		$form: null,
		buttonManager: null,
		ed: null,
		mentioner: null,
		emojiCompleter: null,
		uploadUrl: null,

		init: function()
		{
			if (!this.$target.is('textarea'))
			{
				console.error('Editor can only be initialized on a textarea');
				return;
			}

			// make sure the min height cannot be below the minimum
			this.options.minHeight = Math.max(this.edMinHeight, this.options.minHeight);

			this.$target.trigger('editor:start', [this]);

			this.$form = this.$target.closest('form');
			if (!this.$form.length)
			{
				this.$form = null;
			}

			if (this.options.attachmentTarget)
			{
				var $attachManager = this.$target.closest('[data-xf-init~=attachment-manager]'),
					$uploader = $attachManager.find(this.options.attachmentUploader);
				this.uploadUrl = $uploader.attr('href');
			}

			if (!this.options.deferred)
			{
				this.startInit();
			}
		},

		startInit: function(callbacks)
		{
			var t = this,
				cbBefore = callbacks && callbacks.beforeInit,
				cbAfter = callbacks && callbacks.afterInit;

			this.$target
				.css('visibility', '')
				.on('froalaEditor.initialized', function (m, ed)
				{
					t.ed = ed;

					if (cbBefore)
					{
						cbBefore(t, ed);
					}

					t.editorInit();

					if (cbAfter)
					{
						cbAfter(t, ed);
					}
				})
				.froalaEditor(this.getEditorConfig());
		},

		getEditorConfig: function()
		{
			var fontSize = ['9', '10', '12', '15', '18', '22', '26'];
			var fontFamily = {
				"arial": 'Arial',
				"'book antiqua'": 'Book Antiqua',
				"'courier new'": 'Courier New',
				"georgia": 'Georgia',
				'tahoma': 'Tahoma',
				"'times new roman'": 'Times New Roman',
				"'trebuchet ms'": 'Trebuchet MS',
				"verdana": 'Verdana'
			};

			var heightLimits = this.getHeightLimits();

			var config = {
				direction: $.FE.LANGUAGE.xf.direction,
				editorClass: 'bbWrapper', // since this is a BB code editor, we want our output to normalize like BB code
				fileUpload: false,
				fileMaxSize: 4 * 1024 * 1024 * 1024, // 4G
				fileUploadParam: 'upload',
				fileUploadURL: false,
				fontFamily: fontFamily,
				fontSize: fontSize,
				heightMin: heightLimits[0],
				heightMax: heightLimits[1],
				htmlAllowedTags: ['a', 'b', 'bdi', 'bdo', 'blockquote', 'br', 'cite', 'code', 'dfn', 'div', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'img', 'li', 'mark', 'ol', 'p', 'pre', 's', 'script', 'style', 'small', 'span', 'strike', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'time', 'tr', 'u', 'ul', 'var', 'video', 'wbr'],
				key: '5D5C4B4B3aG3C2A5A4C4E3E2D4F2G2tFOFSAGLUi1AVKd1SN==',
				htmlAllowComments: false,
				imageUpload: false,
				imageCORSProxy: null,
				imageDefaultDisplay: 'inline',
				imageDefaultWidth: 'auto',
				imageEditButtons: ['imageAlign', 'imageSize', 'imageAlt', '|', 'imageReplace', 'imageRemove', '|', 'imageLink', 'linkOpen', 'linkEdit', 'linkRemove'],
				imageManagerLoadURL: false,
				imageMaxSize: 4 * 1024 * 1024 * 1024, // 4G
				imagePaste: false,
				imageResize: true,
				imageUploadParam: 'upload',
				imageUploadRemoteUrls: false,
				imageUploadURL: false,
				language: 'xf',
				linkAlwaysBlank: true,
				linkEditButtons: ['linkOpen', 'linkEdit', 'linkRemove'],
				linkInsertButtons: ['linkBack'],
				placeholderText: '',
				tableResizer: false,
				tableEditButtons: ['tableHeader', 'tableRemove', '|', 'tableRows', 'tableColumns'],
				toolbarSticky: false,
				videoAllowedTypes: ['mp4', 'quicktime', 'ogg', 'webm'],
				videoAllowedProviders: [],
				videoDefaultAlign: 'left',
				videoDefaultDisplay: 'inline',
				videoDefaultWidth: 500,
				videoEditButtons: ['videoReplace', 'videoRemove', '|', 'videoAlign', 'videoSize'],
				videoInsertButtons: ['videoBack', '|', 'videoUpload'],
				videoMaxSize: 4 * 1024 * 1024 * 1024, // 4G
				videoMove: true,
				videoUpload: false,
				videoUploadParam: 'upload',
				videoUploadURL: false,
				zIndex: XF.getElEffectiveZIndex(this.$target) + 1,
				xfBbCodeAttachmentContextInput: this.options.attachmentContextInput
			};
			$.FE.DT = true;

			// fas = solid, far = regular, fal = light
			$.FE.DefineIconTemplate('xf_font_awesome_5', '<i class="fa' + XF.config.fontAwesomeWeight + ' fa-[NAME]" aria-hidden="true"></i>');
			$.FE.ICON_DEFAULT_TEMPLATE = 'xf_font_awesome_5';

			// FA5 overrides
			$.FE.DefineIcon('insertVideo', { NAME: 'video-plus' });
			$.FE.DefineIcon('undo', { NAME: 'undo' });
			$.FE.DefineIcon('redo', { NAME: 'redo' });
			$.FE.DefineIcon('tableHeader', { NAME: 'heading' });

			if (this.uploadUrl)
			{
				var uploadParams = {
					_xfToken: XF.config.csrf,
					_xfResponseType: 'json',
					_xfWithData: 1
				};

				config.fileUpload = true;
				config.fileUploadParams = uploadParams;
				config.fileUploadURL = this.uploadUrl;

				config.imageUpload = true;
				config.imageUploadParams = uploadParams;
				config.imageUploadURL = this.uploadUrl;
				config.imagePaste = true;

				config.videoUpload = true;
				config.videoUploadParams = uploadParams;
				config.videoUploadURL = this.uploadUrl;
			}
			else
			{
				config.imageInsertButtons = ['imageByURL'];
			}

			var buttons = this.getButtonConfig();

			config = $.extend({}, config, buttons);

			this.$target.trigger('editor:config', [config, this]);

			return config;
		},

		getButtonConfig: function()
		{
			try
			{
				var editorToolbars = $.parseJSON($('.js-editorToolbars').first().html()) || {};
			}
			catch (e)
			{
				console.error("Editor buttons data not valid: ", e);
				return;
			}

			var editorDropdownButtons = {};

			try
			{
				var editorDropdowns = $.parseJSON($('.js-editorDropdowns').first().html()) || {};
				for (var d in editorDropdowns)
				{
					if (editorDropdowns.hasOwnProperty(d) && editorDropdowns[d].buttons)
					{
						editorDropdownButtons[d] = editorDropdowns[d].buttons;
					}
				}
			}
			catch (e)
			{
				console.error("Editor dropdowns data not valid: ", e);
			}

			var buttonManager = new XF.EditorButtons(this, editorToolbars, editorDropdownButtons);
			this.buttonManager = buttonManager;

			if (!this.$form || !this.$form.is('[data-xf-init~=draft]'))
			{
				buttonManager.addRemovedButton('xfDraft');
			}

			var attachmentManager = this.getAttachmentManager();
			if (!attachmentManager || !attachmentManager.supportsVideoUploads)
			{
				buttonManager.addRemovedButton('insertVideo');
			}

			if (this.options.buttonsRemove)
			{
				buttonManager.addRemovedButtons(this.options.buttonsRemove.split(','));
			}

			var eventData = {
				buttonManager: buttonManager
			};

			// note: this is a new event, meaning the original event (editor:buttons)
			// no longer triggers. this should avoid any major issues with BC breaks
			// and most functionality can probably now be replicated with the
			// new editor button manager. note the eventData object has changed too.
			this.$target.trigger('editor:toolbar-buttons', [eventData, this]);

			return buttonManager.getToolbars();
		},

		editorInit: function()
		{
			var t = this,
				ed = this.ed;

			this.watchEditorHeight();

			if (this.$form)
			{
				this.$form.on('ajax-submit:before draft:beforesync', function()
				{
					ed.$oel.val(t.ed.html.get());
				});
				this.$form.on('draft:complete', function()
				{
					var $draftButton = ed.$tb.find('.fr-command.fr-btn[data-cmd=xfDraft]'),
						$indicator = $draftButton.find('.editorDraftIndicator');

					if (!$indicator.length)
					{
						$indicator = $('<b class="editorDraftIndicator" />').appendTo($draftButton);
					}
					setTimeout(function() { $indicator.addClass('is-active'); }, 50);
					setTimeout(function() { $indicator.removeClass('is-active'); }, 2500);
				});

				// detect image/video uploads from within Froala and potentially block submission if they're still happening
				this.$form.on('ajax-submit:before', function(e, data)
				{
					var $uploads = ed.$el.find('.fr-uploading');

					if ($uploads.length > 0 && !confirm(XF.phrase('files_being_uploaded_are_you_sure')))
					{
						data.preventSubmit = true;
					}
				});

				ed.events.on('keydown', function(e)
				{
					if (e.key == 'Enter' && (XF.isMac() ? e.metaKey : e.ctrlKey))
					{
						e.preventDefault();
						t.$form.submit();
						return false;
					}
				}, true);
			}

			// hide justify as we don't support it
			ed.$tb.find('[data-cmd=align][data-param1=justify]').closest('li').css('display', 'none');

			// make images be inline automatically
			ed.events.on('image.inserted', function($img)
			{
				$img.removeClass('fr-dib').addClass('fr-dii');
			});

			ed.events.on('image.loaded', function($img)
			{
				t.replaceBase64ImageWithUpload($img);
			});

			ed.events.on('image.beforePasteUpload', function(img)
			{
				var placeholderSrc = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
				if (img.src == placeholderSrc)
				{
					return false;
				}
			});

			var isPlainPaste = false;

			ed.events.on('cut copy', function(e)
			{
				var range = ed.selection.ranges(0);
				if (range && range.commonAncestorContainer)
				{
					var container = range.commonAncestorContainer;
					if (container.nodeType == Node.TEXT_NODE)
					{
						container = container.parentNode;
					}

					var $ps = $(container).find('p');

					$(container).find('p').attr('data-xf-p', '1');

					setTimeout(function()
					{
						$ps.removeAttr('data-xf-p');
					}, 0);
				}
			});

			ed.events.on('paste.before', function(e)
			{
				isPlainPaste = false;

				if (e && e.clipboardData && e.clipboardData.getData)
				{
					var types = '',
						clipboard_types = e.clipboardData.types;

					if (ed.helpers.isArray(clipboard_types))
					{
						for (var i = 0 ; i < clipboard_types.length; i++)
						{
							types += clipboard_types[i] + ';';
						}
					}
					else
					{
						types = clipboard_types;
					}

					if (
						/text\/plain/.test(types) && !ed.browser.mozilla
						&& !/text\/html/.test(types)
						&& (!/text\/rtf/.test(types) || !ed.browser.safari)
					)
					{
						isPlainPaste = true;
					}
				}
			});

			ed.events.on('paste.beforeCleanup', function(content)
			{
				if (isPlainPaste)
				{
					content = content
						.replace(/\t/g, '    ')
						.replace(/  /g, '&nbsp; ')
						.replace(/  /g, '&nbsp; ')
						.replace(/> /g, '>&nbsp;');
				}

				// by the time the clean up happens, these line breaks have been stripped
				content = content.replace(/(<pre[^>]*>)([\s\S]+)(<\/pre>)/g, function(match, open, inner, close)
				{
					inner = inner.replace(/\r?\n/g, '<br>');

					return open + inner + close;
				});

				content = content.replace(/<div(?=\s|>)/g, function(match)
				{
					return match + ' data-xf-p="1"';
				});

				// sometimes URLs are auto-linked when pasting using some browsers. because this interferes with unfurling
				// (an already linked URL cannot be unfurled) attempt to detect and extract the links to paste as text.
				// there are multiple variants depending on browser and OS.

				var match;

				// variant 1: mostly Apple
				match = content.match(/^(?:<meta charset=(?:'|")[^'"]*(?:'|")>)?<a href=(?:'|")([^'"]*)\/?(?:'|")>\1<\/a>$/);
				if (match)
				{
					content = $.trim(match[1]);
				}

				// variant 2: mostly Windows
				match = content.match(/<!--StartFragment--><a href=(?:'|")([^'"]*)\/?(?:'|")>\1<\/a><!--EndFragment-->/);
				if (match)
				{
					content = $.trim(match[1]);
				}

				return XF.adjustHtmlForRte(content);
			});

			ed.events.on('paste.afterCleanup', function(content)
			{
				return t.normalizePaste(content);
			});

			ed.events.on('paste.after', function()
			{
				// keep the cursor visible if possible
				var range = ed.selection.ranges(0);
				if (!range || !range.getBoundingClientRect)
				{
					return;
				}

				var rect = range.getBoundingClientRect(),
					elRect = ed.$wp[0].getBoundingClientRect();

				if (
					rect.top < 0
					|| rect.left < 0
					|| rect.bottom > $(window).height()
					|| rect.right > $(window).width()
					|| rect.bottom > elRect.bottom
				)
				{
					setTimeout(function()
					{
						t.scrollToCursor();
					}, 100);
				}
			});

			// hide the background color chooser
			ed.events.on('popups.show.colors.picker', function()
			{
				$(this.popups.get('colors.picker')).find('.fr-colors-buttons').css('display', 'none');
			});

			var mentionerOpts = {
				url: XF.getAutoCompleteUrl()
			};
			this.mentioner = new XF.AutoCompleter(
				ed.$el, mentionerOpts, ed
			);

			if (XF.config.shortcodeToEmoji)
			{
				var emojiOpts = {
					url: XF.canonicalizeUrl('index.php?misc/find-emoji'),
					at: ':',
					keepAt: false,
					insertMode: 'html',
					displayTemplate: '<div class="contentRow">' +
						'<div class="contentRow-figure contentRow-figure--emoji">{{{icon}}}</div>' +
						'<div class="contentRow-main contentRow-main--close">{{{text}}}' +
						'<div class="contentRow-minor contentRow-minor--smaller">{{{desc}}}</div></div>' +
						'</div>',
					beforeInsert: function(value, el)
					{
						XF.logRecentEmojiUsage($(el).find('img.smilie').data('shortname'));

						return value;
					}
				};
				this.emojiCompleter = new XF.AutoCompleter(
					ed.$el, emojiOpts, ed
				);
			}

			this.setupUploads();

			if (!XF.isEditorEnabled())
			{
				var $bbCodeInput = this.$target.next('input[data-bb-code]');
				if ($bbCodeInput.length)
				{
					ed.bbCode.toBbCode($bbCodeInput.val(), true);
				}
				else
				{
					ed.bbCode.toBbCode(null, true);
				}
			}

			XF.EditorHelpers.setupBlurSelectionWatcher(ed);

			this.$target.on('control:enabled', function()
			{
				ed.edit.on();
			});
			this.$target.on('control:disabled', function()
			{
				ed.edit.off();
			});

			var self = this;
			this.$target.on('control:enabled', function()
			{
				ed.edit.on();
				if (ed.bbCode && ed.bbCode.isBbCodeView())
				{
					var $button = ed.$tb.find('.fr-command[data-cmd=xfBbCode]');
					$button.removeClass('fr-disabled');
				}
				else
				{
					ed.toolbar.enable();
				}
			});
			this.$target.on('control:disabled', function()
			{
				ed.edit.off();
				ed.toolbar.disable();
				ed.$tb.find(' > .fr-command').addClass('fr-disabled');
			});

			this.$target.trigger('editor:init', [ed, this]);

			XF.layoutChange();
		},

		focus: function()
		{
			XF.EditorHelpers.focus(this.ed);
		},

		blur: function()
		{
			XF.EditorHelpers.blur(this.ed);
		},

		normalizePaste: function(content)
		{
			// FF has a tendency of maintaining whitespace from the content which gives odd pasting results
			content = content.replace(/(<(ul|li|p|div)>)\s+/ig, '$1');
			content = content.replace(/\s+(<\/(ul|li|p|div)>)/ig, '$1');

			// some pastes from Chrome insert this span unexpectedly which causes extra bullet points
			content = content
				.replace(/<span>&nbsp;<\/span>/ig, ' ')
				.replace(/(<\/li>)\s+(<li)/ig, '$1$2');

			var ed = this.ed,
				frag = $.parseHTML(content),
				$fragWrapper = $('<div />').html(frag);

			$fragWrapper.find('table').each(function(i, table)
			{
				var $table = $(table).width('100%');
				$table.wrap('<div class="bbTable"></div>');

				$table.find('[colspan], [rowspan]').removeAttr('colspan rowspan');

				var maxColumns = 0;
				$table.find('> tbody > tr').each(function()
				{
					var columnCount = $(this).find('> td, > th').length;
					maxColumns = Math.max(maxColumns, columnCount);
				}).each(function()
				{
					var $cells = $(this).find('> td, > th'),
						columnCount = $cells.length;
					if (columnCount < maxColumns)
					{
						var tag = '<td />';
						if (columnCount && $cells[0].tagName === 'TH')
						{
							tag = '<th />';
						}

						for (var i = columnCount; columnCount < maxColumns; columnCount++)
						{
							$(this).append(tag);
						}
					}
				});
			});

			$fragWrapper.find('code, del, ins, sub, sup').replaceWith(function() { return this.innerHTML; });
			$fragWrapper.find('h1, h2, h3, h4, h5, h6').replaceWith(function()
			{
				var ret = '<b>' + this.innerHTML + '</b>',
					fontSizes = ed.opts.fontSize;

				switch (this.tagName)
				{
					case 'H1': ret = '<span style="font-size: ' + fontSizes[6] + 'px">' + ret + '</span>'; break;
					case 'H2': ret = '<span style="font-size: ' + fontSizes[5] + 'px">' + ret + '</span>'; break;
					case 'H3': ret = '<span style="font-size: ' + fontSizes[4] + 'px">' + ret + '</span>'; break;
					// smaller headers are just bold
				}

				return ret + '<br>';
			});
			$fragWrapper.find('pre').replaceWith(function()
			{
				var inner = this.innerHTML;

				inner = inner
					.replace(/\r?\n/g, '<br>')
					.replace(/\t/g, '    ')
					.replace(/  /g, '&nbsp; ')
					.replace(/  /g, '&nbsp; ')
					.replace(/> /g, '>&nbsp;')
					.replace(/<br> /g, '<br>&nbsp;');

				return inner + '<br>';
			});

			// first we try to move any br tags up to the root if they're only within inline tags...
			$fragWrapper.find('br').each(function(i, br)
			{
				var $parents = $(br).parents().not($fragWrapper);
				if (!$parents.length)
				{
					// at the root of the paste already
					return;
				}

				if ($parents.filter(function(j, el) { return ed.node.isBlock(el); }).length)
				{
					// if we have a block parent, we can't move this
					return;
				}

				var $shiftTarget = $([]),
					shiftIsEl = false,
					$clone,
					ref = br,
					$topParent = $parents.last();

				do
				{
					while (ref.nextSibling)
					{
						$clone = $(ref.nextSibling).clone();
						if (shiftIsEl)
						{
							$shiftTarget.append($clone);
						}
						else
						{
							$shiftTarget = $shiftTarget.add($clone);
						}

						$(ref.nextSibling).remove();
					}
					ref = ref.parentNode;
					if (!ref || $fragWrapper.is(ref))
					{
						break;
					}

					$clone = $(ref).clone().empty();
					$clone.html($shiftTarget);
					$shiftTarget = $clone;
					shiftIsEl = true;
				}
				while (ref.parentNode && !$fragWrapper.is(ref.parentNode));
				// note: this is intentionally checking the ref.parentNode, even though ref has already been moved up.
				// we want to stop when the last tag we cloned is at the root

				$(br).remove();

				$topParent.after($shiftTarget);
				$topParent.after('<br />');
			});

			// Look for root p tags to add extra line breaks since we treat a p as a single break.
			// Try to detect an internal paste and don't add it there
			var copiedText = '',
				pastedText = $fragWrapper[0].textContent.replace(/\s/g, '');

			try
			{
				copiedText = (ed.win.localStorage.getItem('fr-copied-text') || '').replace(/\s/g, '');
			}
			catch (e) {}

			if (copiedText.length && copiedText != pastedText)
			{
				$fragWrapper.find('> p:not([data-xf-p])').each(function()
				{
					if (this.nextSibling)
					{
						$(this).after('<p />');
					}
				});
			}

			$fragWrapper.find('p').removeAttr('data-xf-p');

			frag = $fragWrapper.contents();

			// ...now we split the root level by br tags into p tags. (Make sure we do this after the p doubling
			// since br is a single break
			var node,
				$output = $('<div />'),
				$wrapTarget = null;

			for (var i = 0; i < frag.length; i++)
			{
				node = frag[i];

				if (node.nodeType == Node.ELEMENT_NODE && ed.node.isBlock(node))
				{
					$output.append(node);
					$wrapTarget = null;
				}
				else if (node.nodeType == Node.ELEMENT_NODE && node.tagName == 'BR')
				{
					if (!$wrapTarget)
					{
						// this would generally be two <br> tags in a row
						$output.append('<p />');
					}

					$wrapTarget = null;
				}
				else // text or some other type of element
				{
					if (!$wrapTarget)
					{
						$wrapTarget = $('<p />');
						$output.append($wrapTarget);
					}

					$wrapTarget.append(node);
				}
			}

			var $children = $output.children();
			if ($children.length == 1 && $children.is('p, div'))
			{
				$output = $children;
			}

			return $output.html();
		},

		watchEditorHeight: function()
		{
			var ed = this.ed,
				self = this;

			$(window).onPassive('resize', function()
			{
				var heightLimits = self.getHeightLimits();
				ed.opts.heightMin = heightLimits[0];
				ed.opts.heightMax = heightLimits[1];
				ed.size.refresh();
				XF.layoutChange();
			});
			ed.events.on('focus', function()
			{
				self.scrollToCursorAfterPendingResize();
			});

			//var getHeight = function() { return ed.$el.height(); },
			var getHeight = function() { return ed.$wp.height(); },
				height = getHeight(),
				layoutChangeIfNeeded = function()
				{
					var newHeight = getHeight();
					if (height != newHeight)
					{
						height = newHeight;
						XF.layoutChange();
					}
				};

			ed.events.on('keyup', layoutChangeIfNeeded);
			ed.events.on('commands.after', layoutChangeIfNeeded);
			ed.events.on('html.set', layoutChangeIfNeeded);
			ed.events.on('init', layoutChangeIfNeeded);
			ed.events.on('initialized', layoutChangeIfNeeded);
		},

		getHeightLimits: function()
		{
			var maxHeightOption = this.options.maxHeight,
				minHeightOption = this.options.minHeight,
				maxHeight = null,
				minHeight = null;

			if (this.$target.closest('.overlay').length)
			{
				maxHeightOption = 0.1; // don't grow the editor at all if we are in an overlay
			}

			if (maxHeightOption)
			{
				var viewHeight = $(window).height(),
					height;

				// we can't reliably detect when the keyboard displays, so we need to act like it's always displayed
				if (/(iPad|iPhone|iPod)/g.test(navigator.userAgent))
				{
					viewHeight -= 250;
				}

				if (maxHeightOption > 0)
				{
					if (maxHeightOption <= 1) // example: 0.8 = 80%
					{
						height = viewHeight * maxHeightOption;
					}
					else
					{
						height = maxHeightOption; // example 250 = 250px
					}
				}
				else // example: -100 = window height - 100 px
				{
					height = viewHeight + maxHeightOption;
				}

				maxHeight = Math.floor(height);
				maxHeight = Math.max(maxHeight, 150);
			}

			if (minHeightOption && maxHeight)
			{
				minHeight = Math.min(minHeightOption, maxHeight);
				if (minHeight == maxHeight)
				{
					minHeight -= 1; // prevents an unnecessary scrollbar
				}
			}

			return [minHeight, maxHeight];
		},

		setupUploads: function()
		{
			var t = this,
				ed = this.ed;

			ed.events.on('file.uploaded', function(response)
			{
				this.popups.hide('file.insert');
				this.events.focus();
				return t.handleUploadSuccess(response);
			});

			ed.events.on('file.error', function(details, response)
			{
				this.popups.hide('file.insert');
				t.handleUploadError(details, response);
				this.events.focus();
				return false;
			});

			if (!this.uploadUrl)
			{
				ed.events.on('image.beforeUpload', function()
				{
					return false; // prevent uploading
				});
				ed.events.on('file.beforeUpload', function()
				{
					return false; // prevent uploading
				});
				ed.events.on('video.beforeUpload', function()
				{
					return false; // prevent uploading
				});
			}

			ed.events.on('image.error', function(details, response)
			{
				if (!response)
				{
					return; // not an uploaded image
				}

				this.popups.hide('image.insert');
				t.handleUploadError(details, response);
				return false;
			});

			ed.events.on('video.error', function(details, response)
			{
				if (!response)
				{
					return; // not an uploaded image
				}

				this.popups.hide('video.insert');
				t.handleUploadError(details, response);
				return false;
			});

			ed.events.on('image.uploaded', function(response)
			{
				var onError = function()
				{
					ed.image.remove();
					ed.popups.hide('image.insert');
					ed.events.focus();
					return false;
				};

				var onSuccess = function()
				{
					return true;
				};

				return t.handleUploadSuccess(response, onError, onSuccess);
			});

			ed.events.on('video.uploaded', function(response)
			{
				var onError = function()
				{
					ed.video.remove();
					ed.popups.hide('video.insert');
					ed.events.focus();
					return false;
				};

				var onSuccess = function()
				{
					return true;
				};

				return t.handleUploadSuccess(response, onError, onSuccess);
			});

			var videoImageInsert = function($el, response)
			{
				if (!response)
				{
					return;
				}

				try
				{
					var json = $.parseJSON(response);
				}
				catch (e)
				{
					return;
				}

				if ($el.hasClass('fr-video'))
				{
					var $video = $el.find('video');

					$video
						.attr('data-xf-init', 'video-init')
						.attr('style', '')
						.empty();

					$el = $video;
				}

				if (json.attachment)
				{
					// clean up the data attributes that were added from our JSON response
					var id = json.attachment.attachment_id,
						attrs = $el[0].attributes,
						re = /^data-(?!xf-init)/;
					for (var i = attrs.length - 1; i >= 0; i--)
					{
						if (re.test(attrs[i].nodeName))
						{
							$el.removeAttr(attrs[i].nodeName);
						}
					}

					$el.attr('data-attachment', "full:" + id);
				}
			};

			ed.events.on('image.inserted video.inserted', videoImageInsert);
			ed.events.on('image.replaced video.replaced', videoImageInsert);

			ed.events.on('image.loaded', function($img)
			{
				// try to prevent automatic editing of an image once inserted

				if (!ed.popups.isVisible('image.edit'))
				{
					// ... but not if we're not in the edit mode
					return;
				}

				var $editorImage = ed.image.get();
				if (!$editorImage || $editorImage[0] != $img[0])
				{
					// ... and only if it's for this image
					return;
				}

				ed.image.exitEdit(true);

				var range = ed.selection.ranges(0);
				range.setStartAfter($img[0]);
				range.collapse(true);

				var selection = ed.selection.get();
				selection.removeAllRanges();
				selection.addRange(range);

				ed.events.focus();
				t.scrollToCursor();
			});

			ed.events.on('video.loaded', function($video)
			{
				// try to prevent automatic editing of a video once inserted

				if (!ed.popups.isVisible('video.edit'))
				{
					// ... but not if we're not in the edit mode
					return;
				}

				var $editorVideo = ed.video.get();
				if (!$editorVideo || $editorVideo[0] != $video[0])
				{
					// ... and only if it's for this video
					return;
				}

				ed.events.trigger('video.hideResizer');
				ed.popups.hide('video.edit');

				var range = ed.selection.ranges(0);
				range.setStartAfter($video[0]);
				range.collapse(true);

				var selection = ed.selection.get();
				selection.removeAllRanges();
				selection.addRange(range);

				ed.events.focus();
				t.scrollToCursor();
			});

			ed.events.on('popups.show.image.edit', function()
			{
				var $editorImage = ed.image.get();

				if (!$editorImage.length || !$editorImage.hasClass('smilie'))
				{
					return;
				}

				ed.image.exitEdit(true);
				ed.selection.save();

				setTimeout(function()
				{
					ed.selection.restore();
				}, 0);
			});
		},

		handleUploadSuccess: function(response, onError, onSuccess)
		{
			try
			{
				var json = $.parseJSON(response);
			}
			catch (e)
			{
				json = {
					status: 'error',
					errors: [XF.phrase('oops_we_ran_into_some_problems')]
				}
			}

			if (json.status && json.status == 'error')
			{
				XF.alert(json.errors[0]);
				return onError ? onError(json) : false;
			}

			var attachmentManager = this.getAttachmentManager();
			if (attachmentManager && json.attachment)
			{
				attachmentManager.insertUploadedRow(json.attachment);
				return onSuccess ? onSuccess(json, attachmentManager) : false;
			}

			return false;
		},

		handleUploadError: function(details, response)
		{
			var json;

			try
			{
				json = $.parseJSON(response);
			}
			catch (e)
			{
				json = null;
			}

			if (!json || !json.errors)
			{
				json = {
					status: 'error',
					errors: [XF.phrase('oops_we_ran_into_some_problems')]
				};
			}

			XF.alert(json.errors[0]);
		},

		getAttachmentManager: function()
		{
			var $match = this.$target.closest('[data-xf-init~=attachment-manager]');
			if ($match && $match.length)
			{
				return XF.Element.getHandler($match, 'attachment-manager');
			}

			return null;
		},

		isBbCodeView: function()
		{
			if (this.ed.bbCode && this.ed.bbCode.isBbCodeView)
			{
				return this.ed.bbCode.isBbCodeView();
			}
			else
			{
				return false;
			}
		},

		insertContent: function(html, text)
		{
			var ed = this.ed;

			if (this.isBbCodeView())
			{
				if (typeof text !== 'undefined')
				{
					ed.bbCode.insertBbCode(text);
				}
			}
			else
			{
				this.focus();
				ed.html.insert(html);
				XF.Element.initialize(ed.$el);
			}

			this.scrollToCursor();
			this.scrollToCursorAfterPendingResize();
		},

		replaceContent: function(html, text)
		{
			var ed = this.ed;

			if (this.isBbCodeView())
			{
				if (typeof text !== 'undefined')
				{
					ed.bbCode.replaceBbCode(text);
				}
			}
			else
			{
				ed.html.set(html);
			}
		},

		scrollToCursor: function()
		{
			var ed = this.ed;

			if (this.isBbCodeView())
			{
				ed.bbCode.getTextArea().autofocus();
				ed.$box[0].scrollIntoView(true);
			}
			else
			{
				this.focus();

				var $edBox = ed.$box,
					$edWrapper = ed.$wp,
					selEl = ed.selection.endElement(),
					selBottom = selEl.getBoundingClientRect().bottom,
					selVisible = true,
					winHeight = XF.windowHeight();

				if (XF.browser.ios)
				{
					// assume the keyboard takes up approximately this much space
					winHeight -= 250;
				}

				if (selBottom < 0 || selBottom >= winHeight)
				{
					// outside the window
					selVisible = false;
				}
				if ($edWrapper && selVisible)
				{
					var wrapperRect = $edWrapper[0].getBoundingClientRect();

					if (selBottom > wrapperRect.bottom || selBottom < wrapperRect.top)
					{
						// inside the window, but need to scroll the wrapper
						selVisible = false;
					}
				}

				if (!selVisible)
				{
					var boxPos = $edBox[0].getBoundingClientRect();
					if (boxPos.top < 0 || boxPos.bottom >= winHeight)
					{
						if (!XF.browser.ios)
						{
							// don't add in iOS because it shouldn't apply to small screens but this doesn't trigger
							// in iOS as expected
							$edBox.addClass('is-scrolling-to');
						}
						$edBox[0].scrollIntoView(true);
						$edBox.removeClass('is-scrolling-to');
					}

					if ($edWrapper)
					{
						var info = ed.position.getBoundingRect().top;

						// attempt to put this in the middle of the screen.
						// 50px offset to compensate for sticky form footer.
						// note this doesn't seem to work in iOS at all likely due to webkit limitations.
						if (info > $edWrapper.offset().top - ed.helpers.scrollTop() + $edWrapper.height() - 50) {
							$edWrapper.scrollTop(info + $edWrapper.scrollTop() - ($edWrapper.height() + $edWrapper.offset().top) + ed.helpers.scrollTop() + (winHeight / 2));
						}
					}
					else
					{
						selEl.scrollIntoView();
					}
				}
			}
		},

		scrollToCursorAfterPendingResize: function(forceTrigger)
		{
			// This is to ensure that we keep the cursor visible after the onscreen keyboard appears
			// by trying to determine when this happens and scroll to it.
			var self = this,
				ed = this.ed,
				scrollTimer,
				onResize = function()
				{
					$(window).off('resize', onResize);
					$(window).on('scroll', scrollWatcher);

					if (scrollTimer)
					{
						clearTimeout(scrollTimer);
					}
					scrollTimer = setTimeout(scrollTo, 500);
				},
				scrollWatcher = function()
				{
					if (scrollTimer)
					{
						clearTimeout(scrollTimer);
					}
					scrollTimer = setTimeout(scrollTo, 100);
				},
				scrollTo = function()
				{
					$(window).off('scroll', scrollWatcher);

					if (ed.core.hasFocus())
					{
						self.scrollToCursor();
					}
				};

			$(window).on('resize', onResize);
			setTimeout(function()
			{
				$(window).off('resize', onResize);
			}, 2000);

			if (forceTrigger)
			{
				scrollTimer = setTimeout(scrollTo, 1000);
			}
		},

		base64ToBytes: function(base64String, sliceSize)
		{
			sliceSize = sliceSize || 512;

			var byteCharacters = atob(base64String);
			var byteArrays = [];

			for (var offset = 0; offset < byteCharacters.length; offset += sliceSize)
			{
				var slice = byteCharacters.slice(offset, offset + sliceSize);

				var byteNumbers = new Array(slice.length);
				for (var i = 0; i < slice.length; i++)
				{
					byteNumbers[i] = slice.charCodeAt(i);
				}

				var byteArray = new Uint8Array(byteNumbers);

				byteArrays.push(byteArray);
			}

			return byteArrays;
		},

		editorSupportsUploads: function()
		{
			return (this.ed.opts.imageInsertButtons.indexOf('imageUpload') !== -1);
		},

		imageMatchesBase64Encoding: function($img)
		{
			var src = $img.attr('src');
			return src.match(/^data:(image\/([a-z0-9]+));base64,(.*)$/);
		},

		replaceBase64ImageWithUpload: function($img)
		{
			if ($img.hasClass('smilie'))
			{
				// it's one of our smilies or emojis so skip it
				return;
			}

			var match, contentType, extension, base64String;

			match = this.imageMatchesBase64Encoding($img);

			if (match)
			{
				contentType = match[1];
				extension = match[2];
				base64String = match[3];

				if (this.ed.opts.imageAllowedTypes.indexOf(extension) === -1)
				{
					$img[0].remove();
					return;
				}

				if (this.editorSupportsUploads())
				{
					var file = new Blob(this.base64ToBytes(base64String), {
						type: contentType
					});

					// skip very small data URIs
					if (file.size > 1024)
					{
						this.ed.image.upload([ file ]);
					}
				}
				else
				{
					$img[0].remove();
				}
			}
		}
	});

	XF.EditorButtons = XF.create({
		xfEd: null,
		buttonClasses: null,

		toolbars: {},
		dropdowns: {},
		removeButtons: null,

		recalculateNeeded: true,

		__construct: function(xfEd, toolbars, dropdowns)
		{
			this.xfEd = xfEd;

			// initialize this as empty for each editor instance
			this.removeButtons = [];

			if (toolbars)
			{
				this.toolbars = toolbars;
			}
			if (dropdowns)
			{
				this.dropdowns = dropdowns;
			}
		},

		addToolbar: function(name, buttons)
		{
			this.toolbars[name] = buttons;
			this.recalculateNeeded = true;
		},

		adjustToolbar: function(name, callback)
		{
			var buttons = this.toolbars[name];
			if (buttons)
			{
				this.toolbars[name] = callback(buttons, name, this);
				this.recalculateNeeded = true;
				return true;
			}
			else
			{
				return false;
			}
		},

		adjustToolbars: function(callback)
		{
			for (var k in this.toolbars)
			{
				if (this.toolbars.hasOwnProperty(k))
				{
					this.adjustToolbar(k, callback);
				}
			}
		},

		getToolbar: function(name)
		{
			var toolbars = this.getToolbars();
			return toolbars[name];
		},

		getToolbars: function()
		{
			this.recalculateIfNeeded();

			return this.toolbars;
		},

		addDropdown: function(name, buttons)
		{
			this.dropdowns[name] = buttons;
			this.recalculateNeeded = true;
		},

		adjustDropdown: function(name, callback)
		{
			var buttons = this.dropdowns[name];
			if (buttons)
			{
				this.dropdowns[name] = callback(buttons, name, this);
				this.recalculateNeeded = true;
				return true;
			}
			else
			{
				return false;
			}
		},

		adjustDropdowns: function(callback)
		{
			for (var k in this.dropdowns)
			{
				if (this.dropdowns.hasOwnProperty(k))
				{
					this.adjustDropdown(k, callback);
				}
			}
		},

		getDropdown: function(name)
		{
			var dropdowns = this.getDropdowns();
			return dropdowns[name];
		},

		getDropdowns: function()
		{
			this.recalculateIfNeeded();

			return this.dropdowns;
		},

		addRemovedButton: function(name)
		{
			this.removeButtons.push(name);
			this.recalculateNeeded = true;
		},

		addRemovedButtons: function(buttons)
		{
			for (var i = 0; i < buttons.length; i++)
			{
				this.removeButtons.push(buttons[i]);
			}
			this.recalculateNeeded = true;
		},

		recalculateIfNeeded: function()
		{
			if (this.recalculateNeeded)
			{
				this.recalculate();
			}
		},

		recalculate: function()
		{
			var removeList = this.removeButtons,
				remove,
				buttonClasses = this.getButtonClasses(),
				k;

			function removeFromButtons(buttons, removeName)
			{
				if (typeof removeName == 'string' && buttonClasses[removeName])
				{
					removeName = buttonClasses[removeName];
				}

				if (typeof removeName == 'string')
				{
					removeName = removeName.split('|');
				}

				return buttons.filter(function(button)
				{
					return !(removeName.indexOf(button) >= 0);
				});
			}

			function cleanUp(buttons)
			{
				var newButtons = [],
					value,
					last;

				for (i = 0; i < buttons.length; i++)
				{
					value = buttons[i];

					var isVs = (value === '-vs' || value === '|'),
						isHs = (value === '-hs' || value === '-');

					if (isVs || isHs)
					{
						value = (isVs ? '|' : '-');

						if (isHs && !newButtons.length)
						{
							// don't start with a line break
							continue;
						}
						if (newButtons.length)
						{
							last = newButtons[newButtons.length - 1];
							if (last === value)
							{
								continue;
							}

							if (isHs && last === '|')
							{
								// line break immediately after a | ignores the break, so prioritize the break
								newButtons.pop();
							}
						}
					}

					newButtons.push(value);
				}

				return newButtons;
			}

			// remove disallowed buttons
			for (var i = 0; i < removeList.length; i++)
			{
				remove = removeList[i];

				for (k in this.toolbars)
				{
					if (this.toolbars.hasOwnProperty(k))
					{
						this.toolbars[k] = removeFromButtons(this.toolbars[k], remove);
					}
				}
				for (k in this.dropdowns)
				{
					if (this.dropdowns.hasOwnProperty(k))
					{
						this.dropdowns[k] = removeFromButtons(this.dropdowns[k], remove);
					}
				}
			}

			// remove empty dropdowns
			for (k in this.dropdowns)
			{
				if (this.dropdowns.hasOwnProperty(k) && !this.dropdowns[k].length)
				{
					for (var t in this.toolbars)
					{
						if (this.toolbars.hasOwnProperty(t))
						{
							this.toolbars[t] = removeFromButtons(this.toolbars[t], k);
						}
					}
				}
			}

			// clean up
			for (k in this.toolbars)
			{
				if (this.toolbars.hasOwnProperty(k))
				{
					this.toolbars[k] = cleanUp(this.toolbars[k], remove);
				}
			}
			for (k in this.dropdowns)
			{
				if (this.dropdowns.hasOwnProperty(k))
				{
					this.dropdowns[k] = cleanUp(this.dropdowns[k], remove);
				}
			}

			this.recalculateNeeded = false;
		},

		getButtonClasses: function()
		{
			if (!this.buttonClasses)
			{
				this.buttonClasses = {
					_basic: ['bold', 'italic', 'underline', 'strikeThrough'],
					_extended: ['color', 'fontFamily', 'fontSize', 'xfInlineCode'],
					_link: ['insertLink'],
					_align: ['align'],
					_list: ['formatOL', 'formatUL', 'outdent', 'indent'],
					_indent: ['outdent', 'indent'],
					_smilies: ['xfSmilie'],
					_image: ['insertImage'],
					_media: ['insertVideo', 'xfMedia'],
					_block: ['xfQuote', 'xfCode', 'xfSpoiler', 'xfInlineSpoiler', 'insertTable']
				};
			}

			return this.buttonClasses;
		}
	});

	XF.EditorHelpers = {
		setupBlurSelectionWatcher: function(ed)
		{
			var $el = ed.$el,
				trackSelection = false,
				trackKey = 'xf-ed-blur-sel',
				range;

			$(document).on('mousedown keydown', function(e)
			{
				if (!trackSelection)
				{
					// editor isn't known to be focused
					return;
				}
				if (ed.$el[0] == e.target || $.contains(ed.$el[0], e.target))
				{
					// event triggering is the editor or within it, so should maintain selection
					return;
				}
				if (!ed.selection.inEditor())
				{
					// the current selection isn't in the editor, so nothing to save
					return;
				}

				range = ed.selection.ranges(0);
			});

			ed.events.on('blur', function()
			{
				if (range)
				{
					$el.data(trackKey, range);
				}
				else
				{
					$el.removeData(trackKey);
				}

				trackSelection = false;
				range = null;
			}, true);
			ed.events.on('focus', function()
			{
				trackSelection = true;
				range = null;

				setTimeout(function()
				{
					$el.removeData(trackKey);
				}, 0);
			});
			ed.events.on('commands.before', function(cmd)
			{
				var cmdConfig = $.FE.COMMANDS[cmd];
				if (cmdConfig && (typeof cmdConfig.focus == 'undefined' || cmdConfig.focus))
				{
					XF.EditorHelpers.restoreMaintainedSelection(ed);
					// focus will happen in the command
				}
			});
		},

		restoreMaintainedSelection: function(ed)
		{
			var $el = ed.$el,
				blurSelection = $el.data('xf-ed-blur-sel');

			if (!ed.selection.inEditor())
			{
				if (blurSelection)
				{
					ed.markers.remove();
					ed.markers.place(blurSelection, true, 0);
					ed.markers.place(blurSelection, false, 0);
					ed.selection.restore();
				}
				else
				{
					ed.selection.setAtEnd(ed.el);
					ed.selection.restore();
				}
			}
		},

		focus: function(ed)
		{
			XF.EditorHelpers.restoreMaintainedSelection(ed);
			ed.events.focus();
		},

		blur: function(ed)
		{
			ed.$el[0].blur();
			ed.selection.clear();
		},

		wrapSelectionText: function(ed, before, after, save)
		{
			if (save)
			{
				ed.selection.save();
			}

			var $markers = ed.$el.find('.fr-marker');
			$markers.first().before(XF.htmlspecialchars(before));
			$markers.last().after(XF.htmlspecialchars(after));
			ed.selection.restore();
			ed.placeholder.hide();
		},

		insertCode: function(ed, type, code)
		{
			var tag, lang, output;

			switch (type.toLowerCase())
			{
				case '': tag = 'CODE'; lang = ''; break;
				default: tag = 'CODE'; lang = type.toLowerCase(); break;
			}

			code = code.replace(/&/g, '&amp;').replace(/</g, '&lt;')
				.replace(/>/g, '&gt;').replace(/"/g, '&quot;')
				.replace(/\t/g, '    ')
				.replace(/\n /g, '\n&nbsp;')
				.replace(/  /g, '&nbsp; ')
				.replace(/  /g, ' &nbsp;') // need to do this twice to catch a situation where there are an odd number of spaces
				.replace(/\n/g, '</p><p>');

			output = '[' + tag + (lang ? '=' + lang : '') + ']' + code + '[/' + tag + ']';
			if (output.match(/<\/p>/i))
			{
				output = '<p>' + output + '</p>';
				output = output.replace(/<p><\/p>/g, '<p><br></p>');
			}

			ed.html.insert(output);
		},

		insertSpoiler: function(ed, title)
		{
			var open;
			if (title)
			{
				open = '[SPOILER="' + title + '"]';
			}
			else
			{
				open = '[SPOILER]';
			}

			XF.EditorHelpers.wrapSelectionText(ed, open, '[/SPOILER]', true);
		},

		dialogs: {},

		loadDialog: function (ed, dialog)
		{
			var dialogs = XF.EditorHelpers.dialogs;
			if (dialogs[dialog])
			{
				dialogs[dialog].show(ed);
			}
			else
			{
				console.error("Unknown dialog '" + dialog + "'");
			}
		}
	};

	XF.EditorDialog = XF.create({
		ed: null,
		overlay: null,
		dialog: null,
		cache: true,

		__construct: function(dialog)
		{
			this.dialog = dialog;
		},

		show: function(ed)
		{
			this.ed = ed;

			ed.selection.save();

			XF.loadOverlay(XF.canonicalizeUrl('index.php?editor/dialog&dialog=' + this.dialog), {
				beforeShow: XF.proxy(this, 'beforeShow'),
				afterShow: XF.proxy(this, 'afterShow'),
				init: XF.proxy(this, 'init'),
				cache: this.cache
			});
		},

		init: function(overlay)
		{
			var self = this;

			overlay.on('overlay:hidden', function()
			{
				if (self.ed)
				{
					self.ed.markers.remove();
				}
			});

			this._init(overlay);
		},

		_init: function(overlay) {},

		beforeShow: function(overlay)
		{
			this.overlay = overlay;

			this._beforeShow(overlay);
		},

		_beforeShow: function(overlay) {},

		afterShow: function(overlay)
		{
			this._afterShow(overlay);

			overlay.$overlay.find('textarea, input').first().focus();
		},

		_afterShow: function(overlay) {}
	});

	XF.EditorDialogMedia = XF.extend(XF.EditorDialog, {
		_beforeShow: function(overlay)
		{
			$('#editor_media_url').val('');
		},

		_init: function(overlay)
		{
			$('#editor_media_form').submit(XF.proxy(this, 'submit'));
		},

		submit: function(e)
		{
			e.preventDefault();

			var ed = this.ed,
				overlay = this.overlay;

			XF.ajax('POST',
				XF.canonicalizeUrl('index.php?editor/media'),
				{ url: $('#editor_media_url').val() },
				function (data)
				{
					if (data.matchBbCode)
					{
						ed.selection.restore();
						ed.html.insert(XF.htmlspecialchars(data.matchBbCode));
						overlay.hide();
					}
					else if (data.noMatch)
					{
						XF.alert(data.noMatch);
					}
					else
					{
						ed.selection.restore();
						overlay.hide();
					}
				}
			);
		}
	});

	XF.EditorDialogSpoiler = XF.extend(XF.EditorDialog, {
		_beforeShow: function(overlay)
		{
			$('#editor_spoiler_title').val('');
		},

		_init: function(overlay)
		{
			$('#editor_spoiler_form').submit(XF.proxy(this, 'submit'));
		},

		submit: function(e)
		{
			e.preventDefault();

			var ed = this.ed,
				overlay = this.overlay;

			ed.selection.restore();
			XF.EditorHelpers.insertSpoiler(ed, $('#editor_spoiler_title').val());

			overlay.hide();
		}
	});

	XF.EditorDialogCode = XF.extend(XF.EditorDialog, {
		_beforeShow: function(overlay)
		{
			this.ed.$el.blur();
		},

		_init: function(overlay)
		{
			$('#editor_code_form').submit(XF.proxy(this, 'submit'));
		},

		submit: function(e)
		{
			e.preventDefault();

			var ed = this.ed,
				overlay = this.overlay;

			var $codeMirror = overlay.$container.find('.CodeMirror');
			if ($codeMirror.length)
			{
				var codeMirror = $codeMirror[0].CodeMirror,
					doc = codeMirror.getDoc();

				codeMirror.save();
				doc.setValue('');

				codeMirror.setOption('mode', '');
			}

			var $type = $('#editor_code_type'),
				$code = $('#editor_code_code');

			ed.selection.restore();
			XF.EditorHelpers.insertCode(ed, $type.val(), $code.val());

			overlay.hide();

			$code.val('');
			$type.val('');
		}
	});

	XF.editorStart = {
		started: false,
		custom: [],

		startAll: function()
		{
			if (!XF.editorStart.started)
			{
				XF.editorStart.setupLanguage();
				XF.editorStart.registerCommands();
				XF.editorStart.registerCustomCommands();
				XF.editorStart.registerEditorDropdowns();
				XF.editorStart.registerDialogs();

				$(document).trigger('editor:first-start');

				XF.editorStart.started = true;
			}
		},

		setupLanguage: function()
		{
			var dir = $('html').attr('dir'),
				lang;

			try
			{
				lang = $.parseJSON($('.js-editorLanguage').first().html()) || {};
			}
			catch (e)
			{
				console.error(e);
				lang = {};
			}

			$.FE.LANGUAGE['xf'] = {
				translation: lang,
				direction: dir ? dir.toLowerCase() : 'ltr'
			};
		},

		registerCommands: function()
		{
			$.FE.DefineIcon('xfQuote', { NAME: 'quote-right'});
			$.FE.RegisterCommand('xfQuote', {
				title: 'Quote',
				icon: 'xfQuote',
				undo: true,
				focus: true,
				callback: function()
				{
					XF.EditorHelpers.wrapSelectionText(this, '[QUOTE]', '[/QUOTE]', true);
				}
			});

			$.FE.DefineIcon('xfCode', { NAME: 'code'});
			$.FE.RegisterCommand('xfCode', {
				title: 'Code',
				icon: 'xfCode',
				undo: true,
				focus: true,
				callback: function()
				{
					XF.EditorHelpers.loadDialog(this, 'code');
				}
			});

			$.FE.DefineIcon('xfInlineCode', { NAME: 'terminal'});
			$.FE.RegisterCommand('xfInlineCode', {
				title: 'Inline Code',
				icon: 'xfInlineCode',
				undo: true,
				focus: true,
				callback: function()
				{
					XF.EditorHelpers.wrapSelectionText(this, '[ICODE]', '[/ICODE]', true);
				}
			});

			$.FE.DefineIcon('xfMedia', { NAME: 'video'});
			$.FE.RegisterCommand('xfMedia', {
				title: 'Media',
				icon: 'xfMedia',
				undo: true,
				focus: true,
				callback: function()
				{
					XF.EditorHelpers.loadDialog(this, 'media');
				}
			});

			$.FE.DefineIcon('xfSpoiler', { NAME: 'flag'});
			$.FE.RegisterCommand('xfSpoiler', {
				title: 'Spoiler',
				icon: 'xfSpoiler',
				undo: true,
				focus: true,
				callback: function()
				{
					XF.EditorHelpers.loadDialog(this, 'spoiler');
				}
			});

			$.FE.DefineIcon('xfInlineSpoiler', { NAME: 'flag-checkered'});
			$.FE.RegisterCommand('xfInlineSpoiler', {
				title: 'Inline Spoiler',
				icon: 'xfInlineSpoiler',
				undo: true,
				focus: true,
				callback: function()
				{
					XF.EditorHelpers.wrapSelectionText(this, '[ISPOILER]', '[/ISPOILER]', true);
				}
			});

			$.FroalaEditor.PLUGINS.xfSmilie = function(editor)
			{
				var initialized = false;
				var loaded = false;

				var $menu,
					$menuScroll,
					scrollTop = 0,
					flashTimeout,
					logTimeout;

				function showMenu()
				{
					selectionSave();

					XF.EditorHelpers.blur(editor);

					var $btn = editor.$tb.find('.fr-command[data-cmd="xfSmilie"]');

					if (!initialized)
					{
						initialized = true;

						$menu = $($.parseHTML(Mustache.render($('.js-xfSmilieMenu').first().html())));
						$menu.insertAfter($btn);

						$btn.data('xf-click', 'menu');

						var handler = XF.Event.getElementHandler($btn, 'menu', 'click');

						$menu.on('menu:complete', function()
						{
							$menuScroll = $menu.find('.menu-scroller');

							if (!loaded)
							{
								loaded = true;

								if (window.IntersectionObserver)
								{
									var observer = new IntersectionObserver(onEmojiIntersection, {
										root: $menuScroll[0],
										rootMargin: '0px 0px 100px 0px'
									});
									$menuScroll.find('span.smilie--lazyLoad').each(function()
									{
										observer.observe(this);
									});
								}
								else
								{
									$menuScroll.onPassive('scroll', loadVisibleImages);
								}

								$menuScroll.find('.js-emoji').on('click', insertEmoji);

								var $emojiSearch = $menu.find('.js-emojiSearch');
								$emojiSearch.on('focus', selectionSave);
								$emojiSearch.on('blur', selectionRestore);
								$emojiSearch.on('input', performSearch);

								$menu.find('.js-emojiCloser').on('click', function()
								{
									XF.EditorHelpers.focus(editor);
								});

								$(document).on('recent-emoji:logged', updateRecentEmoji);

								editor.events.on('commands.mousedown', function($el)
								{
									if ($el.data('cmd') != 'xfSmilie')
									{
										handler.close();
									}
								});

								$menu.on('menu:closed', function()
								{
									scrollTop = $menuScroll.scrollTop();
								});
							}

							$menuScroll.scrollTop(scrollTop);

							if (!window.IntersectionObserver)
							{
								loadVisibleImages($menuScroll);
							}
						});

						$menu.on('menu:closed', function()
						{
							setTimeout(function()
							{
								editor.markers.remove();
							}, 50);
						});
					}

					var clickHandlers = $btn.data('xfClickHandlers');
					if (clickHandlers && clickHandlers.menu)
					{
						clickHandlers.menu.toggle();
					}
				}

				function insertEmoji(e)
				{
					var $target = $(e.currentTarget),
						html = $target.html(),
						$html = $(html);

					if ($html.hasClass('smilie--lazyLoad'))
					{
						return;
					}

					XF.EditorHelpers.focus(editor);
					editor.html.insert(html);
					selectionSave();
					XF.EditorHelpers.blur(editor);

					if ($menu)
					{
						var $insertRow = $menu.find('.js-emojiInsertedRow');
						$insertRow.find('.js-emojiInsert').html(html);
						$insertRow.addClassTransitioned('is-active');

						clearTimeout(flashTimeout);
						flashTimeout = setTimeout(function()
						{
							$insertRow.removeClassTransitioned('is-active');
						}, 1500);
					}

					clearTimeout(logTimeout);
					logTimeout = setTimeout(function()
					{
						// delay the logging of the recent emoji usage in order to
						// avoid a situation whereby the emojis do not flip position
						// if you are attempting to insert the same emoji repeatedly.
						// a delay here also prevents the emoji menu from closing.
						XF.logRecentEmojiUsage($target.data('shortname'));
					}, 1500);
				}


				function onEmojiIntersection(changes, observer)
				{
					var entry, $target;

					for (var i = 0; i < changes.length; i++)
					{
						entry = changes[i];
						if (!entry.isIntersecting)
						{
							continue;
						}

						$target = $(entry.target);
						lazyLoadEmoji($target);
						observer.unobserve(entry.target);
					}
				}

				function loadVisibleImages($rowOrEvent)
				{
					var $row = $rowOrEvent;

					if ($rowOrEvent instanceof Event)
					{
						$row = $($rowOrEvent.currentTarget);
					}

					if (!$row.is(':visible'))
					{
						return;
					}

					var visibleRect = $row[0].getBoundingClientRect(),
						visibleBottom = visibleRect.bottom + 100; // 100px offset for visible detection
					$row.children().each(function()
					{
						var $child = $(this),
							childRect = this.getBoundingClientRect();

						if (childRect.bottom < visibleRect.top)
						{
							// area is above what's visible
							return;
						}
						if (childRect.top > visibleBottom)
						{
							// area is below what's visible, so assume everything else is
							return false;
						}

						// otherwise we're visible, so look for smilies here
						$child.find('span.smilie--lazyLoad').each(function()
						{
							var $toLoad = $(this),
								smilieRect = this.getBoundingClientRect();

							if (smilieRect.top <= visibleBottom)
							{
								// smilie is before the end of the visible area, so load
								lazyLoadEmoji($toLoad);
							}
						});
					});
				}

				function lazyLoadEmoji($toLoad)
				{
					var $image = $('<img />').attr({
						'class': $toLoad.attr('class').replace(/(\s|^)smilie--lazyLoad(\s|$)/, ' '),
						alt: $toLoad.attr('data-alt'),
						title: $toLoad.attr('title'),
						src: $toLoad.attr('data-src'),
						'data-shortname': $toLoad.attr('data-shortname')
					});

					var replace = function()
					{
						var f = function()
						{
							$toLoad.replaceWith($image);
						};

						if (window.requestAnimationFrame)
						{
							window.requestAnimationFrame(f);
						}
						else
						{
							f();
						}
					};

					if (!$image.prop('complete'))
					{
						$image.on('load', replace);
					}
					else
					{
						replace();
					}
				}

				var timer;

				function performSearch()
				{
					var $input = $(this),
						$fullList = $menu.find('.js-emojiFullList'),
						$searchResults = $menu.find('.js-emojiSearchResults');

					clearTimeout(timer);

					timer = setTimeout(function()
					{
						var value = $input.val();

						if (!value || value.length < 2)
						{
							$searchResults.hide();
							$fullList.show();
							loadVisibleImages($fullList);
							return;
						}

						var url = XF.canonicalizeUrl('index.php?editor/smilies-emoji/search');
						XF.ajax('GET', url, {'q': value}, function(data)
						{
							if (!data.html)
							{
								return;
							}

							XF.setupHtmlInsert(data.html, function($html)
							{
								$html.find('.js-emoji').on('click', insertEmoji);

								$fullList.hide();
								$searchResults.replaceWith($html);
							});
						});
					}, 300);
				}

				function updateRecentEmoji()
				{
					var recent = XF.getRecentEmojiUsage(),
						$recentHeader = $menuScroll.find('.js-recentHeader'),
						$recentBlock = $menuScroll.find('.js-recentBlock'),
						$recentList = $recentBlock.find('.js-recentList'),
						$emojiLists = $menuScroll.find('.js-emojiList');

					if (!recent)
					{
						return;
					}

					var $newList = $recentList.clone(),
						newListArr = [];

					$newList.empty();

					for (var i in recent)
					{
						var shortname = recent[i],
							$emoji;

						$emojiLists.each(function()
						{
							var $list = $(this),
								$original = $list.find('.js-emoji[data-shortname="' + shortname + '"]').closest('li');
								$emoji = $original.clone();

							if ($emoji.length)
							{
								$emoji.find('.js-emoji').on('click', insertEmoji);
								newListArr.push($emoji);
								return false;
							}
						});
					}

					for (i in newListArr)
					{
						var $li = newListArr[i];
						$li.appendTo($newList);
					}

					$recentList.replaceWith($newList);

					if ($recentBlock.hasClass('is-hidden'))
					{
						$recentBlock.hide();
						$recentBlock.removeClass('is-hidden');
						$recentHeader.removeClass('is-hidden');
						$recentBlock.xfFadeDown(XF.config.speed.fast);
					}

					loadVisibleImages($newList);
				}

				function selectionSave()
				{
					editor.selection.save();
				}

				function selectionRestore()
				{
					editor.selection.restore();
				}

				return {
					showMenu: showMenu
				}
			};

			$.FE.DefineIcon('xfSmilie', { NAME: 'smile' });
			$.FE.RegisterCommand('xfSmilie', {
				title: 'Smilies',
				icon: 'xfSmilie',
				undo: false,
				focus: false,
				refreshOnCallback: false,
				callback: function()
				{
					this.xfSmilie.showMenu();
				}
			});

			$.FE.DefineIcon('xfDraft', { NAME: 'save' });
			$.FE.RegisterCommand('xfDraft', {
				type: 'dropdown',
				title: 'Drafts',
				focus: true,
				undo: false,
				options: {
					xfDraftSave: 'Save Draft',
					xfDraftDelete: 'Delete Draft'
				},
				callback: function(cmd, val)
				{
					var $form = this.$el.closest('form');
					if (!$form.length)
					{
						console.error('No parent form to find draft handler');
						return;
					}

					var draftHandler = XF.Element.getHandler($form, 'draft');
					if (!draftHandler)
					{
						console.error('No draft handler on parent form');
						return;
					}

					if (val == 'xfDraftSave')
					{
						draftHandler.triggerSave();
					}
					else if (val == 'xfDraftDelete')
					{
						draftHandler.triggerDelete();
					}
				}
			});

			$.extend($.FE.DEFAULTS, {
				xfBbCodeAttachmentContextInput: 'attachment_hash_combined'
			});
			$.FE.PLUGINS.bbCode = function(ed)
			{
				var _isBbCodeView = false;

				function getButton()
				{
					return ed.$tb.find('.fr-command[data-cmd=xfBbCode]');
				}

				function getBbCodeBox()
				{
					var $oel = ed.$oel;

					var $bbCodeBox = $oel.data('xfBbCodeBox');
					if (!$bbCodeBox)
					{
						var borderAdjust = parseInt(ed.$wp.css('border-bottom-width'), 10)
							+ parseInt(ed.$wp.css('border-top-width'), 10);

						$bbCodeBox = $('<textarea class="input" style="display: none" />');
						$bbCodeBox.attr('aria-label', XF.htmlspecialchars(XF.phrase('rich_text_box')));
						$bbCodeBox.css({
							minHeight: ed.opts.heightMin ? (ed.opts.heightMin + borderAdjust) + 'px' : null,
							maxHeight: ed.opts.heightMax ? ed.opts.heightMax + 'px' : null,
							height: ed.opts.height ? (ed.opts.height + borderAdjust) + 'px' : null,
							padding: ed.$el.css('padding')
						});
						$bbCodeBox.attr('name', $oel.data('original-name'));
						$oel.data('xfBbCodeBox', $bbCodeBox);
						ed.$wp.after($bbCodeBox);

						XF.Element.applyHandler($bbCodeBox, 'textarea-handler');
						XF.Element.applyHandler($bbCodeBox, 'user-mentioner');
						XF.Element.applyHandler($bbCodeBox, 'emoji-completer');
					}

					return $bbCodeBox;
				}

				function toBbCode(bbCode, skipFocus)
				{
					var $bbCodeBox = getBbCodeBox();

					var apply = function(bbCode, skipFocus)
					{
						_isBbCodeView = true;

						var $button;

						ed.undo.saveStep();
						ed.$el.blur();

						$button = getButton();

						ed.$tb.find(' > .fr-command').not($button).addClass('fr-disabled');
						$button.addClass('fr-active');

						ed.$wp.css('display', 'none');
						ed.$oel.prop('disabled', true);

						$bbCodeBox.val(bbCode)
							.css('display', '')
							.prop('disabled', false)
							.trigger('autosize');

						if (!skipFocus)
						{
							$bbCodeBox.autofocus();
						}

						XF.setIsEditorEnabled(false);
					};

					if (typeof bbCode == 'string')
					{
						apply(bbCode, skipFocus);
					}
					else
					{
						XF.ajax('POST',
							XF.canonicalizeUrl('index.php?editor/to-bb-code'),
							{ html: ed.html.get() },
							function (data) { apply(data.bbCode, skipFocus); }
						);
					}
				}

				function toHtml(html)
				{
					var $bbCodeBox = getBbCodeBox();

					var apply = function(html)
					{
						_isBbCodeView = false;

						var $button = getButton();

						ed.$tb.find(' > .fr-command').not($button).removeClass('fr-disabled');
						$button.removeClass('fr-active');

						ed.$oel.prop('disabled', false);
						ed.html.set(html);
						$bbCodeBox.css('display', 'none').prop('disabled', true);
						ed.$wp.css('display', '');
						ed.events.focus();
						ed.undo.saveStep();
						ed.size.refresh();

						XF.setIsEditorEnabled(true);
						XF.layoutChange();
					};

					if (typeof html == 'string')
					{
						apply(html);
					}
					else
					{
						var params = { bb_code: $bbCodeBox.val() };

						var $form = ed.$el.closest('form');
						if ($form.length)
						{
							if ($form[0][ed.opts.xfBbCodeAttachmentContextInput])
							{
								params.attachment_hash_combined = $($form[0][ed.opts.xfBbCodeAttachmentContextInput]).val();
							}
						}

						XF.ajax('POST',
							XF.canonicalizeUrl('index.php?editor/to-html'),
							params,
							function (data) { apply(data.editorHtml); }
						);
					}
				}

				function toggle()
				{
					if (_isBbCodeView)
					{
						toHtml();
					}
					else
					{
						toBbCode();
					}
				}

				function isBbCodeView()
				{
					return _isBbCodeView;
				}

				function insertBbCode(bbCode)
				{
					if (!_isBbCodeView)
					{
						return;
					}

					var $bbCodeBox = getBbCodeBox();
					XF.insertIntoTextBox($bbCodeBox, bbCode);
				}

				function replaceBbCode(bbCode)
				{
					if (!_isBbCodeView)
					{
						return;
					}

					var $bbCodeBox = getBbCodeBox();
					XF.replaceIntoTextBox($bbCodeBox, bbCode);
				}

				function getTextArea()
				{
					return (_isBbCodeView ? getBbCodeBox() : null);
				}

				function _init()
				{
					ed.events.on('buttons.refresh', function()
					{
						return !_isBbCodeView;
					});
				}

				return {
					_init: _init,
					toBbCode: toBbCode,
					isBbCodeView: isBbCodeView,
					getTextArea: getTextArea,
					insertBbCode: insertBbCode,
					replaceBbCode: replaceBbCode,
					toHtml: toHtml,
					toggle: toggle
				};
			};

			$.FE.DefineIcon('xfBbCode', { NAME: 'cog'});
			$.FE.RegisterCommand('xfBbCode', {
				title: 'Toggle BB Code',
				icon: 'xfBbCode',
				undo: false,
				focus: false,
				forcedRefresh: true,
				callback: function()
				{
					this.bbCode.toggle();
				}
			});
		},

		registerCustomCommands: function()
		{
			var custom;

			try
			{
				custom = $.parseJSON($('.js-editorCustom').first().html()) || {};
			}
			catch (e)
			{
				console.error(e);
				custom = {};
			}

			for (var tag in custom)
			{
				if (!custom.hasOwnProperty(tag))
				{
					continue;
				}

				(function(tag, def)
				{
					// make sure this matches with the disabler in XF\Service\User\SignatureEdit
					var name = 'xfCustom_' + tag,
						tagUpper = tag.toUpperCase(),
						template = {},
						faMatch;

					if (def.type == 'fa')
					{
						faMatch = def.value.match(/^fa([slrb]) fa-(.+)$/);
						if (faMatch)
						{
							template = {
								FA5NAME: faMatch[2],
								template: 'font_awesome_5' + (faMatch[1] === 's' ? '' : faMatch[1])
							};

						}
						else
						{
							template = { NAME: def.value };
						}
					}
					else if (def.type == 'image')
					{
						template = {
							template: 'image',
							SRC: '"' + XF.canonicalizeUrl(def.value) + '"',
							ALT: '"' + def.title + '"'
						};
					}

					$.FE.DefineIcon(name, template);
					$.FE.RegisterCommand(name, {
						title: def.title,
						icon: name,
						undo: true,
						focus: true,
						callback: function()
						{
							XF.EditorHelpers.wrapSelectionText(
								this,
								def.option == 'yes' ? '[' + tagUpper + '=]' : '[' + tagUpper + ']',
								'[/' + tagUpper + ']',
								true
							);
						}
					});

					XF.editorStart.custom.push(name);
				})(tag, custom[tag]);
			}
		},

		registerEditorDropdowns: function()
		{
			var editorDropdowns;

			try
			{
				editorDropdowns = $.parseJSON($('.js-editorDropdowns').first().html()) || {};
			}
			catch (e)
			{
				console.error("Editor dropdowns data not valid: ", e);
				editorDropdowns = {};
			}

			for (var cmd in editorDropdowns)
			{
				if (!editorDropdowns.hasOwnProperty(cmd))
				{
					continue;
				}

				(function(cmd, button)
				{
					// removes the fa- prefix which we use internally
					button.icon = button.icon.substr(3);

					$.FE.DefineIcon(cmd, { NAME: button.icon});
					$.FE.RegisterCommand(cmd, {
						type: 'dropdown',
						title: button.title,
						icon: cmd,
						undo: false,
						focus: false,
						html: function()
						{
							var o = '<ul class="fr-dropdown-list">',
								options = button.buttons,
								c, info;

							var editor = XF.getEditorInContainer(this.$oel);
							if (editor && editor.buttonManager)
							{
								// respect any removals if possible
								options = editor.buttonManager.getDropdown(cmd);
							}

							for (var i in options)
							{
								c = options[i];
								info = $.FE.COMMANDS[c];
								if (info)
								{
									o += '<li><a class="fr-command" data-cmd="' + c + '">' + this.icon.create(info.icon || c) + '&nbsp;&nbsp;' + this.language.translate(info.title) + '</a></li>';
								}
							}
							o += '</ul>';

							return o;
						}
					});
				})(cmd, editorDropdowns[cmd]);
			}
		},

		registerDialogs: function()
		{
			XF.EditorHelpers.dialogs.media = new XF.EditorDialogMedia('media');
			XF.EditorHelpers.dialogs.spoiler = new XF.EditorDialogSpoiler('spoiler');
			XF.EditorHelpers.dialogs.code = new XF.EditorDialogCode('code');
		}
	};

	$(document).one('editor:start', XF.editorStart.startAll);

	XF.EditorPlaceholderClick = XF.Event.newHandler({
		eventNameSpace: 'XFEditorPlaceholderClick',
		options: {},

		edInitialized: false,

		init: function()
		{
		},

		click: function(e)
		{
			var $target = this.$target,
				t = this;

			var displayEditor = function()
			{
				t.edInitialized = true;

				$target.find('.editorPlaceholder-editor').removeClass('is-hidden');
				$target.find('.editorPlaceholder-placeholder').addClass('is-hidden');
			};

			var editor = XF.getEditorInContainer($target);
			if (editor instanceof XF.Editor)
			{
				if (this.edInitialized)
				{
					return;
				}

				editor.startInit({
					beforeInit: displayEditor,
					afterInit: function(xfEd, froalaEd)
					{
						// initialized with a click so focus
						XF.EditorHelpers.focus(froalaEd);

						if (XF.isIOS())
						{
							xfEd.scrollToCursor();
							xfEd.scrollToCursorAfterPendingResize();
						}

						if (froalaEd.opts.tooltips)
						{
							setTimeout(function()
							{
								// hide any tooltips that appeared as a result of the editor loading
								// as clicks in the placeholder may place the cursor over a button
								// and trigger a tooltip.
								froalaEd.tooltip.hide();
							}, 30);
						}
					}
				});
			}
			else
			{
				displayEditor();
				if (editor instanceof $)
				{
					editor.focus();
				}
			}
		}
	});

	XF.Event.register('click', 'editor-placeholder', 'XF.EditorPlaceholderClick');

	XF.Element.register('editor', 'XF.Editor');
}
(jQuery, window, document);
