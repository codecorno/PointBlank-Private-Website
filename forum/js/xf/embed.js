!function($, window, document, _undefined)
{
	"use strict";

	XF.OembedFetcher = XF.Element.newHandler({
		options: {
			provider: '',
			id: ''
		},

		init: function()
		{
			if (this.options.provider && this.options.id)
			{
				$.ajax(XF.canonicalizeUrl('oembed.php'), {
					data: {
						'provider': this.options.provider,
						'id': this.options.id.replace(/#/, '{{_hash_}}')
					},
					success: XF.proxy(this, 'handleResponse'),
					global: false
				});
			}
		},

		handleResponse: function(data, message, xhr)
		{
			if (data.hasOwnProperty('html'))
			{
				this.insertOembedHtml(data, xhr.getResponseHeader('X-Oembed-Retain-Scripts') ? true : false); // see oEmbed controller response
			}
			else if (data.type == 'photo')
			{
				this.insertOembedImage(data);
			}
			else if (data.hasOwnProperty('xf-oembed-error'))
			{
				this.oembedFetchError(data);
			}
		},

		insertOembedHtml: function(data, retainScripts)
		{
			if (data.html === undefined)
			{
				return false;
			}

			var container = {
				content: data.html
			};

			XF.setupHtmlInsert(container, XF.proxy(function($html, container, onComplete)
			{
				this.$target
					.addClass('bbOembed--loaded')
					.html($html);

				this.onComplete();

			}, this), retainScripts);
		},

		insertOembedImage: function(data)
		{
			var $a = $('<a />', this.getImageLinkData(data)),
				$i = $('<img class="bbImage" data-zoom-target="1" />').appendTo($a);

			$i.on('load', XF.proxy(this, 'onComplete'))
				.attr('src', data.url);

			this.$target.empty().append($a);
		},

		oembedFetchError: function(data)
		{
			this.$target.addClass('bbOembed--failure');
			console.warn('Unable to fetch %s media id: %s', this.options.provider, this.options.id);
		},

		getImageLinkData: function(data)
		{
			var attributes = {
					rel: 'external',
					target: '_blank'
				},
				properties = {
					href: ['web_page', 'web_page_short_url', 'author_url'],
					title: ['title'],
					'data-author': ['author_name']
				},
				attr = '',
				i = 0;

			for (attr in properties)
			{
				for (i = 0; i < properties[attr].length; i++)
				{
					if (data.hasOwnProperty(properties[attr][i]))
					{
						attributes[attr] = data[properties[attr][i]];

						break;
					}
				}
			}

			return attributes;
		},

		onComplete: function()
		{
			$(document).trigger('embed:loaded');
			XF.layoutChange();
		}
	});

	XF.TweetRenderer = XF.Element.newHandler({
		options: {
			tweetId: null,

			// see https://dev.twitter.com/web/javascript/creating-widgets
			lang: 'en',
			dnt: 'false',
			related: null,
			via: null,

			conversation: 'all',
			cards: 'visible',
			align: null,
			theme: 'light',
			linkColor: '#2b7bb9'
		},

		init: function()
		{
			var tweetId = this.options.tweetId + '';

			if (window.twttr && tweetId.length)
			{
				twttr.ready(XF.proxy(function(twttr)
				{
					twttr.widgets.createTweet(tweetId, this.$target.get(0), this.options)
						.then(function()
						{
							$(document).trigger('embed:loaded');
							XF.layoutChange();
						});

				}, this));
			}
		}
	});

	XF.Element.register('oembed', 'XF.OembedFetcher');
	XF.Element.register('tweet', 'XF.TweetRenderer');
}
(jQuery, window, document);