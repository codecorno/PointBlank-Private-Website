<?php

namespace XF\Entity;

use XF\Mvc\Entity\Structure;

trait ImageSpriteTrait
{
	public function getSpriteCss()
	{
		if (!$this->sprite_mode || empty($this->sprite_params))
		{
			return '';
		}

		$params = $this->sprite_params;
		$w = isset($params['w']) ? intval($params['w']) : 0;
		$h = isset($params['h']) ? intval($params['h']) : 0;
		$x = isset($params['x']) ? intval($params['x']) : 0;
		$y = isset($params['y']) ? intval($params['y']) : 0;

		$css = sprintf(
			'width: %1$dpx; height: %2$dpx; background: url(\'%3$s\') no-repeat %4$dpx %5$dpx;',
			$w, $h, preg_replace('/["\'\r\n;}]/', '', $this->image_url), $x, $y
		);
		if (!empty($params['bs']))
		{
			$css .= ' background-size: ' . preg_replace('/["\'\r\n;}]/', '', $params['bs']);
		}

		return $css;
	}

	protected function verifySpriteParams(&$spriteParams)
	{
		array_walk($spriteParams, function($value, $key)
		{
			if ($key != 'bs')
			{
				$value = intval($value);
			}
			return $value;
		});
		return true;
	}

	protected function _preSave()
	{
		parent::_preSave();

		if ($this->sprite_mode)
		{
			$this->image_url_2x = '';
		}
	}

	protected static function addImageSpriteStructureElements(Structure $structure)
	{
		$structure->columns['image_url'] = ['type' => self::STR, 'maxLength' => 200,
			'required' => 'please_enter_valid_url'
		];
		$structure->columns['image_url_2x'] = ['type' => self::STR, 'maxLength' => 200, 'default' => ''];
		$structure->columns['sprite_mode'] = ['type' => self::BOOL, 'default' => false];
		$structure->columns['sprite_params'] = ['type' => self::JSON_ARRAY, 'default' => []];
	}
}