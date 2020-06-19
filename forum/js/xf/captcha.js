!function($, window, document, _undefined)
{
	"use strict";

	XF.KeyCaptcha = XF.Element.newHandler({

		options: {
			user: null,
			session: null,
			sign: null,
			sign2: null
		},

		$form: null,
		$code: null,

		init: function()
		{
			this.$form = this.$target.closest('form');
			this.$form.xfUniqueId();

			this.$code = this.$form.find('input[name=keycaptcha_code]');
			this.$code.xfUniqueId();

			this.load();
			this.$target.closest('form').on('ajax-submit:error ajax-submit:always', XF.proxy(this, 'reload'));
		},

		load: function()
		{
			if (window.s_s_c_onload)
			{
				this.create();
			}
			else
			{
				window.s_s_c_user_id = this.options.user;
				window.s_s_c_session_id = this.options.session;
				window.s_s_c_captcha_field_id = this.$code.attr('id');
				window.s_s_c_submit_button_id = 'sbutton-#-r';
				window.s_s_c_web_server_sign = this.options.sign;
				window.s_s_c_web_server_sign2 = this.options.sign2;
				document.s_s_c_element = this.$form[0];
				document.s_s_c_debugmode = 1;

				var $div = $('#div_for_keycaptcha');
				if (!$div.length)
				{
					$('body').append('<div id="div_for_keycaptcha" />');
				}

				$.ajax({
					url: 'https://backs.keycaptcha.com/swfs/cap.js',
					dataType: 'script',
					cache: true,
					global: false
				});
			}
		},

		create: function()
		{
			window.s_s_c_onload(this.$form.attr('id'), this.$code.attr('id'), 'sbutton-#-r');
		},

		reload: function(e)
		{
			if (!window.s_s_c_onload)
			{
				return;
			}

			if (!$(e.target).is('form'))
			{
				e.preventDefault();
			}
			this.load();
		}
	});

	XF.ReCaptcha = XF.Element.newHandler({

		options: {
			sitekey: null,
			invisible: null
		},

		$reCaptchaTarget: null,

		reCaptchaId: null,
		invisibleValidated: false,
		reloading: false,

		init: function()
		{
			if (!this.options.sitekey)
			{
				return;
			}

			var $form = this.$target.closest('form');

			if (this.options.invisible)
			{
				var $reCaptchaTarget = $('<div />'),
					$formRow = this.$target.closest('.formRow');

				$formRow.hide();
				$formRow.after($reCaptchaTarget);
				this.$reCaptchaTarget = $reCaptchaTarget;

				$form.on('ajax-submit:before', XF.proxy(this, 'beforeSubmit'));
			}
			else
			{
				this.$reCaptchaTarget = this.$target;
			}

			$form.on('ajax-submit:error ajax-submit:always', XF.proxy(this, 'reload'));

			if (window.grecaptcha)
			{
				this.create();
			}
			else
			{
				XF.ReCaptcha.Callbacks.push(XF.proxy(this, 'create'));

				$.ajax({
					url: 'https://www.google.com/recaptcha/api.js?onload=XFReCaptchaCallback&render=explicit',
					dataType: 'script',
					cache: true,
					global: false
				});
			}
		},

		create: function()
		{
			if (!window.grecaptcha)
			{
				return;
			}

			var options = {
				sitekey: this.options.sitekey
			};
			if (this.options.invisible)
			{
				options.size = 'invisible';
				options.callback = XF.proxy(this, 'complete');

			}
			this.reCaptchaId = grecaptcha.render(this.$reCaptchaTarget[0], options);
		},

		beforeSubmit: function(e, config)
		{
			if (!this.invisibleValidated)
			{
				e.preventDefault();
				config.preventSubmit = true;

				grecaptcha.execute();
			}
		},

		complete: function()
		{
			this.invisibleValidated = true;
			this.$target.closest('form').submit();
		},

		reload: function()
		{
			if (!window.grecaptcha || this.reCaptchaId === null || this.reloading)
			{
				return;
			}

			this.reloading = true;

			var self = this;
			setTimeout(function()
			{
				grecaptcha.reset(self.reCaptchaId);
				self.reloading = false;
				self.invisibleValidated = false;
			}, 50);
		}
	});
	XF.ReCaptcha.Callbacks = [];
	window.XFReCaptchaCallback = function()
	{
		var cb = XF.ReCaptcha.Callbacks;

		for (var i = 0; i < cb.length; i++)
		{
			cb[i]();
		}
	};

	XF.QaCaptcha = XF.Element.newHandler({

		options: {
			url: null
		},

		reloading: false,

		init: function()
		{
			if (!this.options.url)
			{
				return;
			}

			this.$target.closest('form').on('ajax-submit:error ajax-submit:always', XF.proxy(this, 'reload'));
		},

		reload: function()
		{
			if (this.reloading)
			{
				return;
			}

			this.reloading = true;

			this.$target.fadeTo(XF.config.speed.fast, 0.5);
			XF.ajax('get', this.options.url, XF.proxy(this, 'show'));
		},

		show: function(data)
		{
			var $target = this.$target,
				self = this;

			XF.setupHtmlInsert(data.html, function ($html, container, onComplete)
			{
				$html.hide();
				$target.after($html);

				$target.xfFadeUp(XF.config.speed.fast, function()
				{
					$html.xfFadeDown(XF.config.speed.fast);
					$target.remove();
				});

				self.reloading = false;
				onComplete();
			});
		}
	});

	XF.SolveCaptcha = XF.Element.newHandler({

		options: {
			ckey: null,
			theme: 'white'
		},

		instance: null,
		reloading: false,

		init: function()
		{
			if (this.instance)
			{
				this.instance.remove();
			}
			this.instance = this;

			if (!this.options.ckey)
			{
				return;
			}

			this.$target.closest('form').on('ajax-submit:error ajax-submit:always', XF.proxy(this, 'reload'));
			this.$target.siblings('noscript').remove();

			this.load();
			$(window).on('unload', XF.proxy(this, 'remove'));
		},

		load: function()
		{
			if (window.ACPuzzle)
			{
				this.create();
			}
			else
			{
				var prefix = window.location.protocol == 'https:' ? 'https://api-secure' : 'http://api';

				window.ACPuzzleOptions = {
					onload: XF.proxy(this, 'create')
				};
				$.ajax({
					url: prefix + '.solvemedia.com/papi/challenge.ajax',
					dataType: 'script',
					cache: true,
					global: false
				});
			}
		},

		create: function()
		{
			window.ACPuzzle.create(this.options.ckey, this.$target.attr('id'), {
				theme: this.options.theme || 'white',
				lang: $('html').attr('lang').substr(0, 2) || 'en'
			});
		},

		reload: function(e)
		{
			if (!window.ACPuzzle || this.reloading)
			{
				return;
			}

			//this.reloading = true;

			if (!$(e.target).is('form'))
			{
				e.preventDefault();
			}
			window.ACPuzzle.reload();

//			this.reloading = false;
		},

		remove: function()
		{
			this.$target.empty().remove();
			if (window.ACPuzzle)
			{
				window.ACPuzzle.destroy();
			}
		}
	});

	XF.Element.register('key-captcha', 'XF.KeyCaptcha');
	XF.Element.register('re-captcha', 'XF.ReCaptcha');
	XF.Element.register('qa-captcha', 'XF.QaCaptcha');
	XF.Element.register('solve-captcha', 'XF.SolveCaptcha');
}
(jQuery, window, document);