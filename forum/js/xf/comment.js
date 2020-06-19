!function($, window, document, _undefined)
{
	"use strict";

	XF.CommentLoader = XF.Event.newHandler({
		eventNameSpace: 'XFCommentLoaderClick',

		options: {
			container: null,
			target: null,
			href: null
		},

		$loaderTarget: null,
		$container: null,
		href: null,
		loading: false,

		init: function()
		{
			var container = this.options.container,
				$container = container ? this.$target.closest(container) : this.$target,
				target = this.options.target,
				$target = target ? XF.findRelativeIf(target, this.$container) : $container;

			this.$container = $container;

			if ($target.length)
			{
				this.$loaderTarget = $target;
			}
			else
			{
				console.error('No loader target for %o', this.$target);
				return;
			}

			this.href = this.options.href || this.$target.attr('href');

			if (!this.href)
			{
				console.error('No href for %o', this.$target);
			}
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.loading)
			{
				return;
			}

			this.loading = true;

			var t = this;
			XF.ajax('get', this.href, null, function(data)
			{
				if (data.html)
				{
					XF.setupHtmlInsert(data.html, function($html, container)
					{
						$html.insertAfter(t.$loaderTarget);
						t.$container.remove();
					});
				}
			}).always(function()
			{
				t.loading = false;
			});
		}
	});

	XF.Event.register('click', 'comment-loader', 'XF.CommentLoader');
}
(jQuery, window, document);