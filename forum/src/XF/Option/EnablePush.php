<?php

namespace XF\Option;

class EnablePush extends AbstractOption
{
	protected static function canEnablePush(&$error = null)
	{
		if (PHP_VERSION_ID < 70100)
		{
			$error = \XF::phrase('enabling_push_notifications_requires_php_7_1', ['phpversion' => phpversion()]);
			return false;
		}

		$extensions = [
			'gmp',
			'mbstring',
			'openssl'
		];

		$missing = [];
		foreach ($extensions AS $extension)
		{
			if (!extension_loaded($extension))
			{
				$missing[] = $extension;
			}
		}

		if ($missing)
		{
			$error = \XF::phrase('enabling_push_notifications_requires_php_to_have_following_extensions', [
				'extensions' => implode(', ', $missing)
			]);
			return false;
		}

		$request = \XF::app()->request();

		if (!$request->isHostLocal() && !$request->isSecure())
		{
			$error = \XF::phrase('enabling_push_notifications_requires_site_to_be_accessible_over_https');
			return false;
		}

		return true;
	}

	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		$canEnablePush = self::canEnablePush($error);

		return self::getTemplate('admin:option_template_enablePush', $option, $htmlParams, [
			'canEnablePush' => $canEnablePush,
			'error' => $error
		]);
	}

	public static function verifyOption(&$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			return true;
		}

		$canEnablePush = self::canEnablePush($error);

		if ($value === 1 && !$canEnablePush)
		{
			$option->error($error);
			return false;
		}

		$options = \XF::options();

		if ($value === 1
			&& !$options->pushKeysVAPID['publicKey']
			&& !$options->pushKeysVAPID['privateKey']
		)
		{
			/** @var \XF\Repository\Option $optionRepo */
			$optionRepo = \XF::repository('XF:Option');

			$optionRepo->updateOptionSkipVerify(
				'pushKeysVAPID', \Minishlink\WebPush\VAPID::createVapidKeys()
			);
		}

		return true;
	}

	public static function verifyVapidKeysOption(&$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			return true;
		}

		if ($option->option_value['publicKey'] || $option->option_value['privateKey'])
		{
			$changes = false;

			if ($value['publicKey'] !== $option->option_value['publicKey'])
			{
				$changes = true;
			}
			if ($value['privateKey'] !== $option->option_value['privateKey'])
			{
				$changes = true;
			}

			if ($changes)
			{
				$option->error(\XF::phrase('it_is_not_possible_to_change_vapid_keys_after_they_have_been_set'));
				return false;
			}
		}

		return true;
	}
}