<?php

namespace XF\Help;

use XF\Mvc\Controller;
use XF\Mvc\Reply\View;

class Smilies
{
	public static function renderSmilies(Controller $controller, View &$response)
	{
		$smilies = $controller->finder('XF:Smilie')
			->with('Category')
			->order(['Category.display_order', 'display_order', 'title'])
			->keyedBy('smilie_id')
			->fetch();

		$smilieCategories = [];

		foreach ($smilies AS $smilieId => $smilie)
		{
			$smilieCatId = $smilie->smilie_category_id;
			$smilieCategories[$smilieCatId]['smilie_category_id'] = $smilieCatId;
			$smilieCategories[$smilieCatId]['title'] = $smilie->Category ? $smilie->Category->title : '';
			$smilieCategories[$smilieCatId]['smilies'][$smilieId] = $smilie;
		}

		$response->setParam('smilieCategories', $smilieCategories);
	}
}