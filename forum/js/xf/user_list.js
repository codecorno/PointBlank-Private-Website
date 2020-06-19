!function($, window, document, _undefined)
{
	"use strict";

	XF.RemoveLink = XF.Event.newHandler({
		eventNameSpace: 'XFRemoveClick',

		options: {
			user: null,
			href: null
		},

		loading: false,
		container: null,
		noResults: null,
		count: 0,

		init: function()
		{
			if (!this.options.href)
			{
				this.options.href = $(this.$target).attr('href');
			}

			if (this.options.user === null || this.options.href === null)
			{
				console.warn('Link found without userId or url defined. %o', this.$target);
				return false;
			}

			this.container = $('#user_list_' + this.options.user);
			this.noResults = $('.js-userListEmpty');
			this.count = $('.user-link').length;
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.loading)
			{
				return;
			}

			this.remove();
		},

		remove: function()
		{
			this.loading = true;

			var t = this;
			XF.ajax(
				'post',
				this.options.href,
				{ user_id: this.options.user },
				function()
				{
					t.container.remove();
					t.count--;

					if (t.count < 1)
					{
						t.noResults.removeClass('u-hidden');
					}
				},
				{ skipDefault: true }
			).always(function()
			{
				t.loading = false;
			});
		}
	});

	XF.AddForm = XF.Element.newHandler({
		options: {},

		form: null,
		userInput: null,
		noResults: null,

		init: function()
		{
			this.form = $(this.$target);
			this.form.on('ajax-submit:complete', XF.proxy(this, 'formSubmit'));

			this.userInput = this.form.find('.users');
			this.noResults = this.form.find('.js-userListEmpty');
		},

		formSubmit: function (e, data)
		{
			e.preventDefault();

			if (data.status === 'error')
			{
				return;
			}

			var userIds = data.userIds,
				lastId = null,
				templateHtml = null,
				i = 0,
				t = this;

			this.userInput.val('').autofocus();

			for (i = 0; i < userIds.length; i++)
			{
				if (this.form.find('#user_list_' + userIds[i]).length == 0)
				{
					// this user is not already shown, so insert the template here
					templateHtml = $(data.users[userIds[i]]);

					if (lastId)
					{
						XF.setupHtmlInsert(templateHtml, function($html, container, onComplete)
						{
							$html.appendTo('.user-list');
						});
					}
					else
					{
						XF.setupHtmlInsert(templateHtml, function($html, container, onComplete)
						{
							$html.prependTo('.user-list');
						});
					}

					if ($('.user-link').length > 0)
					{
						t.noResults.addClass('u-hidden');
					}
				}

				lastId = '#user_list_' + userIds[i];
			}
		}
	});

	XF.Event.register('click', 'remove-user', 'XF.RemoveLink');
	XF.Element.register('add-user', 'XF.AddForm');
}
(jQuery, window, document);