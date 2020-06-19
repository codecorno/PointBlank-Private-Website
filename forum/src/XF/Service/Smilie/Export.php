<?php

namespace XF\Service\Smilie;

use XF\Mvc\Entity\Finder;
use XF\Service\AbstractXmlExport;
use XF\Util\Arr;
use XF\Util\Xml;

class Export extends AbstractXmlExport
{
	public function getRootName()
	{
		return 'smilies_export';
	}

	public function export(Finder $smilies)
	{
		$smilies = $smilies->fetch();

		if ($smilies->count())
		{
			$smilieCategories = $this->finder('XF:SmilieCategory')
				->with('MasterTitle')
				->order(['display_order'])
				->where('smilie_category_id', $smilies->pluckNamed('smilie_category_id'));

			return $this->exportFromArray($smilies->toArray(), $smilieCategories->fetch()->toArray());
		}
		else
		{
			$this->throwNoSmiliesError();
		}
	}

	/**
	 * This method allows the normal smilie export to be called on an array of smilies
	 * that don't necessarily come from the database, such as from an import.
	 *
	 * @param array $smilies (including 'title' from phrase)
	 * @param array $smilieCategories (including 'title' from phrase)
	 *
	 * @return \DOMDocument
	 */
	public function exportFromArray(array $smilies, array $smilieCategories)
	{
		$document = $this->createXml();
		$rootNode = $document->createElement($this->getRootName());
		$document->appendChild($rootNode);

		if (count($smilies))
		{
			$smiliesNode = $document->createElement('smilies');
			foreach ($smilies AS $smilie)
			{
				$smilieNode = $document->createElement('smilie');

				if ($smilie['smilie_category_id'])
				{
					$smilieNode->setAttribute('smilie_category_id', $smilie['smilie_category_id']);
				}

				$smilieNode->setAttribute('title', $smilie['title']);

				$smilieNode->appendChild(Xml::createDomElement($document, 'image_url', $smilie['image_url']));
				$smilieNode->appendChild(Xml::createDomElement($document, 'image_url_2x', $smilie['image_url_2x']));

				if ($smilie['sprite_mode'])
				{
					$spriteParamsNode = $document->createElement('sprite_params');

					foreach ($smilie['sprite_params'] AS $param => $value)
					{
						$spriteParamsNode->setAttribute($param, $value);
					}

					$smilieNode->appendChild($spriteParamsNode);
				}

				foreach (Arr::stringToArray($smilie['smilie_text'], '/\r?\n/') AS $smilieText)
				{
					$smilieNode->appendChild(Xml::createDomElement($document, 'smilie_text', $smilieText));
				}

				$smilieNode->setAttribute('display_order', $smilie['display_order']);
				$smilieNode->setAttribute('display_in_editor', $smilie['display_in_editor']);

				$smiliesNode->appendChild($smilieNode);
			}

			$categoriesNode = $document->createElement('smilie_categories');

			foreach ($smilieCategories AS $smilieCategory)
			{
				$categoryNode = $document->createElement('smilie_category');
				$categoryNode->setAttribute('id', $smilieCategory['smilie_category_id']);
				$categoryNode->setAttribute('title', $smilieCategory['title']);
				$categoryNode->setAttribute('display_order', $smilieCategory['display_order']);

				$categoriesNode->appendChild($categoryNode);
			}

			$rootNode->appendChild($categoriesNode);
			$rootNode->appendChild($smiliesNode);

			return $document;
		}
		else
		{
			$this->throwNoSmiliesError();
		}
	}

	protected function throwNoSmiliesError()
	{
		throw new \XF\PrintableException(\XF::phrase('please_select_at_least_one_smilie_to_export')->render());
	}

	/**
	 * @return \XF\Repository\SmilieCategory
	 */
	protected function getSmilieCategoryRepo()
	{
		return $this->repository('XF:SmilieCategory');
	}
}