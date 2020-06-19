!function($, window, document, _undefined)
{
	"use strict";

	XF.Color = XF.create({
		rgb: null,
		hsv: null,
		hsl: null,
		a: 1,
		name: null,

		__construct: function(type, color, name)
		{
			var a = null;

			var capInt = function(v, min, max)
			{
				v = parseInt(v, 10);
				v = Math.max(min, Math.min(max, v));
				return isNaN(v) ? min : v;
			};
			var capFloat = function(v, min, max)
			{
				v = parseFloat(v);
				return Math.max(min, Math.min(max, v));
			};

			if (type == 'rgb')
			{
				if (typeof color.r != 'undefined')
				{
					this.rgb = {
						r: capInt(color.r, 0, 255),
						g: capInt(color.g, 0, 255),
						b: capInt(color.b, 0, 255)
					};

					a = color.a;
				}
				else if (typeof color[0] != 'undefined')
				{
					this.rgb = {
						r: capInt(color[0], 0, 255),
						g: capInt(color[1], 0, 255),
						b: capInt(color[2], 0, 255)
					};

					a = color[3];
				}
			}
			else if (type == 'hsv')
			{
				if (typeof color.h != 'undefined')
				{
					this.hsv = {
						h: capInt(color.h, 0, 359),
						s: capFloat(color.s, 0, 1),
						v: capFloat(color.v, 0, 1)
					};

					a = color.a;
				}
				else if (typeof color[0] != 'undefined')
				{
					this.hsv = {
						h: capInt(color[0], 0, 359),
						s: capFloat(color[1], 0, 1),
						v: capFloat(color[2], 0, 1)
					};

					a = color[3];
				}
			}
			else if (type == 'hsl')
			{
				if (typeof color.h != 'undefined')
				{
					this.hsl = {
						h: capInt(color.h, 0, 359),
						s: capFloat(color.s, 0, 1),
						l: capFloat(color.l, 0, 1)
					};

					a = color.a;
				}
				else if (typeof color[0] != 'undefined')
				{
					this.hsl = {
						h: capInt(color[0], 0, 359),
						s: capFloat(color[1], 0, 1),
						v: capFloat(color[2], 0, 1)
					};

					a = color[3];
				}
			}

			if (!this.rgb && !this.hsv && !this.hsl)
			{
				throw new Error('No RGB, HSV or HSL color');
			}

			if (typeof a !== 'undefined' && a !== null)
			{
				this.a = capFloat(a, 0, 1);
			}

			this.name = name;
		},

		setName: function(name)
		{
			var type, color;
			if (this.rgb)
			{
				color = this.rgb;
				type = 'rgb';
			}
			else if (this.hsv)
			{
				color = this.hsv;
				type = 'hsv';
			}
			else if (this.hsl)
			{
				color = this.hsl;
				type = 'hsl';
			}
			color.a = this.a;

			return new XF.Color(type, color, name);
		},

		setHsvHue: function(h)
		{
			var hsv = this.toHsv();
			hsv.h = h;

			return new XF.Color('hsv', hsv);
		},

		setHsvSaturation: function(s)
		{
			var hsv = this.toHsv();
			hsv.s = s;

			return new XF.Color('hsv', hsv);
		},

		setHsvValue: function(v)
		{
			var hsv = this.toHsv();
			hsv.v = v;

			return new XF.Color('hsv', hsv);
		},

		setHslHue: function(h)
		{
			var hsl = this.toHsl();
			hsl.h = h;

			return new XF.Color('hsl', hsl);
		},

		setHslSaturation: function(s)
		{
			var hsl = this.toHsl();
			hsl.s = s;

			return new XF.Color('hsl', hsl);
		},

		setHslLightness: function(l)
		{
			var hsl = this.toHsl();
			hsl.l = l;

			return new XF.Color('hsl', hsl);
		},

		setR: function(r)
		{
			var rgb = this.toRgb();
			rgb.r = r;

			return new XF.Color('rgb', rgb);
		},

		setG: function(g)
		{
			var rgb = this.toRgb();
			rgb.g = g;

			return new XF.Color('rgb', rgb);
		},

		setB: function(b)
		{
			var rgb = this.toRgb();
			rgb.b = b;

			return new XF.Color('rgb', rgb);
		},

		setAlpha: function(a)
		{
			var type, color;
			if (this.rgb)
			{
				color = this.rgb;
				type = 'rgb';
			}
			else if (this.hsv)
			{
				color = this.hsv;
				type = 'hsv';
			}
			else if (this.hsl)
			{
				color = this.hsl;
				type = 'hsl';
			}
			color.a = a;

			return new XF.Color(type, color);
		},

		toHsv: function()
		{
			var hsv = this.hsv;
			if (!hsv)
			{
				if (this.rgb)
				{
					var rgb = this.rgb;
					hsv = this._rgbToHsv(rgb.r, rgb.g, rgb.b);
				}
				else if (this.hsl)
				{
					var hsl = this.hsl;
					hsv = this._hslToHsv(hsl.h, hsl.s, hsl.l);
				}
			}

			hsv.a = this.a;

			return hsv;
		},

		_rgbToHsv: function(r, g, b)
		{
			var max,
				min,
				c,
				h,
				s,
				v;

			r /= 255;
			g /= 255;
			b /= 255;

			max = Math.max(r, g, b);
			min = Math.min(r, g, b);
			c = max - min;

			v = max;

			if (c == 0)
			{
				h = 0;
				s = 0;
			}
			else
			{
				switch (max)
				{
					case r: h = ((g - b) / c) % 6; break;
					case g: h = (b - r) / c + 2; break;
					case b: h = (r - g) / c + 4; break;
				}

				h = Math.round(60 * h);
				if (h < 0)
				{
					h += 360;
				}

				s = c / v;
			}

			return {
				h: h,
				s: s,
				v: v
			};
		},

		_hslToHsv: function(h, s, l)
		{
			if (l < .5)
			{
				s *= l;
			}
			else
			{
				s *= 1 - l;
			}

			return {
				h: h,
				s: 2 * s / (l + s),
				v: l + s
			};
		},

		toRgb: function()
		{
			var rgb = this.rgb;
			if (!rgb)
			{
				if (this.hsv)
				{
					var hsv = this.hsv;
					rgb = this._hsvToRgb(hsv.h, hsv.s, hsv.v);
				}
				else if (this.hsl)
				{
					var hsl = this.hsl;
					rgb = this._hslToRgb(hsl.h, hsl.s, hsl.l);
				}
			}

			rgb.a = this.a;

			return rgb;
		},

		_hsvToRgb: function(h, s, v)
		{
			var c, hAlt, x, modifier, r, g, b;

			c = v * s;
			hAlt = h / 60;
			x = c * (1 - Math.abs(hAlt % 2 - 1));

			if (hAlt < 1)      { r = c; g = x; b = 0; }
			else if (hAlt < 2) { r = x; g = c; b = 0; }
			else if (hAlt < 3) { r = 0; g = c; b = x; }
			else if (hAlt < 4) { r = 0; g = x; b = c; }
			else if (hAlt < 5) { r = x; g = 0; b = c; }
			else if (hAlt < 6) { r = c; g = 0; b = x; }

			modifier = v - c;

			return {
				r: Math.round(255 * (r + modifier)),
				g: Math.round(255 * (g + modifier)),
				b: Math.round(255 * (b + modifier))
			};
		},

		_hslToRgb: function(h, s, l)
		{
			h /= 360;

			var r, g, b;

			if (s == 0)
			{
				r = g = b = l * 255;
			}
			else
			{
				var t2, t1;

				if (l < 0.5)
				{
					t2 = l * (1 + s);
				}
				else
				{
					t2 = (l + s) - (s * l);
				}

				t1 = 2 * l - t2;

				var hueToColor = function(h)
				{
					if (h < 0)
					{
						h++;
					}
					if (h > 1)
					{
						h--;
					}

					if ((6 * h) < 1)
					{
						return (t1 + (t2 - t1) * 6 * h);
					}
					if ((2 * h) < 1)
					{
						return t2;
					}
					if ((3 * h) < 2)
					{
						return (t1 + (t2 - t1) * ((2 / 3) - h) * 6);
					}

					return t1;
				};

				r = 255 * hueToColor(h + 1/3);
				g = 255 * hueToColor(h);
				b = 255 * hueToColor(h - 1/3);
			}

			return {
				r: Math.round(r),
				g: Math.round(g),
				b: Math.round(b)
			};
		},

		toHsl: function()
		{
			var hsl = this.hsl;
			if (!hsl)
			{
				if (this.hsv)
				{
					var hsv = this.hsv;
					hsl = this._hsvToHsl(hsv.h, hsv.s, hsv.v);
				}
				else if (this.rgb)
				{
					var rgb = this.rgb;
					hsl = this._rgbToHsl(rgb.r, rgb.g, rgb.b);
				}
			}

			hsl.a = this.a;

			return hsl;
		},

		_hsvToHsl: function(h, s, v)
		{
			var adjuster = (2 - s) * v;

			return {
				h: h,
				s: s * v / (adjuster < 1 ? adjuster : 2 - adjuster),
				l: adjuster / 2
			};
		},

		_rgbToHsl: function(r, g, b)
		{
			r /= 255;
			g /= 255;
			b /= 255;

			var max = Math.max(r, g, b),
				min = Math.min(r, g, b),
				h,
				s,
				l = (max + min) / 2;

			if (max == min)
			{
				h = 0;
				s = 0;
			}
			else
			{
				var diff = max - min;

				if (l > .5)
				{
					s = diff / (2 - max - min);
				}
				else
				{
					s = diff / (max + min);
				}

				switch (max)
				{
					case r: h = (g - b) / diff; break;
					case g: h = 2 + (b - r) / diff; break;
					case b: h = 4 + (r - g) / diff; break;
					default: h = 0;
				}

				h *= 60;
				if (h < 0)
				{
					h += 360;
				}
			}

			return {
				h: h,
				s: s,
				l: l
			};
		},

		toCss: function()
		{
			var rgb = this.toRgb();

			if (rgb.a == 1)
			{
				return 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
			}
			else
			{
				var alpha = Math.round(rgb.a * 100) / 100;

				return 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ', ' + alpha + ')';
			}
		},

		toPrintable: function()
		{
			if (this.name)
			{
				return this.name;
			}
			else
			{
				return this.toCss();
			}
		}
	});
	XF.Color.fromString = function(str, name)
	{
		str = $.trim(str);

		if (str.length == 0)
		{
			return null;
		}

		var match, r = 0, g = 0, b = 0, a = 1, color = null;

		var adjust,
			percentMatch,
			percent = null;

		if (match = str.match(/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i))
		{
			r = parseInt(match[1], 16);
			g = parseInt(match[2], 16);
			b = parseInt(match[3], 16);
			return new XF.Color('rgb', [r, g, b, 1], name);
		}
		else if (match = str.match(/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i))
		{
			r = parseInt(match[1] + match[1], 16);
			g = parseInt(match[2] + match[2], 16);
			b = parseInt(match[3] + match[3], 16);
			return new XF.Color('rgb', [r, g, b, 1], name);
		}
		else if (match = str.match(/^rgb\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)$/i))
		{
			r = match[1];
			g = match[2];
			b = match[3];
			return new XF.Color('rgb', [r, g, b, 1], name);
		}
		else if (match = str.match(/^rgba\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*(([0-9]*\.)?[0-9]+)\s*\)$/i))
		{
			r = match[1];
			g = match[2];
			b = match[3];
			a = match[4];
			return new XF.Color('rgb', [r, g, b, a], name);
		}
		else if (match = str.match(
			/^(xf-diminish|xf-intensify|lighten|darken|fade)\(\s*([a-z0-9_]*\([^)]*\)\s*|[^,]+),([^)]+)\)$/i
		))
		{
			var baseColor = XF.Color.fromString($.trim(match[2]), name || str);
			if (!baseColor)
			{
				return null;
			}

			if (percentMatch = $.trim(match[3]).match(/^([0-9.]+)%/))
			{
				percent = parseFloat(percentMatch[1]);
			}

			if (match[1] == 'xf-intensify')
			{
				match[1] = (XF.Color.styleType == 'light' ? 'darken' : 'lighten');
			}
			else if (match[1] == 'xf-diminish')
			{
				match[1] = (XF.Color.styleType == 'light' ? 'lighten' : 'darken');
			}

			var hsl;
			switch (match[1].toLowerCase())
			{
				case 'darken':
					if (percent)
					{
						hsl = baseColor.toHsl();
						hsl.l = Math.max(0, hsl.l - percent / 100);
						return new XF.Color('hsl', hsl, name || str);
					}
					break;

				case 'lighten':
					if (percent)
					{
						hsl = baseColor.toHsl();
						hsl.l = Math.min(1, hsl.l + percent / 100);
						return new XF.Color('hsl', hsl, name || str);
					}
					break;

				case 'fade':
					if (percent)
					{
						baseColor = baseColor.setAlpha(percent / 100);
						return baseColor.setName(name || str);
					}
					break;
			}

			return baseColor;
		}
		else if (match = str.match(
			/^(mix)\(\s*([a-z0-9_]*\([^)]*\)\s*|[^,]+),\s*([a-z0-9_]*\([^)]*\)\s*|[^,]+)(,([^)]+))?\)$/i
		))
		{
			var mixColor1 = XF.Color.fromString($.trim(match[2]), name || str),
				mixColor2 = XF.Color.fromString($.trim(match[3]), name || str);
			if (!mixColor1 || !mixColor2)
			{
				return null;
			}

			if (match[5])
			{
				if (percentMatch = $.trim(match[5]).match(/^([0-9.]+)%/))
				{
					percent = parseFloat(percentMatch[1]);
				}
			}
			if (!percent)
			{
				percent = 50;
			}

			percent /= 100;

			var rgb1 = mixColor1.toRgb(),
				rgb2 = mixColor2.toRgb(),
				w = percent * 2 - 1,
				w1, w2,
				mixA = rgb1.a - rgb2.a;

			if (w * mixA == -1)
			{
				w1 = w;
			}
			else
			{
				w1 = (w + mixA) / (1 + w * mixA);
			}
			w1 = (w1 + 1) / 2;
			w2 = 1 - w1;

			return new XF.Color('rgb', [
				Math.round(rgb1.r * w1 + rgb2.r * w2),
				Math.round(rgb1.g * w1 + rgb2.g * w2),
				Math.round(rgb1.b * w1 + rgb2.b * w2),
				Math.round(rgb1.a * w1 + rgb2.a * w2)
			], name || str);
		}
		else if (XF.Color.namedColors[str.toLowerCase()])
		{
			return XF.Color.fromString(XF.Color.namedColors[str.toLowerCase()], name || str);
		}
		else
		{
			var mapColors = XF.Color.mapColors,
				firstMapped = false;

			if (XF.Color.mapVisited === null)
			{
				firstMapped = true;
				XF.Color.mapVisited = {};
			}

			for (var mapKey in mapColors)
			{
				if (mapColors.hasOwnProperty(mapKey) && mapColors[mapKey].colors[str])
				{
					if (XF.Color.mapVisited[str])
					{
						if (firstMapped)
						{
							XF.Color.mapVisited = null;
						}
						return '';
					}
					XF.Color.mapVisited[str] = true;

					var final = XF.Color.fromString(mapColors[mapKey].colors[str].value, name || str);

					if (firstMapped)
					{
						XF.Color.mapVisited = null;
					}

					return final;
				}
			}

			if (firstMapped)
			{
				XF.Color.mapVisited = null;
			}

			return null;
		}
	};

	XF.Color.styleType = 'light';

	XF.Color.namedColors = {};

	var $namedColors = $('.js-namedColors');
	if ($namedColors.length)
	{
		XF.Color.namedColors = $.parseJSON($namedColors.first().html()) || {};
	}

	XF.Color.mapColors = {};
	XF.Color.mapVisited = null;

	XF.ColorPicker = XF.Element.newHandler({
		options: {
			input: '| .input',
			box: '| .js-colorPickerTrigger',
			rgbTxt: '| .js-rgbTxt',
			allowPalette: true,
			mapName: null
		},

		$input: null,
		$box: null,
		inputColor: null,

		$rgbTxtR: null,
		$rgbTxtG: null,
		$rgbTxtB: null,

		menuInited: false,
		menu: null,
		menuColor: null,
		menuEls: {},

		allowReparse: true,

		init: function()
		{
			var $target = this.$target;

			this.$input = XF.findRelativeIf(this.options.input, $target);
			this.$input.on('keyup paste', XF.proxy(this, 'onInputUpdate'));

			this.$box = XF.findRelativeIf(this.options.box, $target);
			this.$box.append('<span class="colorPickerBox-sample" />');
			this.$box.click(XF.proxy(this, 'click'));

			this.$rgbTxtR = XF.findRelativeIf('| .js-rgbTxt-r', $target);
			this.$rgbTxtG = XF.findRelativeIf('| .js-rgbTxt-g', $target);
			this.$rgbTxtB = XF.findRelativeIf('| .js-rgbTxt-b', $target);

			this.updateFromInput();

			var self = this;
			$(document).on('color-picker:reparse', function()
			{
				if (!self.allowReparse)
				{
					return;
				}
				self.updateFromInput();
				self.destroyMenu();
			});
		},

		getInputColor: function(forceValid)
		{
			var color = this.inputColor;
			if (!color && forceValid)
			{
				color = new XF.Color('hsv', [0, 1, 1]);
			}

			return color;
		},

		updateFromInput: function(updateMap)
		{
			var val = this.$input.val();

			this.inputColor = XF.Color.fromString(val);
			this.updateBox();

			if (this.inputColor && updateMap)
			{
				this.updateMapColor(val);
				this.$input.trigger('change');
			}
		},

		updateMapColor: function(value)
		{
			var group,
				color,
				mapColors = XF.Color.mapColors,
				expectedName = this.options.mapName;

			if (!expectedName)
			{
				return;
			}

			for (group in mapColors)
			{
				if (!mapColors.hasOwnProperty(group))
				{
					continue;
				}

				for (color in mapColors[group].colors)
				{
					if (!mapColors[group].colors.hasOwnProperty(color) || color != expectedName)
					{
						continue;
					}

					mapColors[group].colors[color].value = value;
					this.allowReparse = false;
					$(document).trigger('color-picker:reparse');
					this.destroyMenu();
					this.allowReparse = true;
					break;
				}
			}
		},

		updateBox: function()
		{
			var color = this.getInputColor(),
				$box = this.$box,
				$sample = $box.find('.colorPickerBox-sample');

			if (color)
			{
				$box.removeClass('is-unknown').addClass('is-active');
				$sample.css('background-color', color.toCss());
			}
			else if (this.$input.val() == '')
			{
				$box.removeClass('is-active is-unknown');
				$sample.css('background-color', '');
			}
			else
			{
				$box.removeClass('is-active').addClass('is-unknown');
				$sample.css('background-color', '');
			}

			this.updateRgb(color);
		},

		updateRgb: function(color)
		{
			if (this.$rgbTxtR.length)
			{
				if (color)
				{
					var rgb = color.toRgb();

					this.$rgbTxtR.text(rgb.r);
					this.$rgbTxtG.text(rgb.g);
					this.$rgbTxtB.text(rgb.b);
				}
				else
				{
					this.$rgbTxtR.text('');
					this.$rgbTxtG.text('');
					this.$rgbTxtB.text('');
				}
			}
		},

		onInputUpdate: function()
		{
			this.updateFromInput(true);
		},

		setupMenu: function()
		{
			if (this.menuInited)
			{
				return;
			}
			this.menuInited = true;

			var $menu = this.getMenuEl();
			this.$box.after($menu);
			XF.activate($menu);

			this.menuEls.$propertyContainer = $menu.find('.colorPicker-propertyContainer');
			this.menuEls.$colorGrad = $menu.find('.colorPicker-colorGrad-color');
			this.menuEls.$colorGradIndicator = $menu.find('.colorPicker-colorGrad-indicator');
			this.menuEls.$hueBar = $menu.find('.colorPicker-hue-bar');
			this.menuEls.$hueIndicator = $menu.find('.colorPicker-hue-indicator');
			this.menuEls.$alphaBar = $menu.find('.colorPicker-alpha-bar');
			this.menuEls.$alphaIndicator = $menu.find('.colorPicker-alpha-indicator');
			this.menuEls.$previewOriginal = $menu.find('.colorPicker-preview-original');
			this.menuEls.$previewCurrent = $menu.find('.colorPicker-preview-current');
			this.menuEls.$input = $menu.find('.colorPicker-input');
			this.menuEls.$save = $menu.find('.colorPicker-save');

			this.menuEls.$propertyContainer.on('click', '[data-property]', XF.proxy(this, 'propertyClick'));
			this.menuEls.$colorGrad.on('mousedown', XF.proxy(this, 'colorGradMouseDown'));
			this.menuEls.$hueBar.on('mousedown', XF.proxy(this, 'hueBarMouseDown'));
			this.menuEls.$alphaBar.on('mousedown', XF.proxy(this, 'alphaBarMouseDown'));
			this.menuEls.$save.on('click', XF.proxy(this, 'save'));

			this.menu = new XF.MenuClick(this.$box, {});
		},

		destroyMenu: function()
		{
			if (!this.menuInited)
			{
				return;
			}

			this.closeMenu();
			this.menuInited = false;
			this.menu = null;
			this.menuColor = null;
			this.menuEls = {};
		},

		openMenu: function()
		{
			if (this.$input.prop('disabled'))
			{
				return;
			}

			this.setupMenu();

			this.menu.open();
		},

		closeMenu: function()
		{
			if (this.menu)
			{
				this.menu.close();
			}
		},

		getMenuEl: function()
		{
			var headers = [],
				bodies = [];

			if (this.options.allowPalette)
			{
				var mapColors = XF.Color.mapColors,
					thisMapName = this.options.mapName,
					colorGroupId,
					colorGroup,
					propName,
					propNameHtml,
					color,
					colorValue,
					colorOptions;

				for (colorGroupId in mapColors)
				{
					if (!mapColors.hasOwnProperty(colorGroupId))
					{
						continue;
					}

					colorGroup = mapColors[colorGroupId];
					colorOptions = '';

					for (propName in colorGroup.colors)
					{
						if (!colorGroup.colors.hasOwnProperty(propName))
						{
							continue;
						}
						if (thisMapName && thisMapName === propName)
						{
							continue;
						}

						color = colorGroup.colors[propName];
						colorValue = XF.Color.fromString(color.value);
						propNameHtml = XF.htmlspecialchars(propName);

						colorOptions += '<div class="colorPicker-property' + (colorValue ? '' : ' is-unknown')
							+ '" data-property="' + propNameHtml + '" title="' + propNameHtml + '">'
							+ '<span class="colorPicker-property-preview" style="background-color: '
							+ XF.htmlspecialchars(colorValue ? colorValue.toCss() : 'transparent') + '"></span>'
							+ XF.htmlspecialchars(color.title)
							+ '<span class="colorPicker-propName">' + propNameHtml + '</span></div>';
					}

					headers.push(colorGroup.title);
					bodies.push('<div class="colorPicker-propertyContainer">' + colorOptions + '</div>');
				}
			}

			var sliders = ''
				+ '<div class="colorPicker-sliders" dir="ltr">'
					+ '<div class="colorPicker-hue"><div class="colorPicker-hue-bar"><span class="colorPicker-hue-indicator"></span></div></div>'
					+ '<div class="colorPicker-colorGrad"><div class="colorPicker-colorGrad-color"><div class="colorPicker-colorGrad-sat"><div class="colorPicker-colorGrad-val"><span class="colorPicker-colorGrad-indicator"></span></div></div></div></div>'
					+ '<div class="colorPicker-alpha"><div class="colorPicker-alpha-bar"><span class="colorPicker-alpha-indicator"></span></div></div>'
				+ '</div>';

			headers.push(XF.phrase('picker'));
			bodies.push(sliders);

			var mainParts = '';

			if (headers.length > 1)
			{
				var i;

				// This intentionally loops over headers twice. They should be identical, but in case they aren't,
				// we need to make sure we at least have the same number of tabs and panes.

				mainParts += '<h4 class="menu-tabHeader tabs hScroller" data-xf-init="tabs h-scroller" role="tablist">'
					+ '<span class="hScroller-scroll">';
				for (i = 0; i < headers.length; i++)
				{
					mainParts += '<a class="tabs-tab' + (i == 0 ? ' is-active' : '')
						+ '" role="tab" tabindex="0">'
						+ XF.htmlspecialchars(headers[i]) + '</a>';
				}
				mainParts += '</span></h4>';

				mainParts += '<ul class="tabPanes">';
				for (i = 0; i < headers.length; i++)
				{
					mainParts += '<li class="' + (i == 0 ? 'is-active' : '') + '" role="tabpanel">' + bodies[i] + '</li>';
				}
				mainParts += '</ul>';
			}
			else if (headers.length == 1)
			{
				mainParts = bodies[0];
			}

			var inputs = ''
				+ '<div class="colorPicker-inputs">'
					+ '<div class="colorPicker-preview"><span class="colorPicker-preview-original"></span><span class="colorPicker-preview-current"></span></div>'
					+ '<div class="colorPicker-inputContainer"><input type="text" class="input colorPicker-input" dir="ltr" /></div>'
					+ '<div class="colorPicker-saveContainer"><button class="button button--primary colorPicker-save"><span class="button-text">' + XF.phrase('update') + '</span></button></div>'
				+ '</div>';

			var html = ''
				+ '<div class="menu menu--colorPicker" data-menu="menu" aria-hidden="true"><div class="menu-content colorPicker">'
					+ mainParts
					+ inputs
				+ '</div></div>';

			return $($.parseHTML(html));
		},

		propertyClick: function(e)
		{
			e.preventDefault();

			var $target = $(e.currentTarget),
				property = $target.data('property');
			if (property)
			{
				var color = XF.Color.fromString(property);
				if (!color)
				{
					color = XF.Color.fromString('transparent', property);
				}

				this.updateMenuColor(color);
			}
		},

		colorGradMouseDown: function(e)
		{
			this.colorGradMouseAction(e);

			var self = this;

			$(document).on({
				'mouseup.colorpicker': function()
				{
					$(document).off('.colorpicker');
				},
				'mousemove.colorpicker': function(e)
				{
					self.colorGradMouseAction(e);
				}
			});
		},

		colorGradMouseAction: function(e)
		{
			e.preventDefault();

			var $grad = this.menuEls.$colorGrad,
				offset = $grad.offset(),
				x = e.pageX - offset.left,
				y = e.pageY - offset.top;

			var s = Math.max(0, Math.min(1, x / $grad.width())),
				v = Math.max(0, Math.min(1, 1 - y / $grad.height()));

			var color = this.getMenuColor(true).setHsvSaturation(s).setHsvValue(v);
			this.updateMenuColor(color);
		},

		hueBarMouseDown: function(e)
		{
			this.hueBarMouseAction(e);

			var self = this;

			$(document).on({
				'mouseup.colorpicker': function()
				{
					$(document).off('.colorpicker');
				},
				'mousemove.colorpicker': function(e)
				{
					self.hueBarMouseAction(e);
				}
			});
		},

		hueBarMouseAction: function(e)
		{
			e.preventDefault();

			var $bar = this.menuEls.$hueBar,
				hue = 359 * (e.pageY - $bar.offset().top) / $bar.height();

			hue = Math.round(Math.max(0, Math.min(359, hue)));

			var color = this.getMenuColor(true).setHsvHue(hue);
			this.updateMenuColor(color);
		},

		alphaBarMouseDown: function(e)
		{
			this.alphaBarMouseAction(e);

			var self = this;

			$(document).on({
				'mouseup.colorpicker': function()
				{
					$(document).off('.colorpicker');
				},
				'mousemove.colorpicker': function(e)
				{
					self.alphaBarMouseAction(e);
				}
			});
		},

		alphaBarMouseAction: function(e)
		{
			e.preventDefault();

			var $bar = this.menuEls.$alphaBar,
				alpha = (e.pageX - $bar.offset().left) / $bar.width();

			alpha = 1 - Math.max(0, Math.min(1, alpha));

			var color = this.getMenuColor(true).setAlpha(alpha);
			this.updateMenuColor(color);
		},

		save: function()
		{
			var val = this.menuEls.$input.val();

			this.closeMenu();
			this.$input.val(val);
			this.updateFromInput(true);
		},

		getMenuColor: function(forceValid)
		{
			var menuColor = this.menuColor;
			if (!menuColor && forceValid)
			{
				menuColor = new XF.Color('hsv', [0, 1, 1]);
			}

			return menuColor;
		},

		updateMenuColor: function(color, isMenuOpening)
		{
			this.menuColor = color;
			this.updatePickerData(isMenuOpening);
		},

		updatePickerData: function(isMenuOpening)
		{
			var validMenuColor = this.getMenuColor(true),
				menuColor = this.getMenuColor();

			this.updatePickerSelections(validMenuColor);
			this.updateSelectedPropertyColor(menuColor);

			if (menuColor)
			{
				this.menuEls.$input.val(menuColor.toPrintable());
				this.menuEls.$previewCurrent.css('background-color', menuColor.toCss());
			}
			else
			{
				if (isMenuOpening)
				{
					this.menuEls.$input.val('');
				}
				// if not opening, we don't have a valid value so we have to just leave it as is since they might be typing

				this.menuEls.$previewCurrent.css('background-color', '');
			}
		},

		updateSelectedPropertyColor: function(color)
		{
			var $properties = this.menuEls.$propertyContainer.find('[data-property]'),
				$matchProperty = this.getPropertyColorMatch(color);

			$properties.removeClass('is-active');
			if ($matchProperty)
			{
				$matchProperty.addClass('is-active');
			}
		},

		getPropertyColorMatch: function(color)
		{
			var $properties = this.menuEls.$propertyContainer.find('[data-property]'),
				$property = null;

			if (color && color.name)
			{
				$properties.each(function()
				{
					var $this = $(this);
					if ($this.data('property') == color.name)
					{
						$property = $this;
						return false;
					}
				});
			}

			return $property;
		},

		updatePickerSelections: function(color)
		{
			var hsv = color.toHsv(),
				grad = new XF.Color('hsv', [hsv.h, 1, 1]);

			var x = (hsv.s * 100) + '%',
				y = (100 - (hsv.v * 100)) + '%',
				hueY = ((hsv.h / 359) * 100) + '%',
				alphaLeft = (100 - hsv.a * 100) + '%';

			this.menuEls.$colorGradIndicator.css({
				top: y,
				left: x
			});
			this.menuEls.$colorGrad.css('background-color', grad.toCss());
			this.menuEls.$hueIndicator.css('top', hueY);

			var alphaColor = color.setAlpha(1),
				transparentColor = color.setAlpha(0);

			this.menuEls.$alphaBar.css(
				'background-image',
				'linear-gradient(to right, ' + alphaColor.toCss() + ', ' + transparentColor.toCss() + ')'
			);
			this.menuEls.$alphaIndicator.css('left', alphaLeft);
		},

		onMenuOpen: function()
		{
			var inputColor = this.getInputColor(),
				$preview = this.menuEls.$previewOriginal;

			this.updateMenuColor(inputColor, true);

			if (inputColor)
			{
				$preview.css('background-color', inputColor.toCss());
			}
			else
			{
				$preview.css('background-color', '');
			}
		},

		click: function(e)
		{
			if (this.$input.prop('disabled'))
			{
				if (this.menu)
				{
					this.closeMenu();
				}
				return;
			}

			this.setupMenu();

			if (!this.menu.isOpen())
			{
				this.onMenuOpen();
			}

			this.menu.click(e);
		}
	});

	XF.Element.register('color-picker', 'XF.ColorPicker');

	$(document).on('xf:page-load-start', function()
	{
		var colorData,
			$colorPickerData = $('.js-colorPickerData');

		if ($colorPickerData.length)
		{
			try
			{
				colorData = $.parseJSON($('.js-colorPickerData').first().html()) || {};
			}
			catch (e)
			{
				console.error(e);
				colorData = {};
			}

			if (colorData.colors)
			{
				XF.Color.mapColors = $.extend({}, XF.Color.mapColors, colorData.colors);
			}
			if (colorData.config)
			{
				XF.Color.styleType = colorData.config.styleType || 'light';
			}
		}
	});
}
(jQuery, window, document);