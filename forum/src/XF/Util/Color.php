<?php

namespace XF\Util;

class Color
{
	protected static $namedColors = [
		'aliceblue' => '#f0f8ff',
		'antiquewhite' => '#faebd7',
		'aqua' => '#00ffff',
		'aquamarine' => '#7fffd4',
		'azure' => '#f0ffff',
		'beige' => '#f5f5dc',
		'bisque' => '#ffe4c4',
		'black' => '#000000',
		'blanchedalmond' => '#ffebcd',
		'blue' => '#0000ff',
		'blueviolet' => '#8a2be2',
		'brown' => '#a52a2a',
		'burlywood' => '#deb887',
		'cadetblue' => '#5f9ea0',
		'chartreuse' => '#7fff00',
		'chocolate' => '#d2691e',
		'coral' => '#ff7f50',
		'cornflowerblue' => '#6495ed',
		'cornsilk' => '#fff8dc',
		'crimson' => '#dc143c',
		'cyan' => '#00ffff',
		'darkblue' => '#00008b',
		'darkcyan' => '#008b8b',
		'darkgoldenrod' => '#b8860b',
		'darkgray' => '#a9a9a9',
		'darkgreen' => '#006400',
		'darkkhaki' => '#bdb76b',
		'darkmagenta' => '#8b008b',
		'darkolivegreen' => '#556b2f',
		'darkorange' => '#ff8c00',
		'darkorchid' => '#9932cc',
		'darkred' => '#8b0000',
		'darksalmon' => '#e9967a',
		'darkseagreen' => '#8fbc8f',
		'darkslateblue' => '#483d8b',
		'darkslategray' => '#2f4f4f',
		'darkturquoise' => '#00ced1',
		'darkviolet' => '#9400d3',
		'deeppink' => '#ff1493',
		'deepskyblue' => '#00bfff',
		'dimgray' => '#696969',
		'dodgerblue' => '#1e90ff',
		'firebrick' => '#b22222',
		'floralwhite' => '#fffaf0',
		'forestgreen' => '#228b22',
		'fuchsia' => '#ff00ff',
		'gainsboro' => '#dcdcdc',
		'ghostwhite' => '#f8f8ff',
		'gold' => '#ffd700',
		'goldenrod' => '#daa520',
		'gray' => '#808080',
		'green' => '#008000',
		'greenyellow' => '#adff2f',
		'honeydew' => '#f0fff0',
		'hotpink' => '#ff69b4',
		'indianred' => '#cd5c5c',
		'indigo' => '#4b0082',
		'ivory' => '#fffff0',
		'khaki' => '#f0e68c',
		'lavender' => '#e6e6fa',
		'lavenderblush' => '#fff0f5',
		'lawngreen' => '#7cfc00',
		'lemonchiffon' => '#fffacd',
		'lightblue' => '#add8e6',
		'lightcoral' => '#f08080',
		'lightcyan' => '#e0ffff',
		'lightgoldenrodyellow' => '#fafad2',
		'lightgrey' => '#d3d3d3',
		'lightgreen' => '#90ee90',
		'lightpink' => '#ffb6c1',
		'lightsalmon' => '#ffa07a',
		'lightseagreen' => '#20b2aa',
		'lightskyblue' => '#87cefa',
		'lightslategray' => '#778899',
		'lightsteelblue' => '#b0c4de',
		'lightyellow' => '#ffffe0',
		'lime' => '#00ff00',
		'limegreen' => '#32cd32',
		'linen' => '#faf0e6',
		'magenta' => '#ff00ff',
		'maroon' => '#800000',
		'mediumaquamarine' => '#66cdaa',
		'mediumblue' => '#0000cd',
		'mediumorchid' => '#ba55d3',
		'mediumpurple' => '#9370d8',
		'mediumseagreen' => '#3cb371',
		'mediumslateblue' => '#7b68ee',
		'mediumspringgreen' => '#00fa9a',
		'mediumturquoise' => '#48d1cc',
		'mediumvioletred' => '#c71585',
		'midnightblue' => '#191970',
		'mintcream' => '#f5fffa',
		'mistyrose' => '#ffe4e1',
		'moccasin' => '#ffe4b5',
		'navajowhite' => '#ffdead',
		'navy' => '#000080',
		'oldlace' => '#fdf5e6',
		'olive' => '#808000',
		'olivedrab' => '#6b8e23',
		'orange' => '#ffa500',
		'orangered' => '#ff4500',
		'orchid' => '#da70d6',
		'palegoldenrod' => '#eee8aa',
		'palegreen' => '#98fb98',
		'paleturquoise' => '#afeeee',
		'palevioletred' => '#d87093',
		'papayawhip' => '#ffefd5',
		'peachpuff' => '#ffdab9',
		'peru' => '#cd853f',
		'pink' => '#ffc0cb',
		'plum' => '#dda0dd',
		'powderblue' => '#b0e0e6',
		'purple' => '#800080',
		'red' => '#ff0000',
		'rosybrown' => '#bc8f8f',
		'royalblue' => '#4169e1',
		'saddlebrown' => '#8b4513',
		'salmon' => '#fa8072',
		'sandybrown' => '#f4a460',
		'seagreen' => '#2e8b57',
		'seashell' => '#fff5ee',
		'sienna' => '#a0522d',
		'silver' => '#c0c0c0',
		'skyblue' => '#87ceeb',
		'slateblue' => '#6a5acd',
		'slategray' => '#708090',
		'snow' => '#fffafa',
		'springgreen' => '#00ff7f',
		'steelblue' => '#4682b4',
		'tan' => '#d2b48c',
		'teal' => '#008080',
		'thistle' => '#d8bfd8',
		'tomato' => '#ff6347',
		'transparent' => 'rgba(0, 0, 0, 0)',
		'turquoise' => '#40e0d0',
		'violet' => '#ee82ee',
		'wheat' => '#f5deb3',
		'white' => '#ffffff',
		'whitesmoke' => '#f5f5f5',
		'yellow' => '#ffff00',
		'yellowgreen' => '#9acd32'
	];

	public static function coerceHexColor($hex)
	{
		$hex = ltrim($hex, '#');
		if (preg_match('/^[0-9A-F]{3}$|^[0-9A-F]{6}$/i', $hex))
		{
			if (strlen($hex) < 6)
			{
				$parts = str_split($hex, 1);
				$hex = "{$parts[0]}{$parts[0]}{$parts[1]}{$parts[1]}{$parts[2]}{$parts[2]}";
			}
			return $hex;
		}
		else
		{
			return 'FF0000';
		}
	}

	public static function hexToHsl($hex)
	{
		return self::rgbToHsl(self::hexToRgb($hex));
	}

	public static function hexToRgb($hex)
	{
		$hex = self::coerceHexColor($hex);

		$parts = str_split($hex, 2);
		$r = hexdec($parts[0]);
		$g = hexdec($parts[1]);
		$b = hexdec($parts[2]);

		return [$r, $g, $b];
	}

	/**
	 * Convert an RGB value to HSL
	 * @url http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/
	 *
	 * @param $r
	 * @param null $g
	 * @param null $b
	 *
	 * @return array
	 */
	public static function rgbToHsl($r, $g = null, $b = null)
	{
		if (is_array($r))
		{
			list ($r, $g, $b) = $r;
		}

		$r /= 255;
		$g /= 255;
		$b /= 255;

		$max = max($r, $g, $b);
		$min = min($r, $g, $b);
		$l = ($max + $min) / 2;

		if ($max == $min)
		{
			// greyscale
			$h = 0;
			$s = 0;
		}
		else
		{
			$diff = $max - $min;

			if ($l > .5)
			{
				$s = $diff / (2 - $max - $min);
			}
			else
			{
				$s = $diff / ($max + $min);
			}

			switch ($max)
			{
				case $r: $h = ($g - $b) / $diff; break;
				case $g: $h = 2 + ($b - $r) / $diff; break;
				case $b: $h = 4 + ($r - $g) / $diff; break;
				default: $h = 0;
			}

			$h *= 60;
			if ($h < 0)
			{
				$h += 360;
			}
		}

		return [
			round($h),
			round($s * 100),
			round($l * 100)
		];
	}

	public static function rgbToHex($r, $g = null, $b = null)
	{
		if (is_array($r))
		{
			list ($r, $g, $b) = $r;
		}

		return (
			str_pad(dechex(intval($r)), 2, '0', STR_PAD_LEFT)
			. str_pad(dechex(intval($g)), 2, '0', STR_PAD_LEFT)
			. str_pad(dechex(intval($b)), 2, '0', STR_PAD_LEFT)
		);
	}

	public static function hslToRgb($h, $s = null, $l = null)
	{
		if (is_array($h))
		{
			list ($h, $s, $l) = $h;
		}

		$h /= 360;
		$s /= 100;
		$l /= 100;

		if ($s == 0)
		{
			$r = $g = $b = $l * 255;
		}
		else
		{
			if ($l < 0.5)
			{
				$t2 = $l * (1 + $s);
			}
			else
			{
				$t2 = ($l + $s) - ($s * $l);
			}

			$t1 = 2 * $l - $t2;

			$r = 255 * self::hueToRgb($t1, $t2, $h + (1 / 3));
			$g = 255 * self::hueToRgb($t1, $t2, $h);
			$b = 255 * self::hueToRgb($t1, $t2, $h - (1 / 3));
		}

		return [round($r), round($g), round($b)];
	}

	protected static function hueToRgb($t1, $t2, $h)
	{
		if ($h < 0)
		{
			$h += 1;
		}
		if ($h > 1)
		{
			$h -= 1;
		}
		if ((6 * $h) < 1)
		{
			return ($t1 + ($t2 - $t1) * 6 * $h);
		}
		if ((2 * $h) < 1)
		{
			return $t2;
		}
		if ((3 * $h) < 2)
		{
			return ($t1 + ($t2 - $t1) * ((2 / 3) - $h) * 6);
		}
		return $t1;
	}

	public static function hslToHex($h, $s = null, $l = null)
	{
		if (is_array($h))
		{
			list ($h, $s, $l) = $h;
		}

		return self::rgbToHex(self::hslToRgb($h, $s, $l));
	}

	public static function colorToRgb($color)
	{
		$color = trim(strtolower($color));
		if (isset(self::$namedColors[$color]))
		{
			$color = self::$namedColors[$color];
		}

		if (!self::isValidColor($color))
		{
			return null;
		}

		if ($color && $color[0] === "#")
		{
			return self::hexToRgb(substr($color, 1));
		}

		if (preg_match("/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i", $color, $match))
		{
			return [intval($match[1]), intval($match[2]), intval($match[3])];
		}

		if (preg_match("/^hsl?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i", $color, $match))
		{
			return self::hslToRgb(intval($match[1]), intval($match[2]), intval($match[3]));
		}

		return null;
	}

	public static function getRelativeLuminance($r, $g = null, $b = null)
	{
		if (is_array($r))
		{
			$b = $r[2];
			$g = $r[1];
			$r = $r[0];
		}

		$scaler = function($color)
		{
			$color /= 255;
			if ($color <= 0.03928)
			{
				return $color / 12.92;
			}
			else
			{
				return pow(($color + 0.055) / 1.055, 2.4);
			}
		};

		$r = $scaler($r);
		$g = $scaler($g);
		$b = $scaler($b);

		return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
	}

	public static function darkenOrLightenHsl($h, $s = null, $l = null, $percent = null)
	{
		if (is_array($h))
		{
			$percent = $s;
			$l = $h[2];
			$s = $h[1];
			$h = $h[0];
		}

		$rgb = self::hslToRgb($h, $s, $l);
		if (self::getRelativeLuminance($rgb) > 0.179)
		{
			return self::darkenHsl($h, $s, $l, $percent);
		}
		else
		{
			return self::lightenHsl($h, $s, $l, $percent);
		}
	}

	public static function darkenHsl($h, $s = null, $l = null, $percent = null)
	{
		if (is_array($h))
		{
			$percent = $s;
			list ($h, $s, $l) = $h;
		}

		$l -= $percent;

		return [$h, $s, max(0, $l)];
	}

	public static function lightenHsl($h, $s = null, $l = null, $percent = null)
	{
		if (is_array($h))
		{
			$percent = $s;
			list ($h, $s, $l) = $h;
		}

		$l += $percent;

		return [$h, $s, min(100, $l)];
	}

	public static function isValidColor($color)
	{
		// https://gist.github.com/olmokramer/82ccce673f86db7cda5e (HatScripts, 3rd May)

		if (array_key_exists(utf8_strtolower($color), self::$namedColors))
		{
			return true;
		}

		if ($color && $color[0] === "#")
		{
			$color = substr($color, 1);
			return in_array(strlen($color), [3, 4, 6, 8]) && ctype_xdigit($color);
		}
		else
		{
			return preg_match("/^(rgb|hsl)a?\((\d+%?(deg|rad|grad|turn)?[,\s]+){2,3}[\s\/]*[\d\.]+%?\)$/i", $color);
		}
	}

	public static function getNamedColors()
	{
		return self::$namedColors;
	}
}