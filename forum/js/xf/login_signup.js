!function($, window, document, _undefined)
{
	"use strict";

	XF.AutoTimeZone = XF.Element.newHandler({

		init: function()
		{
			var now = new Date(),
				jan1 = new Date(now.getFullYear(), 0, 1), // 0 = jan
				jun1 = new Date(now.getFullYear(), 5, 1), // 5 = june
				jan1offset = Math.round(jan1.getTimezoneOffset()),
				jun1offset = Math.round(jun1.getTimezoneOffset());

			if (this.map[jan1offset + ',' + jun1offset])
			{
				this.$target.val(this.map[jan1offset + ',' + jun1offset]);
				return true;
			}
			else
			{
				return false;
			}
		},

		map:
		{
			'660,660' : 'Pacific/Midway',
			'600,600' : 'Pacific/Honolulu',
			'570,570' : 'Pacific/Marquesas',
			'540,480' : 'America/Anchorage',
			'480,420' : 'America/Los_Angeles',
			'420,360' : 'America/Denver',
			'420,420' : 'America/Phoenix',
			'360,300' : 'America/Chicago',
			'360,360' : 'America/Belize',
			'300,240' : 'America/New_York',
			'300,300' : 'America/Bogota',
			'270,270' : 'America/Caracas',
			'240,180' : 'America/Halifax',
			'180,240' : 'America/Cuiaba',
			'240,240' : 'America/La_Paz',
			'210,150' : 'America/St_Johns',
			'180,180' : 'America/Argentina/Buenos_Aires',
			'120,180' : 'America/Sao_Paulo',
			'180,120' : 'America/Miquelon',
			'120,120' : 'America/Noronha',
			'60,60' : 'Atlantic/Cape_Verde',
			'60,0' : 'Atlantic/Azores',
			'0,-60' : 'Europe/London',
			'0,0' : 'Atlantic/Reykjavik',
			'-60,-120' : 'Europe/Amsterdam',
			'-60,-60' : 'Africa/Algiers',
			'-120,-60' : 'Africa/Windhoek',
			'-120,-180' : 'Europe/Athens',
			'-120,-120' : 'Africa/Johannesburg',
			'-180,-240' : 'Africa/Nairobi',
			'-180,-180' : 'Africa/Nairobi',
			'-210,-270' : 'Asia/Tehran',
			'-240,-240' : 'Europe/Moscow',
			'-240,-300' : 'Asia/Yerevan',
			'-270,-270' : 'Asia/Kabul',
			'-300,-360' : 'Asia/Yekaterinburg',
			'-300,-300' : 'Asia/Tashkent',
			'-330,-330' : 'Asia/Kolkata',
			'-345,-345' : 'Asia/Kathmandu',
			'-360,-360' : 'Asia/Dhaka',
			'-360,-420' : 'Asia/Novosibirsk',
			'-390,-390' : 'Asia/Yangon',
			'-420,-420' : 'Asia/Bangkok',
			'-420,-480' : 'Asia/Krasnoyarsk',
			'-480,-480' : 'Asia/Hong_Kong',
			'-480,-540' : 'Asia/Irkutsk',
			'-540,-540' : 'Asia/Tokyo',
			'-540,-600' : 'Asia/Yakutsk',
			'-630,-570' : 'Australia/Adelaide',
			'-570,-570' : 'Australia/Darwin',
			'-660,-600' : 'Australia/Sydney',
			'-600,-660' : 'Asia/Vladivostok',
			'-660,-720' : 'Asia/Magadan',
			'-690,-690' : 'Pacific/Norfolk',
			'-780,-720' : 'Pacific/Auckland',
			'-825,-765' : 'Pacific/Chatham',
			'-780,-780' : 'Pacific/Tongatapu',
			'-840,-840' : 'Pacific/Kiritimati'
		}
	});

	XF.RegForm = XF.Element.newHandler({

		$submit: null,
		$disabler: null,
		$timerEl: null,
		$timerNum: null,

		timeLeft: 0,
		timerInterval: null,

		init: function()
		{
			var $form = this.$target.closest('form');

			this.$submit = $form.find('#js-signUpButton');
			if (!this.$submit.length)
			{
				return;
			}

			this.$disabler = $form.find('#js-signUpDisabler');
			if (!this.$disabler.length)
			{
				this.$disabler = null;
			}
			else
			{
				var f = XF.proxy(this, 'updateSubmitState');
				this.$disabler.click(function()
				{
					setTimeout(f, 0);
				});
			}

			this.$timerEl = $form.find('#js-regTimer');
			this.$timerNum = this.$timerEl.find('span');

			if (this.$timerNum.length)
			{
				this.timeLeft = parseInt(this.$timerNum.text(), 10);
				if (this.timeLeft > 0)
				{
					this.enableTimer();
				}
			}

			this.updateSubmitState();
		},

		enableTimer: function()
		{
			if (this.timeLeft <= 0 || !this.$timerNum.length)
			{
				return;
			}

			if (this.timerInterval)
			{
				clearInterval(this.timerInterval);
			}

			var self = this,
				$num = this.$timerNum;

			this.timerInterval = setInterval(function()
			{
				self.timeLeft--;
				if (self.timeLeft <= 0)
				{
					self.$submit.text(self.$timerEl.data('timer-complete'));
					clearInterval(self.timerInterval);
				}
				else
				{
					$num.text(self.timeLeft);
				}
				self.updateSubmitState();
			}, 1000);
		},

		updateSubmitState: function()
		{
			var timerComplete = (this.timeLeft <= 0),
				disablerMet = !this.$disabler || this.$disabler.prop('checked');

			this.$submit.prop('disabled', (!timerComplete || !disablerMet));
		}
	});

	XF.Element.register('auto-timezone', 'XF.AutoTimeZone');
	XF.Element.register('login-form', 'XF.LoginForm');
	XF.Element.register('reg-form', 'XF.RegForm');
}
(jQuery, window, document);