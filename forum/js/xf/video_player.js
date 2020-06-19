!function($, window, document, _undefined)
{
	"use strict";

	XF.VideoPlayer = XF.Element.newHandler({
		options: {
			playerSetup: {}
		},

		player: null,

		init: function()
		{
			this.player = videojs(this.$target[0], this.options.playerSetup);
			this.player.ready(XF.proxy(this, 'ready'));
		},

		ready: function()
		{
			var event = $.Event('video-player:ready'),
				config = {
					player: this.player,
					video: this.$target,
					handler: this
				};

			this.$target.trigger(event, config);
		}
	});

	XF.Element.register('video-player', 'XF.VideoPlayer');
}
(jQuery, window, document);