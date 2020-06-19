!function($, window, document, _undefined)
{
	"use strict";

	// ################################## AVATAR UPLOAD HANDLER ###########################################

	XF.AvatarUpload = XF.Element.newHandler({

		options: {},

		init: function()
		{
			var $form = this.$target,
				$file = $form.find('.js-uploadAvatar'),
				$avatar = $form.find('.js-avatar'),
				$deleteButton = $form.find('.js-deleteAvatar');

			if ($avatar.find('img').length)
			{
				$deleteButton.show();
			}
			else
			{
				$deleteButton.hide();
			}

			$file.on('change', XF.proxy(this, 'changeFile'));
			$form.on('ajax-submit:response', XF.proxy(this, 'ajaxResponse'));
		},

		changeFile: function(e)
		{
			if ($(e.target).val() != '')
			{
				this.$target.submit();
			}
		},

		ajaxResponse: function(e, data)
		{
			if (data.errors || data.exception)
			{
				return;
			}

			e.preventDefault();

			if (data.message)
			{
				XF.flashMessage(data.message, 3000);
			}

			var $form = this.$target,
				$delete = $form.find('.js-deleteAvatar'),
				$file = $form.find('.js-uploadAvatar'),
				$avatar = $form.find('.js-avatar'),
				$x = $form.find('.js-avatarX'),
				$y = $form.find('.js-avatarY'),
				useCustom = ($form.find('input[name="use_custom"]:checked').val() == 1);

			if (!useCustom)
			{
				$('.js-gravatarPreview').attr('src', data.gravatarTest ? data.gravatarPreview : data.gravatarUrl);
				if (data.gravatarTest)
				{
					return;
				}
			}
			else
			{
				$avatar.css({
					left: data.cropX * -1,
					top: data.cropX * -1
				});
				$x.val(data.cropX);
				$y.val(data.cropY);
				$avatar.data('x', data.cropX);
				$avatar.data('y', data.cropY);

				XF.Element.initializeElement($avatar);

				$file.val('');
			}

			XF.updateAvatars(data.userId, data.avatars, useCustom);

			if (data.defaultAvatars)
			{
				$delete.hide();
			}
			else
			{
				$delete.show();
			}

			$('.js-avatarCropper').trigger('avatar:updated', data);
		}
	});

	// ################################## AVATAR CROPPER HANDLER ###########################################

	XF.AvatarCropper = XF.Element.newHandler({

		options: {
			size: 96,
			x: 0,
			y: 0
		},

		$img: null,
		size: 96,

		x: 0,
		y: 0,

		imgW: null,
		imgH: null,

		cropSize: null,
		scale: null,

		init: function()
		{
			this.$target.one('avatar:updated', XF.proxy(this, 'avatarsUpdated'));

			this.$img = this.$target.find('img');

			if (!this.$img.length)
			{
				return;
			}

			this.initTest();
		},

		avatarsUpdated: function(e, data)
		{
			this.options.x = data.cropX;
			this.options.y = data.cropY;
			this.init();
		},

		initTest: function()
		{
			var	img = this.$img[0],
				tests = 0,
				self = this;

			var test = function()
			{
				tests++;
				if (tests > 50)
				{
					return;
				}

				if (img.naturalWidth > 0)
				{
					self.setup();
				}
				else if (img.naturalWidth === 0)
				{
					setTimeout(test, 100);
				}
				// if no naturalWidth support (IE <9), don't init
			};

			test();
		},

		setup: function()
		{
			this.imgW = this.$img[0].naturalWidth;
			this.imgH = this.$img[0].naturalHeight;

			this.cropSize = Math.min(this.imgW, this.imgH);
			this.scale = this.cropSize / this.options.size;

			this.$img.cropbox({
				width: this.size,
				height: this.size,
				zoom: 0,
				maxZoom: 0,
				controls: false,
				showControls: 'never',
				result: {
					cropX: this.options.x * this.scale,
					cropY: this.options.y * this.scale,
					cropW: this.cropSize,
					cropH: this.cropSize
				}
			}).on('cropbox', XF.proxy(this, 'onCrop'));

			// workaround for image dragging bug in Firefox
			// https://bugzilla.mozilla.org/show_bug.cgi?id=1376369
			if (XF.browser.mozilla)
			{
				this.$img.on('mousedown', function(e)
				{
					e.preventDefault();
				});
			}
		},

		onCrop: function(e, results)
		{
			this.$target.parent().find('.js-avatarX').val(results.cropX / this.scale);
			this.$target.parent().find('.js-avatarY').val(results.cropY / this.scale);
		}
	});

	XF.Element.register('avatar-upload', 'XF.AvatarUpload');
	XF.Element.register('avatar-cropper', 'XF.AvatarCropper');
}
(jQuery, window, document);