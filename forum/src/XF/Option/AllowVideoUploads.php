<?php

namespace XF\Option;

class AllowVideoUploads extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		$serverMaxFileSize = \XF::app()->uploadMaxFilesize / 1024;

		return self::getTemplate('admin:option_template_allowVideoUploads', $option, $htmlParams, [
			'explainHtml' => \XF::phrase('option_explain.' . $option->option_id,
				[
					'allowedVideoExtensions' => implode(
						', ', array_keys(\XF::app()->inlineVideoTypes)
					),
					'serverMaxFileSize' => \XF::language()->numberFormat($serverMaxFileSize)
				]
			),
			'max' => $serverMaxFileSize
		]);
	}

	public static function verifyOption(&$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			$value['size'] = \XF::options()->attachmentMaxFileSize;
		}
		else
		{
			if (isset($value['size']) && $value['size'] * 1024 > \XF::app()->uploadMaxFilesize)
			{
				$option->error(\XF::phrase('value_greater_than_server_config_allows'), $option->option_id);
				return false;
			}
		}

		return true;
	}
}