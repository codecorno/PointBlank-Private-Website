<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class TemplateModification extends Repository
{
	/**
	 * @return Finder
	 */
	public function findTemplateModificationsForList($type)
	{
		return $this->finder('XF:TemplateModification')
			->with(['AddOn'])
			->where('type', $type)
			->whereAddOnActive()
			->order(['addon_id', 'type', 'template', 'execution_order']);
	}

	public function getModificationTypes()
	{
		$types = [
			'public' => \XF::phrase('public'),
			'email' => \XF::phrase('email')
		];

		if ($this->canCreateTemplateModification())
		{
			$types['admin'] = \XF::phrase('admin');
		}

		return $types;
	}

	public function canCreateTemplateModification()
	{
		return \XF::$developmentMode;
	}

	public function applyModificationsToTemplate($type, $title, $template, &$status = [])
	{
		$modifications = $this->finder('XF:TemplateModification')
			->where([
				'type' => $type,
				'template' => $title,
				'enabled' => 1
			])
			->whereAddOnActive()
			->order('execution_order')
			->fetch();
		return $this->applyTemplateModifications($template, $modifications->toArray(), $status);
	}

	public function applyTemplateModifications($template, array $modifications, &$status = [])
	{
		$status = [];

		foreach ($modifications AS $id => $modification)
		{
			$template = str_replace("\r\n", "\n", $template);
			$modification = $modification->toArray();

			switch ($modification['action'])
			{
				case 'str_replace':
					$modification['find'] = str_replace("\r\n", "\n", $modification['find']);
					$modification['replace'] = str_replace('$0', $modification['find'], $modification['replace']);

					$status[$id] = substr_count($template, $modification['find']);
					$template = str_replace($modification['find'], $modification['replace'], $template);
					break;

				case 'preg_replace':
				case 'callback':
				$modification['find'] = str_replace(
					["\r\n", '\r\n'],
					["\n", '\n'],
					trim($modification['find'])
				);

				try
				{
					if (preg_match('/\W[\s\w]*e[\s\w]*$/', $modification['find']))
					{
						// can't run a /e regex
						$status[$id] = 'error_invalid_regex';
					}
					else
					{
						$status[$id] = preg_match_all($modification['find'], $template, $null);
					}
				}
				catch (\ErrorException $e)
				{
					$status[$id] = 'error_invalid_regex';
					break;
				}

				if ($modification['action'] == 'callback')
				{
					if ($this->app()->config('enableTemplateModificationCallbacks'))
					{
						if (preg_match('/^([a-z0-9_\\\\]+)::([a-z0-9_]+)$/i', $modification['replace'], $match))
						{
							if (!class_exists($match[1]) || !is_callable([$match[1], $match[2]]))
							{
								$status[$id] = 'error_invalid_callback';
							}
							else
							{
								try
								{
									$template = preg_replace_callback(
										$modification['find'],
										[$match[1], $match[2]],
										$template
									);
								}
								catch (\Exception $e)
								{
									$status[$id] = 'error_callback_failed';
									\XF::logException($e, false, 'Template modification callback error: ');
								}
							}
						}
						else
						{
							$status[$id] = 'error_invalid_callback';
						}
					}
				}
				else
				{
					$template = preg_replace($modification['find'], $modification['replace'], $template);
				}
				break;

				default:
					$status[$id] = 'error_unknown_action';
			}
		}

		return $template;
	}

	/**
	 * @param \XF\Mvc\Entity\ArrayCollection|\XF\Entity\TemplateModification[] $modifications
	 *
	 * @return mixed
	 */
	public function addLogsToModifications($modifications)
	{
		$ids = [];
		foreach ($modifications AS $modification)
		{
			$ids[] = $modification->modification_id;
		}

		if ($ids)
		{
			$logsGrouped = $this->finder('XF:TemplateModificationLog')
				->where('modification_id', $ids)
				->fetch()
				->groupBy('modification_id');

			foreach ($modifications AS $modification)
			{
				$id = $modification->modification_id;

				$logs = isset($logsGrouped[$id])
					? $this->em->getBasicCollection($logsGrouped[$id])
					: $this->em->getEmptyCollection();

				$modification->hydrateRelation('Logs', $logs);
			}
		}

		return $modifications;
	}
}