<?php

namespace XF\Import\DataHelper;

class Smilie extends AbstractHelper
{
	protected $smilies = [];
	protected $smilieCategories = [];

	protected $importUrl = null;

	/**
	 * @var \DOMDocument|null
	 */
	protected $xml = null;

	protected $defaultSmilie = [
		'smilie_id' => 0,
		'title' => '',
		'smilie_text' => '',
		'image_url' => '',
		'image_url_2x' => '',
		'sprite_mode' => 0,
		'sprite_params' => '',
		'smilie_category_id' => 1,
		'display_order' => 1,
		'display_in_editor' => 1
	];

	protected $defaultSmilieCategory = [
		'smilie_category_id' => 1,
		'title' => '',
		'display_order' => 1
	];

	/**
	 * Sets the URL of the software installation being imported for translation of relative URLs
	 *
	 * @param $importUrl http://example.com/my/forum
	 */
	public function setImportUrl($importUrl)
	{
		$this->importUrl = $importUrl;
	}

	public function addSmilie(array $smilie)
	{
		foreach (['title', 'smilie_text', 'image_url'] AS $requiredField)
		{
			if (empty($smilie[$requiredField]))
			{
				return false;
			}
		}

		$this->smilies[] = array_merge($this->defaultSmilie, $smilie);

		return true;
	}

	public function addSmilieCategory(array $smilieCategory)
	{
		if (empty($smilieCategory['title']))
		{
			return false;
		}

		$this->smilieCategories[] = array_merge($this->defaultSmilieCategory, $smilieCategory);

		return true;
	}

	/**
	 * @return \DOMDocument|null
	 */
	public function getXml()
	{
		if (empty($this->smilies))
		{
			throw new \LogicException("Must have use addSmilie() to add at least one smilie before fetching XML.");
		}

		if ($this->importUrl)
		{
			$expectedParts = [
				'path' => null,
				'scheme' => null,
				'host' => null
			];

			$thisUrl = array_replace($expectedParts, parse_url(\XF::app()->options()->boardUrl));
			$importUrl = array_replace($expectedParts, parse_url($this->importUrl));

			if (substr($importUrl['path'], -1, 1) == '/')
			{
				$importUrl['path'] = substr($importUrl['path'], 0, -1);
			}

			// process URLs
			foreach ($this->smilies AS &$smilie)
			{
				foreach (['image_url', 'image_url_2x'] AS $urlField)
				{
					if (!empty($smilie[$urlField]))
					{
						$smilie[$urlField] = $this->processUrl($smilie[$urlField],
							$thisUrl['scheme'] . '://' . $thisUrl['host'],
							$importUrl['scheme'] . '://' . $importUrl['host'],
							$importUrl['path']
						);
					}
				}
			}
		}

		/** @var \XF\Service\Smilie\Export $smilieService */
		$smilieService = \XF::app()->service('XF:Smilie\Export');

		$this->xml = $smilieService->exportFromArray($this->smilies, $this->smilieCategories);

		return $this->xml;
	}

	public function saveXml($fileName)
	{
		if ($this->xml === null)
		{
			$this->getXml();
		}

		return \XF\Util\File::writeToAbstractedPath(
			$this->getXmlFileName($fileName),
			$this->xml->saveXML(),
			[], true);
	}

	/**
	 * Force a file name that fits 'smilies.{anything}.xml'
	 *
	 * @param $fileName
	 *
	 * @return mixed
	 */
	public function getXmlFileName($fileName)
	{
		return \XF::app()->repository('XF:Smilie')->getAbstractedImportedXmlFilePath($fileName);
	}

	protected function processUrl($url, $thisHost = null, $importHost = null, $importPath = null)
	{
		$u = parse_url($url);

		if (isset($u['host']))
		{
			if ($u['scheme'] . '://' . $u['host'] != $thisHost)
			{
				return $url;
			}

			if (!isset($u['path']))
			{
				return $url;
			}
		}

		$host = ($thisHost != $importHost) ? $importHost : '';

		if ($u['path'][0] == '/')
		{
			// absolute path
			return $host . $u['path'] . (isset($u['query']) ? '?' . $u['query'] : '');
		}
		else
		{
			// relative path, calculate path from import URL to xf URL
			$pathBits = array_reverse(explode('/', $importPath . '/' . $u['path']));

			foreach ($pathBits AS $i => $bit)
			{
				if ($bit == '..')
				{
					unset($pathBits[$i]);
					unset($pathBits[$i+1]);
				}
			}

			$newPath = implode('/', array_reverse($pathBits));

			return $host . $newPath;
		}
	}
}