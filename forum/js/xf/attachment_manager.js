!function($, window, document, _undefined)
{
	"use strict";

	XF.AttachmentManager = XF.Element.newHandler({

		options: {
			uploadButton: '.js-attachmentUpload',
			manageUrl: null,
			filesContainer: '.js-attachmentFiles',
			fileRow: '.js-attachmentFile',
			insertAllRow: '.js-attachmentInsertAllRow',
			insertRow: '.js-attachmentInsertRow',
			allActionButton: '.js-attachmentAllAction',
			actionButton: '.js-attachmentAction',
			uploadTemplate: '.js-attachmentUploadTemplate',
			templateProgress: '.js-attachmentProgress',
			templateError: '.js-attachmentError',
			templateThumb: '.js-attachmentThumb',
			templateView: '.js-attachmentView',
			allowDrop: false,
			checkVideoSize: true
		},

		$filesContainer: null,
		template: null,
		$form: null,

		supportsVideoUploads: null,

		manageUrl: null,
		flow: null,

		fileMap: {},
		isUploading: false,

		editor: null,

		init: function()
		{
			var self = this,
				options = this.options,
				$target = this.$target;

			if (!window.Flow)
			{
				console.error('flow.js must be loaded');
				return;
			}

			var ua = navigator.userAgent;
			if (ua.match(/Android [1-4]/))
			{
				var chrome = ua.match(/Chrome\/([0-9]+)/);
				if (!chrome || parseInt(chrome[1], 10) < 33)
				{
					console.warn('Old Android WebView detected. Must fallback to basic uploader.');
					return;
				}
			}

			var $uploaders = $target.find(options.uploadButton);

			if (this.options.manageUrl)
			{
				this.manageUrl = this.options.manageUrl;
			}
			else
			{
				if (!$uploaders.length)
				{
					console.error('No manage URL specified and no uploaders available.');
					return;
				}

				var $uploader = $uploaders.first();
				this.manageUrl = $uploader.data('upload-href') || $uploader.attr('href');
			}

			this.$filesContainer = $target.find(options.filesContainer);
			this.$filesContainer
				.on('click', options.actionButton, XF.proxy(this, 'actionButtonClick'))
				.on('click', options.allActionButton, XF.proxy(this, 'allActionButtonClick'));

			this.template = $target.find(options.uploadTemplate).html();
			if (!this.template)
			{
				console.error('No attached file template found.');
			}

			var flow = this.setupFlow();
			if (!flow)
			{
				console.error('No flow uploader support');
				return;
			}

			this.flow = flow;
			this.setupUploadButtons($uploaders, flow);

			if (this.options.allowDrop)
			{
				flow.assignDrop([$target[0]]); // extra array wrap due to flow.js bug
			}

			setTimeout(function()
			{
				self.editor = XF.getEditorInContainer(self.$target, '[data-attachment-target=false]');
				if (!self.editor)
				{
					self.removeInsertButtons(self.$filesContainer);
				}

				self.toggleInsertAllRow();
			}, 50);

			this.$form = this.$target.closest('form');
			if (this.$form.length)
			{
				this.$form.on('ajax-submit:before', function(e, data)
				{
					if (self.isUploading && !confirm(XF.phrase('files_being_uploaded_are_you_sure')))
					{
						data.preventSubmit = true;
					}
				});
			}
		},

		setupFlow: function()
		{
			var options = this.getFlowOptions(),
				flow = new Flow(options),
				self = this;

			if (!flow.support)
			{
				if (!window.FustyFlow)
				{
					return null;
				}

				options.matchJSON = true;

				flow = new FustyFlow(options);
			}

			flow.on('fileAdded', XF.proxy(this, 'fileAdded'));
			flow.on('filesSubmitted', function() { self.setUploading(true); flow.upload(); });
			flow.on('fileProgress', XF.proxy(this, 'uploadProgress'));
			flow.on('fileSuccess', XF.proxy(this, 'uploadSuccess'));
			flow.on('fileError', XF.proxy(this, 'uploadError'));

			return flow;
		},

		getFlowOptions: function()
		{
			return {
				target: this.manageUrl,
				allowDuplicateUploads: true,
				fileParameterName: 'upload',
				query: XF.proxy(this, 'uploadQueryParams'),
				simultaneousUploads: 1,
				testChunks: false,
				progressCallbacksInterval: 100,
				chunkSize: 4 * 1024 * 1024 * 1024, // always one chunk
				readFileFn: function (fileObj, startByte, endByte, fileType, chunk)
				{
					var function_name = 'slice';

					if (fileObj.file.slice) function_name =  'slice';
					else if (fileObj.file.mozSlice) function_name = 'mozSlice';
					else if (fileObj.file.webkitSlice) function_name = 'webkitSlice';

					if (!fileType)
					{
						fileType = '';
					}

					chunk.readFinished(fileObj.file[function_name](startByte, endByte, fileType));
				}
			};
		},

		setupUploadButtons: function($uploaders, flow)
		{
			var t = this;
			$uploaders.each(function()
			{
				var $button = $(this),
					accept = $button.data('accept') || '',
					$target = $('<span />').insertAfter($button).append($button);

				if (accept == '.')
				{
					accept = '';
				}

				$button.click(function(e) { e.preventDefault(); });
				flow.assignBrowse($target[0], false, false, {
					accept: accept
				});

				if (t.supportsVideoUploads === null)
				{
					var videoExtensions = XF.config.allowedVideoExtensions,
						allowedExtensions = accept.split(',');

					for (var key in allowedExtensions)
					{
						var extension = allowedExtensions[key].substr(1);
						if (videoExtensions.indexOf(extension) !== -1)
						{
							t.supportsVideoUploads = true;
							break;
						}
					}
				}

				var $file = $target.find('input[type=file]');
				$file.attr('title', XF.htmlspecialchars(XF.phrase('attach')));
				$file.css('overflow', 'hidden');
				$file.css(XF.isRtl() ? 'right' : 'left', -1000);
			});
		},

		fileAdded: function(file)
		{
			var $html = this.applyUploadTemplate({
				filename: file.name,
				uploading: true
			});
			this.resizeProgress($html, 0);

			$html.data('file', file);

			this.$filesContainer.addClass('is-active');
			$html.appendTo(this.$filesContainer);

			this.fileMap[file.uniqueIdentifier] = $html;

			this.$target.find(this.options.uploadButton).blur();

			var $button = this.$target.find(this.options.uploadButton).first(),
				maxVideoSize = $button.data('video-size');

			// avoid having to upload a huge file fully before being told it is too large
			if (this.options.checkVideoSize
				&& this.supportsVideoUploads
				&& this.isVideo(file)
				&& maxVideoSize > 0
				&& file.size > maxVideoSize
			)
			{
				// note: only applying this to videos as images at least can be made smaller after upload through resizing
				this.uploadError(file, this.addErrorToJson({}, XF.phrase('file_too_large_to_upload')));
				return false;
			}
			if (XF.config.uploadMaxFilesize > 0 && file.size > XF.config.uploadMaxFilesize)
			{
				this.uploadError(file, this.addErrorToJson({}, XF.phrase('uploaded_file_is_too_large_for_server_to_process')));
				return false;
			}
		},

		isVideo: function(file)
		{
			var name = file.name,
				fileParts = name.split('.'),
				videoExtensions = XF.config.allowedVideoExtensions;

			if (fileParts.length === 1 || (fileParts[0] === '' && fileParts.length))
			{
				return false;
			}

			return (videoExtensions.indexOf(fileParts.pop()) !== -1);
		},

		uploadProgress: function(file)
		{
			var $html = this.fileMap[file.uniqueIdentifier];
			if (!$html)
			{
				return;
			}

			this.setUploading(true);

			this.resizeProgress($html, file.progress());
		},

		resizeProgress: function($row, progress)
		{
			var percent = Math.floor(progress * 100),
				$progress = $row.find(this.options.templateProgress),
				$inner = $progress.find('i');

			if (!$inner.length)
			{
				$inner = $('<i />');
				$progress.html('&nbsp;').append($inner);
			}

			$inner.text(percent + '%')
				.css('width', percent + '%');
		},

		uploadSuccess: function(file, message, chunk)
		{
			var json = this.getObjectFromMessage(message);

			this.setUploading(false);

			if (json.status && json.status == 'error')
			{
				this.uploadError(file, json, chunk);
				return;
			}

			if (json.attachment)
			{
				this.insertUploadedRow(json.attachment, this.fileMap[file.uniqueIdentifier]);
			}
			else
			{
				json = this.addErrorToJson(json);
				this.uploadError(file, json, chunk);
			}
		},

		setUploading: function(uploading)
		{
			var newValue = uploading ? true : false;

			if (newValue !== this.isUploading)
			{
				this.isUploading = newValue;

				if (newValue)
				{
					this.$target.trigger('attachment-manager:upload-start');
				}
				else
				{
					this.$target.trigger('attachment-manager:upload-end');
				}
			}
		},

		getObjectFromMessage: function(message)
		{
			if (message instanceof Object)
			{
				return message;
			}

			try
			{
				return $.parseJSON(message);
			}
			catch(e)
			{
				return this.addErrorToJson({});
			}
		},

		addErrorToJson: function(json, errorString)
		{
			json.status = 'error';
			json.errors = [errorString === null ? XF.phrase('oops_we_ran_into_some_problems') : errorString];

			return json;
		},

		insertUploadedRow: function(attachment, $existingHtml)
		{
			var $newHtml = this.applyUploadTemplate(attachment);

			if (!this.editor)
			{
				this.removeInsertButtons($newHtml);
			}

			if ($existingHtml)
			{
				$existingHtml.replaceWith($newHtml);
			}
			else
			{
				this.$filesContainer.addClass('is-active');
				$newHtml.appendTo(this.$filesContainer);
			}

			XF.activate($newHtml);
			XF.layoutChange();

			var event = $.Event('attachment:row-inserted');
			$newHtml.trigger(event, [$newHtml, this]);

			this.toggleInsertAllRow();
		},

		uploadError: function(file, message, chunk)
		{
			var json = this.getObjectFromMessage(message);

			this.setUploading(false);

			var $row = this.fileMap[file.uniqueIdentifier];
			if ($row && json.errors)
			{
				$row.find(this.options.templateProgress).remove();
				$row.find(this.options.templateError).text(json.errors[0]);
				$row.addClass('is-uploadError');

				delete this.fileMap[file.uniqueIdentifier];
				$row.removeData('file');
			}
			else
			{
				XF.defaultAjaxSuccessError(json, 200, chunk.xhr);
				this.removeFileRow($row);
			}
		},

		actionButtonClick: function(e)
		{
			e.preventDefault();

			var $target = $(e.currentTarget),
				action = $target.attr('data-action'),
				type = $target.attr('data-type'),
				$row = $target.closest(this.options.fileRow);

			switch (action)
			{
				case 'thumbnail':
				case 'full':
					this.insertAttachment($row, action, type);
					break;

				case 'delete':
					this.deleteAttachment($row, type);
					break;

				case 'cancel':
					this.cancelUpload($row);
					break;
			}
		},

		allActionButtonClick: function(e)
		{
			e.preventDefault();

			var action = $(e.currentTarget).attr('data-action'),
				$buttons = this.$filesContainer.find(this.options.fileRow + ' ' + this.options.actionButton);

			$buttons = $buttons.filter(function()
			{
				return ($(this).attr('data-action') === action);
			});

			$buttons.click();
		},

		insertAttachment: function($row, action, type)
		{
			type = type || 'image';

			var attachmentId = $row.data('attachment-id');
			if (!attachmentId)
			{
				return;
			}
			if (!this.editor)
			{
				return;
			}

			var thumb = $row.find(this.options.templateThumb).attr('src'),
				view = $row.find(this.options.templateView).attr('href');

			var html, bbCode, params = {
				id: attachmentId,
				img: thumb
			};

			if (action == 'full')
			{
				bbCode = '[ATTACH=full]' + attachmentId + '[/ATTACH]';

				if (type == 'image')
				{
					html = '<img src="{{img}}" data-attachment="full:{{id}}" alt="" />';
				}
				else
				{
					html = '<span contenteditable="false" draggable="true" class="fr-video fr-dvi fr-fvl fr-draggable fr-deletable"><video data-xf-init="video-init" data-attachment="full:{{id}}" src="{{img}}" controls></video></span>';
				}

				params.img = view;
			}
			else
			{
				if (!thumb || type !== 'image')
				{
					return;
				}

				bbCode = '[ATTACH]' + attachmentId + '[/ATTACH]';
				html = '<img src="{{img}}" data-attachment="thumb:{{id}}" alt="" />';
			}

			html = Mustache.render(html, params);
			XF.insertIntoEditor(this.$target, html, bbCode, '[data-attachment-target=false]');
		},

		deleteAttachment: function($row, type)
		{
			type = type || 'image';

			var attachmentId = $row.data('attachment-id');
			if (!attachmentId)
			{
				return;
			}

			var self = this;

			XF.ajax(
				'post',
				this.manageUrl,
				{ delete: attachmentId },
				function (data)
				{
					if (data.delete)
					{
						self.removeFileRow($row);
					}
				},
				{ skipDefaultSuccess: true }
			);

			var attrMatch = new RegExp('^[a-z]+:' + attachmentId + '$', 'i'),
				textMatch = new RegExp('\\[attach(=[^\\]]*)?\\]' + attachmentId + '\\[/attach\\]', 'gi'),
				htmlRemove = function(editor)
				{
					var frEd = editor.ed;

					if (type == 'image')
					{
						var $imgs = frEd.$el.find('img[data-attachment]').filter(function()
						{
							return attrMatch.test($(this).attr('data-attachment'));
						});

						$imgs.each(function()
						{
							frEd.image.remove($(this));
						});
					}
					else if (type == 'video')
					{
						var $videos = frEd.$el.find('video[data-attachment]').filter(function()
						{
							return attrMatch.test($(this).attr('data-attachment'));
						});

						$videos.each(function()
						{
							var $video = $(this),
								$container = $video.parent();

							$container.remove();
						});
					}
				},
				bbCodeRemove = function($textarea)
				{
					var val = $textarea.val();
					val = val.replace(textMatch, '');
					$textarea.val(val);
				};

			XF.modifyEditorContent(this.$target, htmlRemove, bbCodeRemove, '[data-attachment-target=false]');
		},

		cancelUpload: function($row)
		{
			var file = $row.data('file'),
				attachmentId = $row.data('attachment-id');

			if (attachmentId)
			{
				// fully uploaded and processed
				return;
			}

			if (file && file.progress() == 1)
			{
				// fully uploaded and being processed, don't allow removal
				return;
			}

			// this is either being uploaded or it has errored
			this.removeFileRow($row);
		},

		uploadQueryParams: function()
		{
			return {
				_xfToken: XF.config.csrf,
				_xfResponseType: 'json',
				_xfWithData: 1
			};
		},

		applyUploadTemplate: function(params)
		{
			var $html = $($.parseHTML(Mustache.render(this.template, params))),
				fileRow = this.options.fileRow;

			return $html.filter(function() { return $(this).is(fileRow); });
		},

		removeFileRow: function($row)
		{
			$row.remove();

			this.toggleInsertAllRow();

			if (!this.$filesContainer.find(this.options.fileRow).length)
			{
				this.$filesContainer.removeClass('is-active');
				XF.layoutChange();
			}
		},

		removeInsertButtons: function($container)
		{
			$container.find(this.options.insertRow + ',' + this.options.insertAllRow).remove();

			XF.layoutChange();
		},

		toggleInsertAllRow: function()
		{
			var $rows = this.$filesContainer.find(this.options.actionButton).filter(':not([data-action=delete])').closest(this.options.fileRow),
				$insertAllRow = this.$filesContainer.find(this.options.insertAllRow);

			if ($rows.length > 1)
			{
				$insertAllRow.addClass('is-active');
			}
			else
			{
				$insertAllRow.removeClass('is-active');
			}

			XF.layoutChange();
		}
	});

	XF.AttachmentOnInsert = XF.Element.newHandler({

		options: {
			fileRow: '.js-attachmentFile',
			href: null,
			linkData: null
		},

		loading: false,

		init: function()
		{
			var $row = this.$target.closest(this.options.fileRow);
			if (!$row.length || !this.options.href)
			{
				console.error('Cannot find inserted row or action to perform.');
			}
			$row.on('attachment:row-inserted', XF.proxy(this, 'onAttachmentInsert'));
		},

		onAttachmentInsert: function(e, $html, manager)
		{
			if (this.loading)
			{
				return;
			}

			var self = this,
				href = this.options.href,
				data = this.options.linkData || {};

			XF.ajax('post', href, data, XF.proxy(this, 'onLoad')).always(function() { self.loading = false; });
		},

		onLoad: function(data)
		{
			if (!data.html)
			{
				return;
			}

			var self = this;

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				self.$target.replaceWith($html).xfFadeDown(XF.config.speed.xfast, function()
				{
					onComplete(true);
					XF.layoutChange();
				});
			});
		}
	});

	XF.Element.register('attachment-manager', 'XF.AttachmentManager');
	XF.Element.register('attachment-on-insert', 'XF.AttachmentOnInsert');
}(jQuery, window, document);