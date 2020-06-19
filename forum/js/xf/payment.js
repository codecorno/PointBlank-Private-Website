!function($, window, document, _undefined)
{
	"use strict";

	XF.PaymentProviderContainer = XF.Element.newHandler({

		options: {},

		init: function ()
		{
			if (!this.$target.is('form'))
			{
				console.error('%o is not a form', this.$target[0]);
				return;
			}

			this.$target.on('ajax-submit:response', XF.proxy(this, 'submitResponse'));
		},

		submitResponse: function(event, response)
		{
			if (response.providerHtml)
			{
				event.preventDefault();

				var $replyContainer = this.$target.parent().find('.js-paymentProviderReply-'
					+ response.purchasableTypeId
					+ response.purchasableId
				);

				XF.setupHtmlInsert(response.providerHtml, function($html, container)
				{
					$replyContainer.html($html);
				});
			}
		}
	});

	XF.BraintreePaymentForm = XF.Element.newHandler({

		options: {
			clientToken: null,
			formStyles: '.js-formStyles'
		},

		xhr: null,

		init: function ()
		{
			this.$target.on('submit', XF.proxy(this, 'submit'));

			var urls = [
				'https://js.braintreegateway.com/web/3.19.0/js/client.min.js',
				'https://js.braintreegateway.com/web/3.19.0/js/hosted-fields.min.js'
			];
			XF.loadScripts(urls, XF.proxy(this, 'postInit'));

			var overlay = this.$target.closest('.overlay-container').data('overlay');
			overlay.on('overlay:hidden', function()
			{
				overlay.destroy();
			});
		},

		postInit: function()
		{
			if (!this.options.clientToken)
			{
				console.error('Form must contain a data-client-token attribute.');
				return;
			}

			var self = this,
				$styleData = this.$target.find(this.options.formStyles) || {},
				style = $styleData ? $.parseJSON($styleData.first().html()) : {},
				options = {
					authorization: this.options.clientToken
				};

			braintree.client.create(options, function(clientErr, clientInstance)
			{
				if (clientErr)
				{
					XF.alert(clientErr.message);
					return;
				}

				var options = {
					client: clientInstance,
					styles: style,
					fields: {
						number: {
							selector: '#card-number',
							placeholder: '1234 1234 1234 1234'
						},
						expirationDate: {
							selector: '#card-expiry',
							placeholder: 'MM / YY'
						},
						cvv: {
							selector: '#card-cvv',
							placeholder: 'CVC'
						}
					}
				};
				braintree.hostedFields.create(options, function (hostedFieldsErr, hostedFieldsInstance)
				{
					if (hostedFieldsErr)
					{
						XF.alert(hostedFieldsErr.message);
						return;
					}

					var fields = hostedFieldsInstance._fields;
					for (var key in fields)
					{
						if (fields.hasOwnProperty(key))
						{
							var $elem = $(fields[key]['containerElement']);
							$elem.removeClass('is-disabled');
						}
					}

					hostedFieldsInstance.on('cardTypeChange', function(e)
					{
						var brand = (e.cards.length === 1 ? e.cards[0].type : 'unknown'),
							brandClasses = {
								'visa': 'fa-cc-visa',
								'master-card': 'fa-cc-mastercard',
								'american-express': 'fa-cc-amex',
								'discover': 'fa-cc-discover',
								'diners-club': 'fa-cc-diners',
								'jcb': 'fa-cc-jcb',
								'unionpay': 'fa-credit-card-alt',
								'maestro' : 'fa-credit-card-alt',
								'unknown': 'fa-credit-card-alt'
							};

						if (brand)
						{
							var $brandIconElement = $('#brand-icon'),
								faClass = 'fa-credit-card-alt';

							if (brand in brandClasses)
							{
								faClass = brandClasses[brand];
							}

							$brandIconElement[0].className = '';
							$brandIconElement.addClass('fa');
							$brandIconElement.addClass('fa-lg');
							$brandIconElement.addClass(faClass);
						}
					});

					var $form = self.$target;
					$form.on('submit', function(e)
					{
						e.preventDefault();

						hostedFieldsInstance.tokenize(function (tokenizeErr, payload)
						{
							if (tokenizeErr)
							{
								var message = tokenizeErr.message,
									invalidKeys = tokenizeErr.details.invalidFieldKeys;
								if (invalidKeys)
								{
									message += ' (' + invalidKeys.join(', ') + ')';
								}

								XF.alert(message);
								return;
							}

							self.response(payload);
						});
					});
				});
			});
		},

		submit: function(e)
		{
			e.preventDefault();
			return false;
		},

		response: function(object)
		{
			if (this.xhr)
			{
				this.xhr.abort();
			}

			this.xhr = XF.ajax('post', this.$target.attr('action'), object, XF.proxy(this, 'complete'), { skipDefaultSuccess: true });
		},

		complete: function(data)
		{
			this.xhr = null;

			if (data.redirect)
			{
				XF.redirect(data.redirect);
			}
		}
	});

	XF.BraintreeApplePayForm = XF.Element.newHandler({

		options: {
			clientToken: null,
			currencyCode: '',
			boardTitle: '',
			title: '',
			amount: ''
		},

		xhr: null,

		init: function ()
		{
			var urls = [
				'https://js.braintreegateway.com/web/3.19.0/js/client.min.js',
				'https://js.braintreegateway.com/web/3.19.0/js/apple-pay.min.js'
			];
			XF.loadScripts(urls, XF.proxy(this, 'postInit'));
		},

		postInit: function()
		{
			if (!this.options.clientToken)
			{
				console.error('Form must contain a data-client-token attribute.');
				return;
			}

			var self = this,
				canMakePayments = false;
			if (window.ApplePaySession && ApplePaySession.canMakePayments())
			{
				canMakePayments = true;
			}

			if (!canMakePayments)
			{
				return;
			}

			braintree.client.create({ authorization: this.options.clientToken }, function(clientErr, clientInstance)
			{
				if (clientErr)
				{
					XF.alert(clientErr.message);
					return;
				}

				braintree.applePay.create({ client: clientInstance }, function(applePayErr, applePayInstance)
				{
					if (applePayErr)
					{
						XF.alert(applePayErr.message);
						return;
					}

					var promise = ApplePaySession.canMakePaymentsWithActiveCard(applePayInstance.merchantIdentifier);
					promise.then(function(canMakePaymentsWithActiveCard)
					{
						if (!canMakePaymentsWithActiveCard)
						{
							console.warn('No Apple Pay card available');
							return;
						}

						self.$target.removeClass('u-hidden');

						var $form = self.$target,
							$submit = $form.find('.js-applePayButton');

						$submit.on('click', function()
						{
							var paymentRequest = applePayInstance.createPaymentRequest({
								total: {
									label: self.options.title,
									amount: self.options.amount
								}
							});

							var session = new ApplePaySession(2, paymentRequest);

							session.onvalidatemerchant = function(e)
							{
								applePayInstance.performValidation({ validationURL: e.validationURL, displayName: self.options.boardTitle }, function (validationErr, merchantSession)
								{
									if (validationErr)
									{
										XF.alert(validationErr.message);
										session.abort();
										return;
									}
									session.completeMerchantValidation(merchantSession);
								});
							};

							session.onpaymentauthorized = function(e)
							{
								applePayInstance.tokenize({ token: e.payment.token }, function(tokenizeErr, payload)
								{
									if (tokenizeErr)
									{
										XF.alert(tokenizeErr.message);
										session.completePayment(ApplePaySession.STATUS_FAILURE);
										return;
									}
									session.completePayment(ApplePaySession.STATUS_SUCCESS);

									self.response(payload);
								});
							};

							session.begin();
						});
					});
				});
			});
		},

		response: function(object)
		{
			if (this.xhr)
			{
				this.xhr.abort();
			}

			this.xhr = XF.ajax('post', this.$target.attr('action'), object, XF.proxy(this, 'complete'), { skipDefaultSuccess: true });
		},

		complete: function(data)
		{
			this.xhr = null;

			if (data.redirect)
			{
				XF.redirect(data.redirect);
			}
		}
	});

	XF.BraintreePayPalForm = XF.Element.newHandler({

		options: {
			clientToken: null,
			paypalButton: '#paypal-button',
			testPayments: false
		},

		xhr: null,

		init: function()
		{
			var urls = [
				'https://www.paypalobjects.com/api/checkout.js',
				'https://js.braintreegateway.com/web/3.19.0/js/client.min.js',
				'https://js.braintreegateway.com/web/3.19.0/js/paypal-checkout.min.js',
				'https://js.braintreegateway.com/web/3.19.0/js/data-collector.min.js'
			];
			XF.loadScripts(urls, XF.proxy(this, 'postInit'));
		},

		postInit: function()
		{
			if (!this.options.clientToken)
			{
				console.error('Form must contain a data-client-token attribute.');
				return;
			}

			var self = this,
				options = {
					authorization: this.options.clientToken
				};

			braintree.client.create(options, function(clientErr, clientInstance)
			{
				if (clientErr)
				{
					XF.alert(clientErr.message);
					return;
				}

				braintree.paypalCheckout.create({ client: clientInstance }, function(paypalCheckoutErr, paypalCheckoutInstance)
				{
					if (paypalCheckoutErr)
					{
						XF.alert(paypalCheckoutErr.message);
						return;
					}

					paypal.Button.render({
						env: self.options.testPayments ? 'sandbox' : 'production',

						payment: function()
						{
							return paypalCheckoutInstance.createPayment({
								flow: 'vault',
								enableShippingAddress: false
							});
						},

						onAuthorize: function(data, actions)
						{
							return paypalCheckoutInstance.tokenizePayment(data).then(function(payload)
							{
								self.response(payload);
							});
						},

						onCancel: function (data)
						{
							console.log('checkout.js payment cancelled', JSON.stringify(data, 0, 2));
						},

						onError: function (err)
						{
							XF.alert(err.message);
						}
					}, self.options.paypalButton);
				});
			});
		},

		response: function(object)
		{
			if (this.xhr)
			{
				this.xhr.abort();
			}

			this.xhr = XF.ajax('post', this.$target.attr('action'), object, XF.proxy(this, 'complete'), { skipDefaultSuccess: true });
		},

		complete: function(data)
		{
			this.xhr = null;

			if (data.redirect)
			{
				XF.redirect(data.redirect);
			}
		}
	});

	XF.StripePaymentForm = XF.Element.newHandler({

		options: {
			publishableKey: null,
			formStyles: '.js-formStyles',
			recurring: null,
			piSecret: null,
			prEnabled: null,
			prCountry: null,
			prCost: null,
			prCurrency: null,
			prLabel: null,
			// hard-coding this for now as dark just seems to fit better
			styleType: 'dark'
		},

		stripe: null,
		elements: null,
		elementsCache: {},
		paymentRequest: null,

		stripeJs: 'https://js.stripe.com/v3/',

		processing: null,

		init: function ()
		{
			this.$target.on('submit', XF.proxy(this, 'submit'));

			if (!XF.loadedScripts.hasOwnProperty(this.stripeJs))
			{
				XF.loadScript(this.stripeJs, XF.proxy(this, 'postInit'));
			}
			else
			{
				this.postInit();
			}
		},

		postInit: function()
		{
			if (!this.options.publishableKey)
			{
				console.error('Form must contain a data-publishable-key attribute.');
				return;
			}

			this.stripe = Stripe(this.options.publishableKey);
			this.elements = this.stripe.elements();

			if (this.options.prEnabled)
			{
				this.paymentRequest = this.stripe.paymentRequest({
					country: this.options.prCountry,
					currency: this.options.prCurrency.toLowerCase(),
					total: {
						label: this.options.prLabel,
						amount: this.options.prCost,
					},
					requestPayerName: true,
					requestPayerEmail: true,
				});
			}

			this.initElements();

			var overlay = this.$target.closest('.overlay-container').data('overlay');
			overlay.on('overlay:hidden', function()
			{
				overlay.destroy();

				// remove any iframes created by Stripe to completely reset
				var $iframes = $('iframe');
				$iframes.each(function()
				{
					var $iframe = $(this);
					if ($iframe.attr('name') && $iframe.attr('name').toLowerCase().indexOf('stripe') >= 0)
					{
						$iframe.remove();
					}
				});
			});
		},

		initElements: function()
		{
			var self = this,
				stripe = this.stripe,
				elements = this.elements,
				clientSecret = this.options.piSecret,
				paymentRequest = this.paymentRequest,
				$styleData = this.$target.find(this.options.formStyles) || {},
				style = $styleData ? $.parseJSON($styleData.first().html()) : {};

			if (paymentRequest)
			{
				var prButton = elements.create('paymentRequestButton', {
					paymentRequest: paymentRequest,
					style: {
						paymentRequestButton: {
							theme: (this.options.styleType === 'light') ? 'light-outline' : 'dark'
						}
					}
				});

				paymentRequest.canMakePayment().then(function(result)
				{
					if (result)
					{
						prButton.mount('#payment-request-button');
						self.$target.find('.js-pr-remove').show();
					}
					else
					{
						self.$target.find('.js-pr-remove').remove();
					}
				});

				paymentRequest.on('paymentmethod', function(e)
				{
					stripe.confirmPaymentIntent(clientSecret, {
						payment_method: e.paymentMethod.id,
						save_payment_method: self.options.recurring ? true : false,
						setup_future_usage: self.options.recurring ? 'off_session' : null
					})
					.then(function(confirmResult)
					{
						if (confirmResult.error)
						{
							e.complete('fail');
						}
						else
						{
							e.complete('success');

							self.handleCardPayment();
						}
					});
				});
			}

			var card = elements.create('card', {
				style: style
			});

			card.mount('#card-element');

			this.elementsCache['card'] = card;
		},

		submit: function(e)
		{
			e.preventDefault();

			if (this.processing)
			{
				return false;
			}

			this.processing = true;

			var $submit = $(e.target),
				cardElement = this.elementsCache['card'],
				$errorContainer = $('#card-errors-container'),
				$error = $errorContainer.find('#card-errors');

			this.handleCardPayment(cardElement);

			$errorContainer.addClass('u-hidden');
			$error.addClass('u-hidden');
			$error.text('');

			$submit.addClass('is-disabled')
				.prop('disabed', true);
		},

		handleCardPayment: function(cardElement)
		{
			var self = this,
				stripe = this.stripe,
				data, thenFn = function(result)
				{
					if (result.error)
					{
						self.handleError(result.error);
					}
					else
					{
						self.response();
					}
				};

			if (!cardElement)
			{
				stripe.handleCardPayment(this.options.piSecret).then(thenFn);
			}
			else
			{
				data = {
					save_payment_method: this.options.recurring ? true : false,
					setup_future_usage: this.options.recurring ? 'off_session' : null
				};
				stripe.handleCardPayment(this.options.piSecret, cardElement, data).then(thenFn);
			}
		},

		handleError: function(error)
		{
			var $errorContainer = $('#card-errors-container'),
				$error = $errorContainer.find('#card-errors');

			$error.text(error.message);

			$error.removeClass('u-hidden');
			$errorContainer.removeClass('u-hidden');

			this.processing = false;
		},

		response: function()
		{
			var $form = this.$target,
				formData = XF.getDefaultFormData($form);

			XF.ajax('post', $form.attr('action'), formData, XF.proxy(this, 'complete'), { skipDefaultSuccess: true });
		},

		complete: function(data)
		{
			this.processing = false;

			if (data.redirect)
			{
				XF.redirect(data.redirect);
			}
		}
	});

	XF.Element.register('payment-provider-container', 'XF.PaymentProviderContainer');

	XF.Element.register('braintree-payment-form', 'XF.BraintreePaymentForm');
	XF.Element.register('braintree-apple-pay-form', 'XF.BraintreeApplePayForm');
	XF.Element.register('braintree-paypal-form', 'XF.BraintreePayPalForm');

	XF.Element.register('stripe-payment-form', 'XF.StripePaymentForm');
}
(jQuery, window, document);
