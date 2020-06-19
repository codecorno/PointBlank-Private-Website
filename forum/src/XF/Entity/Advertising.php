<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\PrintableException;

/**
 * COLUMNS
 * @property int|null ad_id
 * @property string title
 * @property string position_id
 * @property string ad_html
 * @property array display_criteria
 * @property int display_order
 * @property bool active
 *
 * RELATIONS
 * @property \XF\Entity\AdvertisingPosition AdvertisingPosition
 */
class Advertising extends Entity
{
	protected function _postSave()
	{
		$this->writeAdsTemplate();
	}

	protected function _postDelete()
	{
		$this->writeAdsTemplate();
	}

	protected function writeAdsTemplate()
	{
		\XF::runOnce('writeAdsTemplate', function()
		{
			$this->getAdvertisingRepo()->writeAdsTemplate();
		});
	}

	protected function verifyAdHtml($adHtml)
	{
		try
		{
			$this->app()->templateCompiler()->compile($adHtml);
		} catch (PrintableException $e)
		{
			$this->error(sprintf("%s %s", \XF::phrase('error_occurred_while_compiling_advertisement_html:'), $e->getMessage()), 'ad_html');
			return false;
		}

		return true;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_advertising';
		$structure->shortName = 'XF:Advertising';
		$structure->primaryKey = 'ad_id';
		$structure->columns = [
			'ad_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 150,
				'required' => 'please_enter_valid_title'
			],
			'position_id' => ['type' => self::STR, 'maxLength' => 50, 'match' => 'alphanumeric',
				'required' => 'please_select_valid_ad_position'
			],
			'ad_html' => ['type' => self::STR, 'default' => ''],
			'display_criteria' => ['type' => self::JSON_ARRAY, 'default' => []],
			'display_order' => ['type' => self::UINT, 'default' => 0],
			'active' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'AdvertisingPosition' => [
				'entity' => 'XF:AdvertisingPosition',
				'type' => self::TO_ONE,
				'conditions' => 'position_id',
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Advertising
	 */
	protected function getAdvertisingRepo()
	{
		return $this->repository('XF:Advertising');
	}
}