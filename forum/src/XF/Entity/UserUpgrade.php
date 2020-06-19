<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Payment\AbstractProvider;

/**
 * COLUMNS
 * @property int|null user_upgrade_id
 * @property string title
 * @property string description
 * @property int display_order
 * @property array extra_group_ids
 * @property bool recurring
 * @property float cost_amount
 * @property string cost_currency
 * @property int length_amount
 * @property string length_unit
 * @property array disabled_upgrade_ids
 * @property bool can_purchase
 * @property array payment_profile_ids
 *
 * GETTERS
 * @property \XF\Phrase|string cost_phrase
 * @property string purchasable_type_id
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserUpgradeActive[] Active
 */
class UserUpgrade extends Entity
{
	public function canPurchase()
	{
		$visitor = \XF::visitor();
		return ($this->can_purchase && !isset($this->Active[$visitor->user_id]));
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getCostPhrase()
	{
		$cost = $this->app()->data('XF:Currency')->languageFormat($this->cost_amount, $this->cost_currency);
		$phrase = $cost;

		if ($this->length_unit)
		{
			if ($this->length_amount > 1)
			{
				if ($this->recurring)
				{
					$phrase = \XF::phrase("x_per_y_{$this->length_unit}s", [
						'cost' => $cost,
						'length' => $this->length_amount
					]);
				}
				else
				{
					$phrase = \XF::phrase("x_for_y_{$this->length_unit}s", [
						'cost' => $cost,
						'length' => $this->length_amount
					]);
				}
			}
			else
			{
				if ($this->recurring)
				{
					$phrase = \XF::phrase("x_per_{$this->length_unit}", [
						'cost' => $cost
					]);
				}
				else
				{
					$phrase = \XF::phrase("x_for_one_{$this->length_unit}", [
						'cost' => $cost
					]);
				}
			}
		}

		return $phrase;
	}

	/**
	 * @return string
	 */
	public function getPurchasableTypeId()
	{
		return 'user_upgrade';
	}

	protected function _preSave()
	{
		if ($this->isChanged(['recurring', 'length_amount', 'length_unit', 'cost_currency']))
		{
			/** @var \XF\Entity\PaymentProfile[] $profiles */
			$profiles = $this->_em->findByIds('XF:PaymentProfile', $this->payment_profile_ids);

			if ($this->isChanged(['recurring', 'length_amount', 'length_unit']) && $this->recurring)
			{
				$invalidRecurring = [];
				$invalidLength = [];

				foreach ($profiles AS $profile)
				{
					$supportsRecurring = $profile->supportsRecurring($this->length_unit, $this->length_amount, $result);
					if (!$supportsRecurring)
					{
						if ($result === AbstractProvider::ERR_NO_RECURRING)
						{
							$invalidRecurring[] = $profile->Provider->getTitle();
						}
						else if ($result === AbstractProvider::ERR_INVALID_RECURRENCE)
						{
							$invalidLength[] = $profile->Provider->getTitle();
						}
					}
				}

				if ($invalidRecurring)
				{
					$invalidRecurring = implode(', ', array_unique($invalidRecurring));
					$this->error(\XF::phrase('following_payment_providers_do_not_support_recurring_payments', ['invalidRecurring' => $invalidRecurring]), 'recurring');
				}

				if ($invalidLength)
				{
					$invalidLength = implode(', ', array_unique($invalidLength));
					$this->error(\XF::phrase('following_payment_providers_support_recurring_payments_but_invalid_length', ['invalidLength' => $invalidLength]), 'recurring');
				}
			}

			if ($this->isChanged('cost_currency'))
			{
				$invalidCurrency = [];

				foreach ($profiles AS $profile)
				{
					if (!$profile->verifyCurrency($this->cost_currency))
					{
						$invalidCurrency[] = $profile->Provider->getTitle();
					}
				}

				if ($invalidCurrency)
				{
					$invalidCurrency = implode(', ', array_unique($invalidCurrency));
					$this->error(\XF::phrase('following_payment_providers_do_not_support_x_as_valid_currency', ['currency' => $this->cost_currency, 'invalidCurrency' => $invalidCurrency]), 'currency_code');
				}
			}
		}

		if (!$this->length_amount || !$this->length_unit)
		{
			$this->length_amount = 0;
			$this->length_unit = '';
		}
	}

	protected function _postSave()
	{
		$this->rebuildUpgradeCount();
	}

	protected function _postDelete()
	{
		$this->getUserGroupChangeService()->removeUserGroupChangeLogByKey("userUpgrade-$this->user_upgrade_id");
		$this->rebuildUpgradeCount();
	}

	protected function rebuildUpgradeCount()
	{
		\XF::runOnce('upgradeCountRebuild', function()
		{
			$this->getUserUpgradeRepo()->rebuildUpgradeCount();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_upgrade';
		$structure->shortName = 'XF:UserUpgrade';
		$structure->primaryKey = 'user_upgrade_id';
		$structure->columns = [
			'user_upgrade_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title'
			],
			'description' => ['type' => self::STR, 'default' => ''],
			'display_order' => ['type' => self::UINT, 'default' => 0],
			'extra_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
			'recurring' => ['type' => self::BOOL, 'default' => false],
			'cost_amount' => ['type' => self::FLOAT, 'required' => true, 'min' => 0.01],
			'cost_currency' => ['type' => self::STR, 'required' => true],
			'length_amount' => ['type' => self::UINT, 'max' => 255, 'required' => true],
			'length_unit' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['day', 'month', 'year', '']
			],
			'disabled_upgrade_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
			'can_purchase' => ['type' => self::BOOL, 'default' => true],
			'payment_profile_ids' => ['type' => self::LIST_COMMA,
				'required' => 'please_select_at_least_one_payment_profile',
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			]
		];
		$structure->getters = [
			'cost_phrase' => true,
			'purchasable_type_id' => true
		];
		$structure->relations = [
			'Active' => [
				'entity' => 'XF:UserUpgradeActive',
				'type' => self::TO_MANY,
				'conditions' => 'user_upgrade_id',
				'key' => 'user_id'
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\UserUpgrade
	 */
	protected function getUserUpgradeRepo()
	{
		return $this->repository('XF:UserUpgrade');
	}

	/**
	 * @return \XF\Service\User\UserGroupChange
	 */
	protected function getUserGroupChangeService()
	{
		return $this->app()->service('XF:User\UserGroupChange');
	}
}