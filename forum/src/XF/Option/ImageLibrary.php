<?php

namespace XF\Option;

class ImageLibrary extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		return self::getTemplate('admin:option_template_imageLibrary', $option, $htmlParams, [
			'noImagick' => !class_exists('Imagick')
		]);
	}

	public static function verifyOption(&$value, \XF\Entity\Option $option)
	{
		if ($value == 'imPecl' && !class_exists('Imagick'))
		{
			$option->error(\XF::phrase('must_have_imagick_pecl_extension', ['link' => 'http://pecl.php.net/package/imagick']), $option->option_id);
			return false;
		}

		return true;
	}
}