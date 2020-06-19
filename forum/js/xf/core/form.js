/** @param {jQuery} $ jQuery Object */
!function($, window, document)
{
	"use strict";

	// ################################## SUBMIT CHANGE HANDLER ###########################################

	XF.SubmitClick = XF.Event.newHandler({
		eventNameSpace: 'XFSubmitClick',
		options: {
			target: null,
			container: null,
			timeout: 500,
			uncheckedValue: '0',
			disable: null
		},

		$input: null,
		$form: null,

		init: function()
		{
			let $input = this.$target;

			if ($input.is('label'))
			{
				$input = $input.find('input:radio, input:checkbox');
				if (!$input.length)
				{
					return;
				}
			}

			this.$input = $input;

			let $form = $input.closest('form');
			this.$form = $form.length ? $form : null;
		},

		click: function(e)
		{
			let $input = this.$input,
				$form = this.$form,
				target = this.options.target,
				container = this.options.container;
			if (!$input)
			{
				return;
			}

			if (target)
			{
				let unchecked = this.options.uncheckedValue;

				setTimeout(function()
				{
					let data = {};

					if (container)
					{
						data = $input.closest(container).find('input, select, textarea').serializeArray();
					}
					else
					{
						data[$input.attr('name')] = $input.prop('checked') ? $input.attr('value') : unchecked;
					}

					XF.ajax('POST', target, data);
				}, 0);
			}
			else if ($form)
			{
				let timer = $form.data('submit-click-timer');
				if (timer)
				{
					clearTimeout(timer);
				}

				$form.one('ajax-submit:complete', function(e, data, submitter)
				{
					if (data.errors)
					{
						// undo the checked status change
						$input.prop('checked', ($input.prop('checked') ? '' : 'checked'));
					}
					else
					{
						// toggle 'dataList-row--disabled' for the parent dataList-row, if there is one
						if ($input.attr('type') == 'checkbox' && $input.closest('tr.dataList-row') !== null)
						{
							$input.closest('tr.dataList-row')[($input.prop('checked') ? 'removeClass' : 'addClass')]('dataList-row--disabled');
						}
					}
				});

				timer = setTimeout(function()
				{
					$form.submit();
				}, this.options.timeout);

				$form.data('submit-click-timer', timer);
			}
			else
			{
				console.error('No target or form to submit on click');
			}
		}
	});

	// ################################## AJAX FORM SUBMISSION ###########################################

	XF.AjaxSubmit = XF.Element.newHandler({
		options: {
			redirect: true,
			skipOverlayRedirect: false,
			forceFlashMessage: false,
			resetComplete: false,
			hideOverlay: true,
			disableSubmit: '.button, :submit, :reset, [data-disable-submit]',
			jsonName: null,
			jsonOptIn: null,
			replace: null,
			showReplacement: true
		},

		submitPending: false,
		$submitButton: null,

		init: function()
		{
			var $form = this.$target;

			if (!$form.is('form'))
			{
				console.error('%o is not a form', $form[0]);
				return;
			}

			$form.on({
				submit: XF.proxy(this, 'submit'),
				keyup: XF.proxy(this, 'cmdEnterKey'),
				'draft:beforesave': XF.proxy(this, 'draftCheck')
			});
			$form.on('click', 'input[type=submit], button:not([type]), button[type=submit]', XF.proxy(this, 'submitButtonClicked'));
		},

		submit: function(e)
		{
			var $submitButton = this.$submitButton,
				$form = this.$target,
				isUploadForm = $form.attr('enctype') == 'multipart/form-data';

			if (isUploadForm)
			{
				if (this.options.jsonName)
				{
					// JSON encoding would try to encode the upload which will break it, so prevent submission and error.
					e.preventDefault();
					console.error('JSON serialized forms do not support the file upload-style enctype.');
					XF.alert(XF.phrase('oops_we_ran_into_some_problems_more_details_console'));
					return;
				}

				if (!window.FormData)
				{
					// This is an upload type form and the browser cannot support AJAX submission for this.
					return;
				}
			}

			if (this.$submitButton && this.$submitButton.data('prevent-ajax'))
			{
				return;
			}

			if (XF.debug.disableAjaxSubmit)
			{
				return;
			}

			if (this.submitPending)
			{
				if (e)
				{
					e.preventDefault();
				}
				return;
			}

			var ajaxOptions = { skipDefault: true };
			if (isUploadForm)
			{
				ajaxOptions.timeout = 0;
			}

			var event = $.Event('ajax-submit:before'),
				config = {
					form: $form,
					handler: this,
					method: $form.attr('method') || 'get',
					action: $form.attr('action'),
					submitButton: $submitButton,
					preventSubmit: false,
					successCallback: XF.proxy(this, 'submitResponse'),
					ajaxOptions: ajaxOptions
				};

			$form.trigger(event, config);

			if (config.preventSubmit)
			{
				// preventing any submit
				return false;
			}
			if (event.isDefaultPrevented())
			{
				// preventing ajax submission
				return true;
			}

			if (e)
			{
				e.preventDefault();
			}

			var t = this;

			// do this in a timeout to ensure that all other submit handlers run
			setTimeout(function()
			{
				t.submitPending = true;

				var formData = XF.getDefaultFormData($form, $submitButton, t.options.jsonName, t.options.jsonOptIn);

				t.disableButtons();

				XF.ajax(
					config.method,
					config.action,
					formData,
					config.successCallback,
					config.ajaxOptions
				).always(function()
				{
					t.$submitButton = null;

					// delay re-enable slightly to allow animation to potentially happen
					setTimeout(function()
					{
						t.submitPending = false;
						t.enableButtons();
					}, 300);

					event = $.Event('ajax-submit:always');
					$form.trigger(event, $form, t);
				});
			}, 0);
		},

		disableButtons: function()
		{
			this.$target.find(this.options.disableSubmit).prop('disabled', true);
		},

		enableButtons: function()
		{
			this.$target.find(this.options.disableSubmit).prop('disabled', false);
		},

		submitResponse: function(data, status, xhr)
		{
			if (typeof data != 'object')
			{
				XF.alert('Response was not JSON.');
				return;
			}

			var $form = this.$target,
				$submitButton = this.$submitButton;

			var event = $.Event('ajax-submit:response');
			$form.trigger(event, data, this);
			if (event.isDefaultPrevented())
			{
				return;
			}

			var errorEvent = $.Event('ajax-submit:error'),
				hasError = false,
				doRedirect = data.redirect && this.options.redirect,
				$overlay = $form.closest('.overlay');

			if (!$overlay.length || !this.options.hideOverlay)
			{
				$overlay = null;
			}

			if (doRedirect && this.options.skipOverlayRedirect && $overlay)
			{
				doRedirect = false;
			}

			if ($submitButton && $submitButton.attr('data-ajax-redirect'))
			{
				doRedirect = $submitButton.data('ajax-redirect');
			}

			if (data.errorHtml)
			{
				$form.trigger(errorEvent, data, this);
				if (!errorEvent.isDefaultPrevented())
				{
					XF.setupHtmlInsert(data.errorHtml, function($html, container)
					{
						var title = container.h1 || container.title || XF.phrase('oops_we_ran_into_some_problems');
						XF.overlayMessage(title, $html);
					});
				}

				hasError = true;
			}
			else if (data.errors)
			{
				$form.trigger(errorEvent, data, this);
				if (!errorEvent.isDefaultPrevented())
				{
					XF.alert(data.errors);
				}

				hasError = true;
			}
			else if (data.exception)
			{
				XF.alert(data.exception);
			}
			else if (data.status == 'ok' && data.message)
			{
				if (doRedirect)
				{
					if (this.options.forceFlashMessage)
					{
						XF.flashMessage(data.message, 1000, function()
						{
							XF.redirect(data.redirect);
						});
					}
					else
					{
						XF.redirect(data.redirect);
					}
				}
				else
				{
					XF.flashMessage(data.message, 3000);
				}

				if ($overlay)
				{
					$overlay.trigger('overlay:hide');
				}
			}
			else if (data.html)
			{
				var self = this;

				XF.setupHtmlInsert(data.html, function($html, container, onComplete)
				{
					if (self.options.replace && self.doSubmitReplace($html, onComplete))
					{
						return false; // handle on complete when finished
					}

					if ($overlay)
					{
						$overlay.trigger('overlay:hide');
					}

					var $childOverlay = XF.getOverlayHtml({
						html: $html,
						title: container.h1 || container.title
					});
					XF.showOverlay($childOverlay);
				});
			}
			else if (data.status == 'ok')
			{
				if (doRedirect)
				{
					XF.redirect(data.redirect);
				}

				if ($overlay)
				{
					$overlay.trigger('overlay:hide');
				}
			}

			if (data.errors && !errorEvent.isDefaultPrevented())
			{
				// TODO: tie to individual fields?
			}

			event = $.Event('ajax-submit:complete');
			$form.trigger(event, data, this);
			if (event.isDefaultPrevented())
			{
				return;
			}

			if (this.options.resetComplete && !hasError)
			{
				$form[0].reset();
			}
		},

		doSubmitReplace: function($html, onComplete)
		{
			var replace = this.options.replace;

			if (!replace)
			{
				return false;
			}

			var parts = replace.split(' with '),
				selectorOld = $.trim(parts[0]),
				selectorNew = parts[1] ? $.trim(parts[1]) : selectorOld,
				$old, $new;

			if (selectorOld == 'self' || this.$target.is(selectorOld))
			{
				$old = this.$target;
			}
			else
			{
				$old = this.$target.find(selectorOld).first();
				if (!$old.length)
				{
					$old = $(selectorOld).first();
				}
			}

			if (!$old.length)
			{
				console.error("Could not find old selector '" + selectorOld + "'");
				return false;
			}

			if ($html.is(selectorNew))
			{
				$new = $html;
			}
			else
			{
				$new = $html.find(selectorNew).first();
			}

			if (!$new.length)
			{
				console.error("Could not find new selector '" + selectorNew + "'");
				return false;
			}

			if (this.options.showReplacement)
			{
				$new.hide().insertAfter($old);
				$old.xfFadeUp(null, function()
				{
					$old.remove();

					if ($new.length)
					{
						XF.activate($new);
						onComplete(false);
					}

					$new.xfFadeDown(null, XF.layoutChange);
				});
			}
			else
			{
				$new.insertAfter($old);
				$old.remove();
				if ($new.length)
				{
					XF.activate($new);
					onComplete(false);
				}
				XF.layoutChange();
			}


			return true;
		},

		submitButtonClicked: function(e)
		{
			this.$submitButton = $(e.currentTarget);
		},

		draftCheck: function(e)
		{
			if (this.submitPending)
			{
				e.preventDefault();
			}
		}
	});

	// ################################## SUBMIT FORM ON CHANGE ###########################################

	XF.ChangeSubmit = XF.Element.newHandler({

		options: {
			toggleElement: null
		},

		formInitialized: false,
		hasChanges: false,
		$toggle: null,

		init: function()
		{
			this.$target.find(':input').on('change', XF.proxy(this, 'change'));

			this.$target.find('.js-revert').on('click', XF.proxy(this, 'revert'));

			// WiP
			//$(this.options.toggleElement).on('toggle:hidden', XF.proxy(this, 'complete'));
		},

		initForm: function()
		{
			if (!this.formInitialized)
			{
				XF.Element.applyHandler(this.$target, 'ajax-submit',
				{
					redirect: false,
					forceFlashMessage: false
				});

				// make double-sure...
				XF.Element.getHandler(this.$target, 'ajax-submit').options['redirect'] = false;
			}

			this.formInitialized = true;
		},

		change: function(e)
		{
			this.initForm();
			this.$target.trigger('submit');
			this.hasChanges = true;
		},

		revert: function(e)
		{
			if (this.hasChanges)
			{
				this.$target.trigger('reset').trigger('submit');

				this.hasChanges = false;
			}
		},

		complete: function(e)
		{

		}
	});

	// ################################## AUTO COMPLETE ###########################################

	XF.AutoComplete = XF.Element.newHandler({
		loadTimer: null,
		loadVal: '',
		results: null,

		options: {
			single: false,
			multiple: ',', // multiple value joiner (used if single == true)
			acurl: '',
			minLength: 2, // min word length before lookup
			queryKey: 'q',
			extraFields: '',
			extraParams: {},
			jsonContainer: 'results',
			autosubmit: false
		},

		init: function()
		{
			var $input = this.$target;

			if (this.options.autosubmit)
			{
				this.options.single = true;
			}

			if (!this.options.acurl)
			{
				this.options.acurl = XF.getAutoCompleteUrl();
			}

			this.results = new XF.AutoCompleteResults({
				onInsert: XF.proxy(this, 'addValue')
			});

			$input.attr('autocomplete', 'off')
				.on({
					keydown: XF.proxy(this, 'keydown'),
					keyup: XF.proxy(this, 'keyup'),
					'blur click': XF.proxy(this, 'blur'),
					paste: function()
					{
						setTimeout(function() { $input.trigger('keydown'); }, 0);
					}
				});

			$input.closest('form').submit(XF.proxy(this, 'hideResults'))
		},

		keydown: function(e)
		{
			if (!this.results.isVisible())
			{
				return;
			}

			var results = this.results,
				prevent = function() { e.preventDefault(); return false; };

			switch (e.key)
			{
				case 'ArrowDown':
					results.selectResult(1);
					return prevent();

				case 'ArrowUp':
					results.selectResult(-1);
					return prevent();

				case 'Escape':
					this.hide();
					return prevent();

				case 'Enter':
					results.insertSelectedResult();
					return prevent();
			}
		},

		keyup: function(e)
		{
			if (this.results.isVisible())
			{
				switch (e.key)
				{
					case 'ArrowDown':
					case 'ArrowUp':
					case 'Enter':
						return;
				}
			}

			this.hideResults();

			if (this.loadTimer)
			{
				clearTimeout(this.loadTimer);
			}
			this.loadTimer = setTimeout(XF.proxy(this, 'load'), 200);
		},

		blur: function(e)
		{
			clearTimeout(this.loadTimer);

			// timeout ensures that clicks still register
			setTimeout(XF.proxy(this, 'hideResults'), 250);

			if (this.xhr)
			{
				this.xhr.abort();
				this.xhr = false;
			}
		},

		load: function()
		{
			var lastLoad = this.loadVal,
				params = this.options.extraParams;

			if (this.loadTimer)
			{
				clearTimeout(this.loadTimer);
			}

			this.loadVal = this.getPartialValue();

			if (this.loadVal == '')
			{
				this.hideResults();
				return;
			}

			if (this.loadVal == lastLoad)
			{
				return;
			}

			if (this.loadVal.length < this.options.minLength)
			{
				return;
			}

			params[this.options.queryKey] = this.loadVal;

			if (this.options.extraFields != '')
			{
				$(this.options.extraFields).each(function()
				{
					params[this.name] = $(this).val();
				});
			}

			if (this.xhr)
			{
				this.xhr.abort();
			}

			this.xhr = XF.ajax(
				'get',
				this.options.acurl,
				params,
				XF.proxy(this, 'showResults'),
				{ global: false, error: false }
			);
		},

		hideResults: function()
		{
			this.results.hideResults();
		},

		showResults: function(results)
		{
			if (this.xhr)
			{
				this.xhr = false;
			}

			if (this.options.jsonContainer && results)
			{
				results = results[this.options.jsonContainer];
			}

			this.results.showResults(this.getPartialValue(), results, this.$target);
		},

		addValue: function(value)
		{
			if (this.options.single)
			{
				this.$target.val(value);
			}
			else
			{
				var values = this.getFullValues();
				if (value != '')
				{
					if (values.length)
					{
						value = ' ' + value;
					}
					values.push(value + this.options.multiple + ' ');
				}
				this.$target.val(values.join(this.options.multiple));
			}

			this.$target
				.trigger('change')
				.trigger('auto-complete:insert', {inserted: $.trim(value), current: this.$target.val()});

			if (this.options.autosubmit)
			{
				this.$target.closest('form').submit();
			}
			else
			{
				this.$target.autofocus();
			}
		},

		getFullValues: function()
		{
			var val = this.$target.val(),
				splitPos = '';

			if (val == '')
			{
				return [];
			}

			if (this.options.single)
			{
				return [val];
			}
			else
			{
				splitPos = val.lastIndexOf(this.options.multiple);
				if (splitPos == -1)
				{
					return [];
				}
				else
				{
					val = val.substr(0, splitPos);
					return val.split(this.options.multiple);
				}
			}
		},

		getPartialValue: function()
		{
			var val = this.$target.val(),
				splitPos;

			if (this.options.single)
			{
				return $.trim(val);
			}
			else
			{
				splitPos = val.lastIndexOf(this.options.multiple);
				if (splitPos == -1)
				{
					return $.trim(val);
				}
				else
				{
					return $.trim(val.substr(splitPos + this.options.multiple.length));
				}
			}
		}
	});

	// ################################## USER MENTIONER ###########################################

	XF.UserMentioner = XF.Element.newHandler({
		options: {},

		handler: null,

		init: function()
		{
			this.handler = new XF.AutoCompleter(this.$target, { url: XF.getAutoCompleteUrl() });
		}
	});

	// ################################## EMOJI COMPLETER ###########################################

	XF.EmojiCompleter = XF.Element.newHandler({
		options: {
			insertTemplate: '${text}'
		},

		handler: null,

		init: function()
		{
			if (!XF.config.shortcodeToEmoji)
			{
				return;
			}

			var emojiHandlerOpts = {
				url: XF.canonicalizeUrl('index.php?misc/find-emoji'),
				at: ':',
				keepAt: false,
				insertMode: 'text',
				displayTemplate: '<div class="contentRow">' +
					'<div class="contentRow-figure contentRow-figure--emoji">{{{icon}}}</div>' +
					'<div class="contentRow-main contentRow-main--close">{{{text}}}' +
					'<div class="contentRow-minor contentRow-minor--smaller">{{{desc}}}</div></div>' +
					'</div>',
				beforeInsert: function(value)
				{
					XF.logRecentEmojiUsage(value);

					return value;
				}
			};
			this.handler = new XF.AutoCompleter(
				this.$target, emojiHandlerOpts
			);
		}
	});

	// ################################## AUTO SUBMIT ###########################################

	XF.AutoSubmit = XF.Element.newHandler({

		options: {
			hide: true,
			progress: true
		},

		init: function()
		{
			this.$target.submit();

			if (this.options.hide)
			{
				this.$target.find(':submit').hide();
			}
			if (this.options.progress)
			{
				$(document).trigger('xf:action-start');
			}
		}
	});

	// ################################## CHANGED FIELD ###########################################

	XF.ChangedFieldNotifier = XF.Element.newHandler({

		options: {
			hide: true,
			progress: true
		},

		init: function ()
		{
			this.$target.find('input, select, textarea').each(function()
			{
				var $el = $(this);
				$el.data('orig-val', $el.val());

				$el.change(function()
				{
					$el.toggleClass('is-changed', ($el.val() != $el.data('orig-val')));
				})
			});
		}
	});

	// ################################## CHECK ALL HANDLER ###########################################

	XF.CheckAll = XF.Element.newHandler({
		options: {
			container: '< form',
			match: 'input:checkbox'
		},

		$container: null,
		updating: false,

		init: function()
		{
			this.$container = XF.findRelativeIf(this.options.container, this.$target);

			var t = this;
			this.$container.on('click', this.options.match, function(e)
			{
				if (t.updating)
				{
					return;
				}

				var $target = $(e.target);
				if ($target.is(t.$target))
				{
					return;
				}

				t.updateState();
			});

			this.$target.closest('form').on('selectplus:redrawSelected', XF.proxy(this, 'updateState'));

			this.updateState();

			this.$target.click(XF.proxy(this, 'click'));
		},

		click: function(e)
		{
			this.updating = true;
			this.getCheckBoxes().prop('checked', e.target.checked).triggerHandler('click');
			this.updating = false;
		},

		updateState: function()
		{
			var $checkboxes = this.getCheckBoxes(),
				allSelected = $checkboxes.length > 0;

			$checkboxes.each(function() {
				if (!$(this).prop('checked'))
				{
					allSelected = false;
					return false;
				}
			});

			this.$target.prop('checked', allSelected);
		},

		getCheckBoxes: function()
		{
			return this.$container.find(this.options.match).not(this.$target);
		}
	});

	// ################################## SELECT PLUS HANDLER ###########################################

	XF.SelectPlus = XF.Element.newHandler({
		options: {
			// optional selector for checkboxes within the target
			spCheckbox: null,

			// checkbox ancestor that will receive .is-selected and .is-hover-selected classes
			spContainer: '.js-spContainer',

			// class to apply to the target when multi-selection is active
			activeClass: 'is-spActive',

			// class to apply to spContainers when the contained checkbox is checked
			checkedClass: 'is-spChecked',

			// class to apply to spContainers when the contained checkbox is part of a hovered potential selection
			hoverClass: 'is-spHovered',

			// URL to an action that will provide actionBar HTML
			spMultiBarUrl: null,

			// enable debug mode
			spDebug: true
		},

		$containers: null,
		$checkboxes: null,

		$multiBar: null,

		isActive: false,
		isShifted: false,

		lastSelected: null,
		lastEntered: null,

		init: function()
		{
			this.$checkboxes = this.$target.find(this.options.spCheckbox ? this.options.spCheckbox : 'input:checkbox');

			this.$containers = this.$checkboxes.closest(this.options.spContainer);

			this.debug('init; containers: %o, checkboxes: %o',
				this.$containers.length,
				this.$checkboxes.length);

			if (this.$containers.length != this.$checkboxes.length)
			{
				console.error("There must be an equal number of checkboxes and containers");
				return;
			}

			this.$checkboxes
				.on('click', XF.proxy(this, 'checkboxClick'))
				.closest('label').hover(XF.proxy(this, 'checkboxEnter'), XF.proxy(this, 'checkboxExit'));

			// TODO: check touch events?

			$(document).onPassive(
			{
				keydown: XF.proxy(this, 'keydown'),
				keyup: XF.proxy(this, 'keyup')
			});

			// This workaround prevents shift-selection from selecting label text
			// @see https://stackoverflow.com/questions/1527751/disable-text-selection-while-pressing-shift
			var self = this;
			this.$containers.on('mousedown', function(e)
			{
				if (self.isActive && (e.ctrlKey || e.shiftKey))
				{
					e.preventDefault();

					if (navigator.userAgent.indexOf('MSIE') !== -1)
					{
						this.onselectstart = function () { return false; };
						var me = this;  // capture in a closure
						window.setTimeout(function () { me.onselectstart = null; }, 0);
					}
				}
			});

			// set initial states
			this.setActive();
			this.redrawSelected();
		},

		// Event handlers

		checkboxClick: function(e)
		{
			if (this.ignoreClick)
			{
				// so that we can run 'click' on shift-selected items without it mucking everything else up
				return;
			}

			this.debug('checkboxClick; delegateTarget: %o', e.delegateTarget);

			var index = this.$checkboxes.index(e.delegateTarget);

			if (e.delegateTarget.checked && this.isShifted && this.lastSelected !== null)
			{
				this.ignoreClick = true;
				this.getShiftItems(this.$checkboxes, index).not(':checked').trigger('click');
				this.ignoreClick = false;
			}
			else
			{
				this.lastSelected = e.delegateTarget.checked ? index : null;
			}

			this.setActive(e.delegateTarget.checked);
			this.redrawSelected();
		},

		checkboxExit: function(e)
		{
			this.lastEntered = null;
		},

		checkboxEnter: function(e)
		{
			if (this.isActive)
			{
				// get the index of the checkbox contained within the target <label>
				this.lastEntered = this.$checkboxes.index($(e.delegateTarget).find('input:checkbox').eq(0));

				if (this.isShifted)
				{
					this.redrawHover();
				}
			}
		},

		keydown: function(e)
		{
			if (e.key == 'Shift' && XF.Keyboard.isShortcutAllowed(document.activeElement))
			{
				this.isShifted = true;
				this.redrawHover();
			}
		},

		keyup: function(e)
		{
			if (e.key == 'Shift' && this.isShifted)
			{
				this.isShifted = false;
				this.redrawHover();
			}
		},

		// Methods

		getShiftItems: function($items, index)
		{
			if (index !== null && this.lastSelected !== null)
			{
				var $items = $items.slice(Math.min(index, this.lastSelected), Math.max(index, this.lastSelected) + 1);

				this.debug('shiftItems: %o', $items);

				return $items;
			}

			return $();
		},

		setActive: function(forceActive)
		{
			var previouslyActive = this.isActive;

			this.isActive = forceActive
				? true
				: this.$checkboxes.filter(':checked').length > 0;

			this.deployMultiBar();

			if (this.isActive != previouslyActive)
			{
				this.debug('setActive: %s', this.isActive);

				this.$target
					.trigger(this.isActive ? 'selectplus:activate' : 'selectplus:deactivate', [this])
					.toggleClassTransitioned(this.options.activeClass, this.isActive);

				$(document.body).toggleClassTransitioned('is-spDocTriggered', this.isActive);
			}
		},

		redrawSelected: function()
		{
			this.$target.trigger('selectplus:redraw-selected', [this]);

			var self = this;

			this.$checkboxes.each(function (i)
			{
				var $this = $(this),
					newCheckState = $this.is(':checked'),
					$container = self.$containers.eq(i);

				$container.toggleClassTransitioned(self.options.checkedClass, newCheckState);

				if ($this.data('check-state') != newCheckState)
				{
					$container.trigger('selectplus:toggle-item', [this, newCheckState])
				}

				$this.data('check-state', newCheckState);
			});
		},

		redrawHover: function()
		{
			this.$target.trigger('selectplus:redraw-hover', [this]);

			if (this.lastSelected !== null && this.lastEntered !== null && this.isShifted)
			{
				var $hovered = this.getShiftItems(this.$containers, this.lastEntered);

				this.debug('redrawHover: lastSelected: %s, lastEntered: %s', this.lastSelected, this.lastEntered);

				this.$containers.not($hovered).toggleClass(this.options.hoverClass, false);

				$hovered.toggleClassTransitioned(this.options.hoverClass, true);
			}
			else
			{
				this.$containers.toggleClassTransitioned(this.options.hoverClass, false);
			}
		},

		deployMultiBar: function()
		{
			if (this.isActive && this.options.spMultiBarUrl)
			{
				var self = this;
				XF.loadMultiBar(this.options.spMultiBarUrl, this.$checkboxes.serializeArray(),
				{
					cache: false,
					init: function(MultiBar)
					{
						if (self.MultiBar)
						{
							self.MultiBar.destroy();
						}
						self.MultiBar = MultiBar;
					}
				}, { fastReplace: (self.MultiBar ? true : false) });
			}
			else if (!this.active && this.MultiBar)
			{
				this.MultiBar.hide();
			}
		},

		debug: function()
		{
			if (this.options.spDebug)
			{
				arguments[0] = 'SelectPlus:' + arguments[0];
				console.log.apply(null, arguments);
			}
		}
	});

	// ################################## DATE INPUT HANDLER ###########################################

	XF.DateInput = XF.Element.newHandler({
		options: {
			weekStart: 0,
			minDate: null,
			maxDate: null,
			disableWeekends: false,
			yearRange: null,
			showWeekNumber: false,
			showDaysInNextAndPreviousMonths: true
		},

		picker: null,

		init: function()
		{
			var minDate = this.options.minDate,
				maxDate = this.options.maxDate;
			if (minDate)
			{
				var minTime = Date.parse(minDate.replace(/-/g, '/'));
				minDate = new Date(minTime);
			}
			if (maxDate)
			{
				var maxTime = Date.parse(maxDate.replace(/-/g, '/'));
				maxDate = new Date(maxTime);
			}

			var self = this,
				$target = this.$target,
				initialValue = $target.val(),
				config = {
					onSelect: function()
					{
						var pad = function(number)
						{
							if (number < 10) { return '0' + number; }
							return number;
						};
						var date = this._d,
							day = String(date.getDate()),
							month = String(date.getMonth() + 1),
							year = String(date.getFullYear());

						self.$target.val(year + '-' + pad(month) + '-' + pad(day));
					},
					onOpen: function()
					{
						if ($target.prop('readonly'))
						{
							this.hide();
						}
					},
					showTime: false,
					firstDay: this.options.weekStart,
					minDate: minDate,
					maxDate: maxDate,
					disableWeekends: this.options.disableWeekends,
					yearRange: this.options.yearRange,
					showWeekNumber: this.options.showWeekNumber,
					showDaysInNextAndPreviousMonths: this.options.showDaysInNextAndPreviousMonths,
					i18n: {
						previousMonth : '',
						nextMonth     : '',
						weekdays      : [0, 1, 2, 3, 4, 5, 6].map(function(day){ return XF.phrase('day' + day) }),
						weekdaysShort : [0, 1, 2, 3, 4, 5, 6].map(function(day){ return XF.phrase('dayShort' + day) }),
						months        : [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11].map(function(month){ return XF.phrase('month' + month) })
					},
					isRTL: XF.isRtl(),
					field: this.$target[0]
				};

			if (initialValue)
			{
				// Pikaday uses Date.parse() internally which parses yyyy-mm-dd unexpectedly when in UTC-X timezones.
				// This works around that issue.
				var match = initialValue.match(/^(\d{4})-(\d\d?)-(\d\d?)$/);
				if (match)
				{
					config.defaultDate = new Date(parseInt(match[1], 10), parseInt(match[2], 10) - 1, parseInt(match[3]));
					config.setDefaultDate = true;
				}
			}

			this.picker = new Pikaday(config);
			this.$target.val(initialValue);

			var $trigger = this.$target.parent().find('.js-dateTrigger');
			if ($trigger.length)
			{
				$trigger.on('click', function()
				{
					self.picker.show();
				});
			}
		}
	});

	// ################################## DESC LOADER HANDLER ###########################################

	XF.DescLoader = XF.Element.newHandler({
		options: {
			descUrl: null
		},

		$container: null,
		changeTimer: null,
		xhr: null,

		init: function()
		{
			if (!this.options.descUrl)
			{
				console.error('Element must have a data-desc-url value');
				return;
			}

			var $container = this.$target.parent().find('.js-descTarget');
			if (!$container.length)
			{
				console.error('Target element must have a .js-descTarget sibling');
				return;
			}
			this.$container = $container;

			this.$target.on('change', XF.proxy(this, 'change'));
		},

		change: function()
		{
			if (this.changeTimer)
			{
				clearTimeout(this.changeTimer);
			}

			if (this.xhr)
			{
				this.xhr.abort();
				this.xhr = null;
			}

			this.changeTimer = setTimeout(XF.proxy(this, 'onTimer'), 200);
		},

		onTimer: function()
		{
			var value = this.$target.val();

			if (!value)
			{
				this.$container.xfFadeUp(XF.config.speed.fast);
				return;
			}

			this.xhr = XF.ajax('post', this.options.descUrl, { id: value }, XF.proxy(this, 'onLoad'));
		},

		onLoad: function(data)
		{
			var $container = this.$container;

			if (data.description)
			{
				XF.setupHtmlInsert(data.description, function($html, container, onComplete)
				{
					$container.xfFadeUp(XF.config.speed.fast, function()
					{
						$container.html($html);
						$container.xfFadeDown(XF.config.speed.normal);
					});
				});
			}
			else
			{
				$container.xfFadeUp(XF.config.speed.fast);
			}

			this.xhr = null;
		}
	});

	// ################################## CONTROL DISABLER HANDLER ###########################################

	XF.Disabler = XF.Element.newHandler({
		options: {
			container: '< li | ul, ol, dl',
			controls: 'input, select, textarea, button, .js-attachmentUpload',
			hide: false,
			optional: false,
			invert: false, // if true, system will disable on checked
			autofocus: true
		},

		$container: null,

		init: function()
		{
			this.$container = XF.findRelativeIf(this.options.container, this.$target);

			if (!this.$container.length)
			{
				if (!this.options.optional)
				{
					console.error('Could not find the disabler control container');
				}
			}

			var $input = this.$target,
				$form = $input.closest('form');
			if ($form.length)
			{
				$form.on('reset', XF.proxy(this, 'formReset'));
			}

			if ($input.is(':radio'))
			{
				var $context = $form,
					name = $input.attr('name');
				if (!$form.length)
				{
					$context = $(document.body);
				}

				// radios only fire events for the element we click normally, so we need to know
				// when we move away from the value by firing every radio's handler for every click
				$context.on('click', 'input:radio[name="' + name + '"]', XF.proxy(this, 'click'));
			}
			else if ($input.is('option'))
			{
				var self = this;
				var $select = $input.closest('select');
				$select.on('change', function(e)
				{
					var $this = $(this);
					var $handler = XF.Element.getHandler($this.find('option:selected').first(), 'disabler');

					if (!$this.find('option:selected').first().is(self.$target) && $handler && $handler.getOption('container') === self.options.container)
					{
						return;
					}

					self.recalculate(false);
				});
			}
			else
			{
				$input.click(XF.proxy(this, 'click'));
			}

			// this ensures that nested disablers are disabled properly
			$input.on('control:enabled control:disabled', XF.proxy(this, 'recalculateAfter'));

			// this ensures that dependent editors are initialised properly as disabled if needed
			this.$container.one('editor:init', XF.proxy(this, 'recalculateAfter'));

			this.recalculate(true);
		},

		click: function(e, options)
		{
			var noSelect = (options && options.triggered);
			this.recalculateAfter(false, noSelect);
		},

		formReset: function(e)
		{
			this.recalculateAfter(false, true);
		},

		recalculateAfter: function(init, noSelect)
		{
			var t = this;
			setTimeout(function()
			{
				t.recalculate(init, noSelect);
			}, 0);
		},

		recalculate: function(init, noSelect)
		{
			var $container = this.$container,
				$input = this.$target,
				$controls = $container.find(this.options.controls).not($input),
				speed = init ? 0 : XF.config.speed.fast,
				enable = $input.is(':enabled') && (($input.is(':checked') && !this.options.invert) || (this.options.invert && !$input.is(':checked'))),
				t = this,
				select = function()
				{
					if (noSelect || !t.options.autofocus)
					{
						return;
					}

					$container.find('input:not([type=hidden], [type=file]), textarea, select, button').not($input)
						.first().autofocus();
				};

			if (enable)
			{
				$container
					.prop('disabled', false)
					.removeClass('is-disabled');

				$controls
					.prop('disabled', false)
					.removeClass('is-disabled')
					.each(function(i, ctrl)
					{
						var $ctrl = $(ctrl);

						if ($ctrl.is('select.is-readonly'))
						{
							// readonly has to be implemented through disabling so we can't undisable this
							$ctrl.prop('disabled', true);
						}
					})
					.trigger('control:enabled');

				if (this.options.hide)
				{
					if (init)
					{
						$container.show();
					}
					else
					{
						var cb = function()
						{
							XF.layoutChange();
							select();
						};

						$container.slideDown(speed, cb);
					}
					XF.layoutChange();
				}
				else if (!init)
				{
					select();
				}
			}
			else
			{
				if (this.options.hide)
				{
					if (init)
					{
						$container.hide();
					}
					else
					{
						$container.slideUp(speed, XF.layoutChange);
					}
					XF.layoutChange();
				}

				$container
					.prop('disabled', true)
					.addClass('is-disabled');

				$controls
					.prop('disabled', true)
					.addClass('is-disabled')
					.trigger('control:disabled')
					.each(function(i, ctrl)
					{
						var $ctrl = $(ctrl),
							disabledVal = $ctrl.data('disabled');

						if (disabledVal !== null && typeof(disabledVal) != 'undefined')
						{
							$ctrl.val(disabledVal);
						}
					});
			}
		}
	});

	// ################################## FIELD ADDER ###########################################

	XF.FieldAdder = XF.Element.newHandler({

		options: {
			incrementFormat: null,
			formatCaret: true,
			removeClass: null,
			remaining: -1
		},

		$clone: null,
		cloned: false,
		created: false,

		init: function()
		{
			// Clear the cached values of any child elements (except checkboxes)
			this.$target.find('input:not(:checkbox), select, textarea').each(function()
			{
				var $el = $(this);
				if ($el.is('select'))
				{
					$el.find('option').each(function()
					{
						$(this).prop('selected', this.defaultSelected);
					});
				}
				else
				{
					$el.val($el.data('default-value') || this.defaultValue || '');
				}
			});

			this.$clone = this.$target.clone();

			var self = this;
			this.$target.on('keypress change paste input', function(e)
			{
				if ($(e.target).prop('readonly') || self.cloned)
				{
					return;
				}

				// self.$clone = self.$target.clone();
				self.cloned = true;
				self.$target.off(e);
				self.create();
			});
		},

		create: function()
		{
			if (this.created)
			{
				return;
			}

			this.created = true;

			if (this.options.remaining == 0)
			{
				return;
			}

			var incrementFormat = this.options.incrementFormat,
				caret = (this.options.formatCaret ? '^' : '');

			if (this.options.incrementFormat)
			{
				var incrementRegex = new RegExp(caret + XF.regexQuote(incrementFormat).replace('\\{counter\\}', '(\\d+)'));

				this.$clone.find('input, select, textarea').each(function()
				{
					var $this = $(this),
						name = $this.attr('name');

					name = name.replace(incrementRegex, function(prefix, counter)
					{
						return incrementFormat.replace('{counter}', parseInt(counter, 10) + 1);
					});

					$this.attr('name', name);
				});
			}

			if (this.options.remaining > 0)
			{
				this.$clone.attr('data-remaining', this.options.remaining - 1);
			}

			this.$clone.find('input, select, textarea').each(function()
			{
				var $input = $(this);

				if ($input.is('select'))
				{
					$input.find('option').each(function()
					{
						$(this).prop('selected', this.defaultSelected);
					});
				}
				else if (typeof this.defaultValue === 'string')
				{
					$input.val(this.defaultValue);
				}
			});

			this.$clone.insertAfter(this.$target);

			if (this.options.removeClass)
			{
				this.$target.removeClass(this.options.removeClass);
			}

			XF.activate(this.$clone);
			XF.layoutChange();
		}
	});

	// ################################## FORM SUBMIT ROWS ###########################################

	XF.FormSubmitRow = XF.Element.newHandler({
		options: {
			container: '.block-container',
			fixedChild: '.formSubmitRow-main',
			stickyClass: 'is-sticky',
			topOffset: 100,
			minWindowHeight: 281
		},

		$container: null,
		$fixedParent: null,
		$fixEl: null,
		fixElHeight: 0,
		winHeight: 0,
		containerTop: 0,
		containerBorderLeftWidth: 0,
		topOffset: 0,
		elBottom: 0,
		state: 'normal',
		windowTooSmall: false,

		init: function()
		{
			if (!XF.config.enableFormSubmitSticky)
			{
				return;
			}

			var $target = this.$target,
				$container = $target.closest(this.options.container);
			if (!$container.length)
			{
				console.error('Cannot float submit row, no container');
				return;
			}

			this.$container = $container;

			this.topOffset = this.options.topOffset;
			this.$fixEl = $target.find(this.options.fixedChild);

			$(window).on('scroll', XF.proxy(this, 'onScroll'))
				.on('resize', XF.proxy(this, 'recalcAndUpdate'));

			var $fixedParent = XF.getFixedOffsetParent($target);
			if (!$fixedParent.is('html'))
			{
				this.$fixedParent = $fixedParent;
				$fixedParent.on('scroll', XF.proxy(this, 'onScroll'));
			}

			$(document.body).on('xf:layout', XF.proxy(this, 'recalcAndUpdate'));

			if (!$target.height())
			{
				setTimeout(XF.proxy(this, 'recalcAndUpdate'), 250);
			}

			this.recalcAndUpdate();
		},

		recalc: function()
		{
			var $target = this.$target;

			this.winHeight = $(window).height();
			this.elBottom = this.getTargetTop() + $target.height();
			this.fixElHeight = this.$fixEl.height();
			this.containerTop = XF.getFixedOffset(this.$container).top;
			this.containerBorderLeftWidth = parseInt(this.$container.css('border-left-width'), 10);
		},

		recalcAndUpdate: function()
		{
			this.state = 'normal'; // need to force CSS updates
			this.resetTarget();
			this.recalc();
			this.update();
		},

		getTargetTop: function()
		{
			var top = this.$target.offset().top;

			if (this.$fixedParent)
			{
				return top - this.$fixedParent.offset().top;
			}
			else
			{
				return top;
			}
		},

		getScrollTop: function()
		{
			if (this.$fixedParent)
			{
				return this.$fixedParent.scrollTop();
			}
			else
			{
				return $(window).scrollTop();
			}
		},

		update: function()
		{
			// in iOS and Android, scrolling may conditionally show/hide UI elements without triggering a
			// resize event. window.innerHeight represents the size of the viewport that shows the page.
			var winHeight = (XF.browser.ios || XF.browser.android) ? window.innerHeight : this.winHeight;

			if (winHeight < this.options.minWindowHeight)
			{
				if (this.state != 'normal')
				{
					this.resetTarget();
					this.state = 'normal';
				}
				return;
			}

			var containerOffset, bottomFixHeight = $('.js-bottomFixTarget').height() || 0;

			var screenBottom = this.getScrollTop() + winHeight - bottomFixHeight;
			if (screenBottom >= this.elBottom)
			{
				// screen is past the end of the element, natural position
				if (this.state != 'normal')
				{
					this.resetTarget();
					this.state = 'normal';
				}
				return;
			}

			var absoluteCutOff = this.containerTop + this.topOffset + this.fixElHeight;

			if (screenBottom <= absoluteCutOff)
			{
				if (absoluteCutOff >= this.elBottom)
				{
					return;
				}

				// screen is above container
				if (this.state != 'absolute')
				{
					containerOffset = this.$container.offset();

					var $offsetParent;
					if (this.state == 'stuck')
					{
						// when fixed, the offset parent is the HTML element
						$offsetParent = this.$fixEl.parent();
						if ($offsetParent.css('position') == 'static')
						{
							$offsetParent = $offsetParent.offsetParent();
						}
					}
					else
					{
						$offsetParent = this.$fixEl.offsetParent();
					}
					var offsetParentOffset = $offsetParent.offset();

					this.$fixEl.css({
						position: 'absolute',
						top: containerOffset.top - offsetParentOffset.top + this.topOffset,
						right: 'auto',
						bottom: 'auto',
						left: containerOffset.left - offsetParentOffset.left + this.containerBorderLeftWidth,
						width: this.$container.width()
					});
					this.setTargetSticky(true);
					this.state = 'absolute';
				}

				return;
			}

			// screen ends within the container
			if (this.state != 'stuck')
			{
				containerOffset = this.$container.offset();

				this.$fixEl.css({
					position: '',
					top: '',
					right: '',
					bottom: bottomFixHeight,
					left: containerOffset.left + this.containerBorderLeftWidth,
					width: this.$container.width()
				});
				this.setTargetSticky(true);
				this.state = 'stuck';
			}
		},

		resetTarget: function()
		{
			this.$fixEl.css({
				position: '',
				top: '',
				right: '',
				bottom: '',
				left: '',
				width: ''
			});
			this.setTargetSticky(false);
		},

		setTargetSticky: function(sticky)
		{
			var $target = this.$target;

			$target
				.toggleClass(this.options.stickyClass, sticky)
				.css('height', this.$fixEl.height());
		},

		onScroll: function()
		{
			this.update();
		}
	});

	// ################################## GUEST USERNAME HANDLER ###########################################

	XF.GuestUsername = XF.Element.newHandler({

		init: function()
		{
			var $input = this.$target;
			$input.val(XF.LocalStorage.get('guestUsername'));
			$input.on('keyup', XF.proxy(this, 'change'));
		},

		change: function()
		{
			var $input = this.$target;
			if ($input.val().length)
			{
				XF.LocalStorage.set('guestUsername', $input.val(), true);
			}
			else
			{
				XF.LocalStorage.remove('guestUsername');
			}
		}
	});

	// ################################## MIN LENGTH ###########################################

	XF.MinLength = XF.Element.newHandler({
		options: {
			minLength: 0,
			allowEmpty: false,
			disableSubmit: true,
			toggleTarget: null
		},

		met: null,
		$form: null,
		$toggleTarget: null,

		init: function()
		{
			var self = this;

			this.$form = this.$target.closest('form');

			if (this.options.toggleTarget)
			{
				this.$toggleTarget = XF.findRelativeIf(this.options.toggleTarget, this.$target);
			}
			else
			{
				this.$toggleTarget = $([]);
			}

			this.$target.on('change keypress keydown paste', function()
			{
				setTimeout(XF.proxy(self, 'checkLimits'), 0);
			});

			this.checkLimits();
		},

		checkLimits: function()
		{
			var length = $.trim(this.$target.val()).length,
				options = this.options,
				met = (length >= options.minLength || (length == 0 && options.allowEmpty));

			if (met === this.met)
			{
				return;
			}
			this.met = met;

			if (met)
			{
				if (options.disableSubmit)
				{
					this.$form.find(':submit').prop('disabled', false).removeClass('is-disabled');
				}
				this.$toggleTarget.hide();
			}
			else
			{
				if (options.disableSubmit)
				{
					this.$form.find(':submit').prop('disabled', true).addClass('is-disabled');
				}
				this.$toggleTarget.show();
			}
		}
	});

	// ################################## TEXTAREA HANDLER ###########################################

	XF.TextAreaHandler = XF.Element.newHandler({
		options: {
			autoSize: true,
			keySubmit: true,
			singleLine: null // if 'next', focus next element on enter, otherwise submit on enter
		},

		initialized: false,

		init: function()
		{
			if (this.options.autoSize)
			{
				if (this.$target[0].scrollHeight)
				{
					this.setupAutoSize();
				}
				else
				{
					this.$target.one('focus control:enabled control:disabled', XF.proxy(this, 'setupDelayed'));
					this.$target.onWithin('toggle:shown overlay:shown tab:shown quick-edit:shown', XF.proxy(this, 'setupDelayed'));
				}

				this.$target.on('autosize', XF.proxy(this, 'update'));
			}

			if (this.options.keySubmit || this.options.singleLine)
			{
				this.$target.on('keydown', XF.proxy(this, 'keySubmit'));
			}
		},

		setupAutoSize: function()
		{
			if (this.initialized)
			{
				return;
			}
			this.initialized = true;

			autosize(this.$target);

			this.$target.on('autosize:resized', function()
			{
				XF.layoutChange();
			});
		},

		setupDelayed: function()
		{
			if (this.initialized)
			{
				this.update();
			}
			else
			{
				var t = this,
					init = function()
					{
						t.setupAutoSize();
						XF.layoutChange();
					};

				if (this.$target[0].scrollHeight)
				{
					init();
				}
				else
				{
					setTimeout(init, 100);
				}
			}
		},

		update: function()
		{
			if (this.initialized)
			{
				autosize.update(this.$target[0]);
			}
			else
			{
				this.setupDelayed();
			}
		},

		keySubmit: function(e)
		{
			if (e.key == 'Enter')
			{
				if (this.options.singleLine || (this.options.keySubmit && (XF.isMac() ? e.metaKey : e.ctrlKey)))
				{
					switch (String(this.options.singleLine).toLowerCase())
					{
						case 'next':
							this.$target.focusNext();
							break;

						case 'blur':
							this.$target.blur();
							break;

						default:
							this.$target.closest('form').submit();
					}

					e.preventDefault();
					return false;
				}
			}
		}
	});

	// ################################# TEXT EDIT - SINGLE VALUE EDITOR #########################################

	XF.TextEdit = XF.Event.newHandler({
		eventType: 'focus',
		eventNameSpace: 'XFTextEdit',
		options: {
			editUrl: null,
			escapeRevert: true
		},

		processing: false,

		init: function()
		{
			if (this.options.editUrl === null)
			{
				console.warn('TextEdit must specify data-edit-url');
				return;
			}

			if (this.options.escapeRevert)
			{
				this.$target.data('original-text', this.$target.val());
				this.$target.onPassive('keyup', XF.proxy(this, 'keyEscape'));
			}

			this.$target.on('change', XF.proxy(this, 'save'));
		},

		focus: function(e)
		{
		},

		save: function(e)
		{
			if (this.processing)
			{
				return;
			}
			this.processing = true;

			var self = this,
				formData = {};

			formData[this.$target.attr('name')] = this.$target.val();

			XF.ajax('POST', this.options.editUrl, formData, XF.proxy(this, 'success')
			).always(function()
			{
				setTimeout(function()
				{
					self.processing = false;
				}, 250);
			});
		},

		success: function(data)
		{
			var newText = data[this.$target.attr('name')];

			this.$target.val(newText).data('original-text', newText);
		},

		keyEscape: function(e)
		{
			if (e.key == 'Escape')
			{
				this.$target.val(this.$target.data('original-text'));
			}
		}
	});

	// ################################## PERMISSION MATRIX UI HANDLER ###########################################

	XF.PermissionMatrix = XF.Element.newHandler({
		options: {
			inputSelector: 'input[type="radio"]',
			parentSelector: 'dl.formRow',
			classPrefix: 'formRow--permissionType-',
			permissionType: 'user'
		},

		values: ['allow', 'unset', 'deny', 'content_allow', 'reset'],
		currentClass: null,

		init: function()
		{
			// identify parent row, to which classes will be attached
			this.$parentRow = this.$target.closest(this.options.parentSelector);

			this.$target.find(this.options.inputSelector).on('click', XF.proxy(this, 'update'));
			this.update();
		},

		update: function()
		{
			// remove any existing permission classes
			if (this.currentClass)
			{
				this.$parentRow.removeClass(this.currentClass);
			}

			// get the currently selected value
			var value = this.$target.find(this.options.inputSelector + ':checked').val();

			// if it's a valid value, assign the class to the target
			if ($.inArray(value, this.values) > -1)
			{
				this.currentClass = this.options.classPrefix + this.options.permissionType + '-' + value;
				this.$parentRow.addClass(this.currentClass);
			}
		}
	});

	// ################################## MULTI-INPUT CHECKER ###########################################
	// TODO: this can be more generic in due course, see it in use in the permission editors

	XF.MultiCheck = XF.Event.newHandler({
		eventNameSpace: 'XFMultiCheck',
		options: {
			target: null, // selector for all inputs
			values: 'allow,unset,deny' // possible input values to cycle
		},

		$target: null,
		values: null,
		currentValue: null,

		init: function ()
		{
			this.$target = $(this.options.target);

			this.values = this.options.values.split(',');

			// if the first radio is checked, start the cycle with the second, otherwise start with the first
			var firstChecked = this.$target.filter(':checked').first().val(),
				firstIndex = $.inArray(firstChecked, this.values);
			this.currentValue = (firstIndex > 0 ? this.values[-1] : this.values[0]);
		},

		click: function(e)
		{
			var currentValue = this.values[$.inArray(this.currentValue, this.values) + 1];

			if (currentValue === undefined)
			{
				currentValue = this.values[0];
			}

			this.$target.each(function()
			{
				if ($(this).val() == currentValue)
				{
					$(this).prop('checked', true).trigger('click');
				}
			});

			this.currentValue = currentValue;
		}
	});

	// ################################## NUMBER BOX HANDLER ###########################################

	XF.NumberBox = XF.Element.newHandler({
		options: {
			textInput: '.js-numberBoxTextInput',
			buttonSmaller: false,
			step: null
		},

		$textInput: null,

		holdTimeout: null,
		holdInterval: null,

		init: function()
		{
			var $target = this.$target,
				$textInput = $target.find(this.options.textInput);

			if (!$textInput.length)
			{
				console.error('Cannot initialize, no text input.');
				return;
			}

			this.$textInput = $textInput;

			$target.addClass('inputGroup--joined');

			var $up = $target.find('.js-up'),
				$down = $target.find('.js-down');

			if (!$up.length)
			{
				$up = this.createButton('up');
			}
			if (!$down.length)
			{
				$down = this.createButton('down');
			}

			this.setupButton($up, $textInput);
			this.setupButton($down, $up);

			if (!this.supportsStepFunctions())
			{
				$textInput.on('keydown', XF.proxy(this, 'stepFallback'));
			}
		},

		createButton: function(dir)
		{
			var $button = $('<button />')
				.attr('type', 'button')
				.attr('tabindex', '-1')
				.addClass('inputGroup-text')
				.addClass('inputNumber-button')
				.addClass('inputNumber-button--' + dir)
				.addClass('js-' + dir)
				.attr('data-dir', dir);

			if (this.$textInput.prop('disabled'))
			{
				$button.addClass('is-disabled').prop('disabled', true);
			}

			if (this.options.buttonSmaller)
			{
				$button.addClass('inputNumber-button--smaller');
			}

			return $button;
		},

		setupButton: function($button, $insertRef)
		{
			$button
				.on('focus', XF.proxy(this, 'buttonFocus'))
				.on('click', XF.proxy(this, 'buttonClick'))
				.on('mousedown touchstart', XF.proxy(this, 'buttonMouseDown'))
				.on('mouseleave mouseup touchend', XF.proxy(this, 'buttonMouseUp'))
				.on('touchend', function(e)
				{
					e.preventDefault();

					// this prevents double tap zoom on touch devices
					$(this).click();
				})
				.insertAfter($insertRef);
		},

		buttonFocus: function(e)
		{
			return false;
		},

		buttonClick: function(e)
		{
			this.step($(e.target).data('dir'));
		},

		step: function(dir)
		{
			var $textInput = this.$textInput,
				fnName = 'step' + dir.charAt(0).toUpperCase() + dir.slice(1);

			if ($textInput.prop('readonly'))
			{
				return;
			}

			if (this.supportsStepFunctions())
			{
				try
				{
					if ($textInput.val() === '')
					{
						$textInput.val($textInput.attr('min') || 0);
					}
					$textInput[0][fnName]();
					$textInput.trigger('change').trigger('input');
				}
				catch (e) {}
			}
			else
			{
				this.stepFallback(dir);
			}
		},

		stepFallback: function(dir)
		{
			if (this.$textInput.prop('readonly'))
			{
				return;
			}

			if (typeof dir === 'object' && dir.keyCode)
			{
				var e = dir;

				switch (e.keyCode)
				{
					case 38:

						dir = 'up';
						e.preventDefault();
						break;

					case 40:

						dir = 'down';
						e.preventDefault();
						break;

					default:
						return;
				}
			}

			var $textInput = this.$textInput,
				originalVal = $textInput.val(),
				multiplier = (dir === 'down' ? -1 : 1),
				min = $textInput.attr('min') || null,
				max = $textInput.attr('max') || null,
				step = this.options.step || $textInput.attr('step') || 1,
				stepAny = (step == 'any'),
				value;

			if (stepAny)
			{
				step = 1;
			}
			step = parseFloat(step);

			value = parseFloat(originalVal) + (step * multiplier);

			// force some rounding if appropriate to the same number of places as the step
			if (Math.round(value) !== value)
			{
				var decs = 0,
					splitVal = originalVal.split('.');

				if (stepAny)
				{
					if (splitVal[1])
					{
						decs = splitVal[1].length;
					}
					else
					{
						decs = 0;
					}
				}
				else if (Math.floor(step) !== step)
				{
					decs = step.toString().split('.')[1].length || 0;
				}
				value = value.toFixed(decs);
			}

			if (min !== null && value < min)
			{
				value = min;
			}
			if (max !== null && value > max)
			{
				value = max;
			}

			if (isNaN(value))
			{
				value = 0;
			}

			$textInput.val(value);
			$textInput.trigger('change').trigger('input');
		},

		buttonMouseDown: function(e)
		{
			this.buttonMouseUp(e);

			this.holdTimeout = setTimeout(
				XF.proxy(function()
				{
					this.holdInterval = setInterval(
						XF.proxy(function()
						{
							this.step($(e.target).data('dir'));
						}, this
					), 75);
				}, this
			), 500);
		},

		buttonMouseUp: function(e)
		{
			clearTimeout(this.holdTimeout);
			clearInterval(this.holdInterval);
		},

		supportsStepFunctions: function()
		{
			var $textInput = this.$textInput;

			if (XF.browser.msie)
			{
				// IE *thinks* it supports them, but doesn't...
				return false;
			}

			if ($textInput.prop('disabled') || $textInput.prop('readonly'))
			{
				return false;
			}

			if (this.$target.data('step') || $textInput.attr('step') === 'any')
			{
				// the stepUp/stepDown methods fail if step = any
				return false;
			}

			// Check we have stepUp and stepDown support else fallback
			return typeof $textInput[0]['stepUp'] === 'function'
				&& typeof $textInput[0]['stepDown'] === 'function';
		}
	});

	// ################################## PASSWORD HIDE/SHOW HANDLER ###########################################

	XF.PasswordHideShow = XF.Element.newHandler({
		options: {
			showText: null,
			hideText: null
		},

		$password: null,
		$checkbox: null,
		$label: null,

		init: function()
		{
			this.$password = this.$target.find('.js-password');

			var $container = this.$target.find('.js-hideShowContainer');
			this.$checkbox = $container.find('input[type="checkbox"]');
			this.$label = $container.find('.iconic-label');

			this.$checkbox.on('change', XF.proxy(this, 'toggle'));
		},

		toggle: function(e)
		{
			var $checkbox = this.$checkbox,
				$password = this.$password,
				$label = this.$label;

			if ($checkbox.is(':checked'))
			{
				$password.attr('type', 'text');
				$label.html(this.options.hideText);
			}
			else
			{
				$password.attr('type', 'password');
				$label.html(this.options.showText);
			}
		}
	});

	// ################################## CHECKBOXES DISABLE SELECT OPTIONS ###########################################
	// Using this, checkbox values correspond to <option> values in the <select> selected by this.options.select,
	// and if the checkbox is not checked, the corresponding <option> will be disabled

	XF.CheckboxSelectDisabler = XF.Element.newHandler({
		options: {
			select: null
		},

		$select: null,
		$checkboxes: null,

		init: function()
		{
			this.$select = XF.findRelativeIf(this.options.select, this.$target);
			if (!this.$select || !this.$select.length)
			{
				console.warn('No select element found using %s', this.options.select);
				return;
			}

			this.$checkboxes = this.$target.find(':checkbox').on('click', XF.proxy(this, 'update'));

			this.update();
		},

		update: function()
		{
			var $select = this.$select;

			this.$checkboxes.each(function()
			{
				var $option = $select.find('option[value=' + this.value + ']').prop('disabled', !this.checked);
				if (!this.checked && $option.is(':selected'))
				{
					$select.find('option:enabled').first().prop('selected', true);
				}
			});
		}
	});

	/**
	 * Focus the next focusable element in the document after el
	 *
	 * @param el
	 */
	XF.focusNext = function(el)
	{
		if (!el instanceof $)
		{
			el = $(el);
		}

		var $focusable = $('a, button, :input, [tabindex]'),
			focusIndex = $focusable.index(el) + 1;

		$focusable.eq(focusIndex >= $focusable.length ? 0 : focusIndex).focus();
	};

	// ################################## --- ###########################################

	XF.Event.register('click', 'submit', 'XF.SubmitClick');
	XF.Event.register('click', 'multi-check', 'XF.MultiCheck');

	XF.Event.register('focus', 'text-edit', 'XF.TextEdit');

	XF.Element.register('ajax-submit', 'XF.AjaxSubmit');
	XF.Element.register('auto-complete', 'XF.AutoComplete');
	XF.Element.register('user-mentioner', 'XF.UserMentioner');
	XF.Element.register('emoji-completer', 'XF.EmojiCompleter');
	XF.Element.register('auto-submit', 'XF.AutoSubmit');
	XF.Element.register('changed-field-notifier', 'XF.ChangedFieldNotifier');
	XF.Element.register('check-all', 'XF.CheckAll');
	XF.Element.register('select-plus', 'XF.SelectPlus');
	XF.Element.register('date-input', 'XF.DateInput');
	XF.Element.register('desc-loader', 'XF.DescLoader');
	XF.Element.register('disabler', 'XF.Disabler');
	XF.Element.register('field-adder', 'XF.FieldAdder');
	XF.Element.register('form-submit-row', 'XF.FormSubmitRow');
	XF.Element.register('guest-username', 'XF.GuestUsername');
	XF.Element.register('min-length', 'XF.MinLength');
	XF.Element.register('textarea-handler', 'XF.TextAreaHandler');
	XF.Element.register('permission-matrix', 'XF.PermissionMatrix');
	XF.Element.register('number-box', 'XF.NumberBox');
	XF.Element.register('checkbox-select-disabler', 'XF.CheckboxSelectDisabler');
	XF.Element.register('password-hide-show', 'XF.PasswordHideShow');
	XF.Element.register('change-submit', 'XF.ChangeSubmit');

}
(jQuery, window, document);
