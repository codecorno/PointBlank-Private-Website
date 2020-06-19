!function($, window, document, _undefined)
{
	"use strict";

	// ################################## PREFIX INPUT HANDLER ###########################################

	XF.PrefixMenu = XF.Element.newHandler({

		options: {
			select: '< .js-prefixContainer | .js-prefixSelect',
			title: '< .js-prefixContainer | .js-titleInput',
			active: '.js-activePrefix',
			menu: '| [data-menu]',
			menuContent: '.js-prefixMenuContent',
			listenTo: '',
			href: ''
		},

		$select: null,
		$active: null,
		$title: null,
		$menu: null,
		$menuContent: null,
		template: null,
		initialPrefixId: 0,

		init: function()
		{
			this.$select = XF.findRelativeIf(this.options.select, this.$target);
			if (!this.$select.length)
			{
				console.error('No select matching %s', this.options.select);
				return;
			}

			this.$select.on('control:enabled control:disabled', XF.proxy(this, 'toggleActive'));

			this.$title = XF.findRelativeIf(this.options.title, this.$target);

			this.$active = this.$target.find(this.options.active);

			this.$menu = XF.findRelativeIf(this.options.menu, this.$target);

			this.$menuContent = this.$menu.find(this.options.menuContent);
			this.$menuContent.on('click', '[data-prefix-id]', XF.proxy(this, 'prefixClick'));

			this.template = this.$menuContent.find('script[type="text/template"]').html();
			if (!this.template)
			{
				console.error('No template could be found');
				this.template = '';
			}

			if (this.options.href)
			{
				var $listenTo = this.options.listenTo ? XF.findRelativeIf(this.options.listenTo, this.$target) : $([]);
				if (!$listenTo.length)
				{
					console.error('Cannot load prefixes dynamically as no element set to listen to for changes');
				}
				else
				{
					$listenTo.on('change', XF.proxy(this, 'loadPrefixes'));
				}
			}

			this.initMenu();

			var prefixId = parseInt(this.$select.val(), 10);
			if (prefixId)
			{
				this.initialPrefixId = prefixId;
				this.selectPrefix(prefixId);
			}

			// reset prefix menu when form is reset
			this.$target.closest('form').on('reset', XF.proxy(function()
			{
				this.reset();
			}, this));
		},

		initMenu: function()
		{
			var groups = [],
				ungrouped = [];

			this.$select.children().each(function()
			{
				var $el = $(this);

				if ($el.is('optgroup'))
				{
					var prefixes = [];
					$el.find('option').each(function()
					{
						var $opt = $(this);

						prefixes.push({
							prefix_id: $opt.attr('value'),
							title: $opt.text(),
							css_class: $opt.attr('data-prefix-class')
						});
					});

					if (prefixes.length)
					{
						groups.push({
							title: $el.attr('label'),
							prefixes: prefixes
						});
					}
				}
				else
				{
					var value = $el.attr('value');

					if (value === '0' || value === '-1')
					{
						// skip no/any
						return;
					}
					else
					{
						ungrouped.push({
							prefix_id: value,
							title: $el.text(),
							css_class: $el.attr('data-prefix-class')
						});
					}
				}
			});

			if (ungrouped.length)
			{
				groups.push({
					title: null,
					prefixes: ungrouped
				});
			}

			this.$menuContent.empty().html(Mustache.render(this.template, { groups: groups }));
		},

		reset: function()
		{
			this.selectPrefix(this.initialPrefixId);
		},

		loadPrefixes: function(e)
		{
			XF.ajax('POST', this.options.href, {
				val: $(e.target).val(),
				initial_prefix_id: this.initialPrefixId
			}, XF.proxy(this, 'loadSuccess'));
		},

		loadSuccess: function(data)
		{
			if (data.html)
			{
				var self = this,
					$select = this.$select;
				XF.setupHtmlInsert(data.html, function($html)
				{
					$html.each(function()
					{
						var $el = $(this);
						if ($el.is('select'))
						{
							var val = $select.val();

							$select.empty().append($el.children());
							if (!$select.find('option[value="' + val + '"]').length)
							{
								val = 0;
							}

							self.initMenu();
							self.selectPrefix(val);

							return false;
						}
					});
				});
			}
		},

		toggleActive: function(e)
		{
			var $select = $(e.target);

			var $textGroup = this.$active.closest('.inputGroup-text');
			if ($textGroup.length)
			{
				if ($select.is(':disabled'))
				{
					$textGroup.addClass('inputGroup-text--disabled');
				}
				else
				{
					$textGroup.removeClass('inputGroup-text--disabled');
				}
			}
		},

		selectPrefix: function(id)
		{
			id = parseInt(id, 10);

			var $active = this.$active,
				$select = this.$select,
				$prefix = $select.find('option[value="' + id + '"]');

			if (!$prefix.length)
			{
				id = 0;
				$prefix = $select.find('option[value="' + id + '"]')
			}

			var addClass = $prefix.data('prefix-class');

			$select.val(id);
			$active.text($prefix.text())
				.removeClass($active.data('prefix-class'))
				.addClass(addClass);

			$active.data('prefix-class', addClass);

			$select.trigger('change');
		},

		prefixClick: function(e)
		{
			this.selectPrefix($(e.target).data('prefix-id'));

			var menu = this.$menu.data('menu-trigger');
			if (menu)
			{
				menu.close();
			}

			var $title = this.$title;
			if ($title.length)
			{
				$title.autofocus();
			}
		}
	});

	// ################################## PREFIX LOADER HANDLER ###########################################

	XF.PrefixLoader = XF.Element.newHandler({

		options: {
			listenTo: '',
			initUpdate: true,
			href: ''
		},

		init: function()
		{
			if (!this.$target.is('select'))
			{
				console.error('Must trigger on select');
				return;
			}

			if (this.options.href)
			{
				var $listenTo = this.options.listenTo ? XF.findRelativeIf(this.options.listenTo, this.$target) : $([]);
				if (!$listenTo.length)
				{
					console.error('Cannot load prefixes dynamically as no element set to listen to for changes');
				}
				else
				{
					$listenTo.on('change', XF.proxy(this, 'loadPrefixes'));

					if (this.options.initUpdate)
					{
						$listenTo.trigger('change');
					}
				}
			}
		},

		loadPrefixes: function(e)
		{
			XF.ajax('POST', this.options.href, {
				val: $(e.target).val()
			}, XF.proxy(this, 'loadSuccess'));
		},

		loadSuccess: function(data)
		{
			if (data.html)
			{
				var $select = this.$target;
				XF.setupHtmlInsert(data.html, function($html)
				{
					var val = $select.val();

					$html.each(function()
					{
						var $el = $(this);
						if ($el.is('select'))
						{
							$select.empty().append($el.children());

							var hasValue = false,
								$options = $select.find('option');
							$options.each(function()
							{
								if ($(this).attr('value') === val)
								{
									$select.val(val);
									hasValue = true;
									return false;
								}
							});
							if (!hasValue)
							{
								$select.val($options.first().attr('value'));
							}

							return false;
						}
					});
				});
			}
		}
	});

	XF.Element.register('prefix-menu', 'XF.PrefixMenu');
	XF.Element.register('prefix-loader', 'XF.PrefixLoader');
}
(jQuery, window, document);