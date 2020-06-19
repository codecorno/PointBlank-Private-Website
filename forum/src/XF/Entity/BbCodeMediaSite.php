<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Util\Arr;

/**
 * COLUMNS
 * @property string media_site_id
 * @property string site_title
 * @property string site_url
 * @property string match_urls
 * @property bool match_is_regex
 * @property string match_callback_class
 * @property string match_callback_method
 * @property string embed_html_callback_class
 * @property string embed_html_callback_method
 * @property bool oembed_enabled
 * @property string oembed_api_endpoint
 * @property string oembed_url_scheme
 * @property bool oembed_retain_scripts
 * @property bool supported
 * @property bool active
 * @property string addon_id
 *
 * GETTERS
 * @property array regexes
 * @property string embed_html
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Template MasterTemplate
 */
class BbCodeMediaSite extends Entity
{
	public function canEdit()
	{
		if (!$this->addon_id || $this->isInsert())
		{
			return true;
		}
		else
		{
			return \XF::$developmentMode;
		}
	}

	public function getMediaIdFromUrl($url)
	{
		foreach ($this->regexes AS $regex)
		{
			if (preg_match($regex, $url, $matches))
			{
				$callback = [$this->match_callback_class, $this->match_callback_method];
				if ($this->match_callback_class && $this->match_callback_method	&& is_callable($callback))
				{
					$mediaId = call_user_func_array($callback, [$url, $matches['id'], $this, $this->media_site_id]);
					if ($mediaId === false)
					{
						return false;
					}
				}
				else
				{
					$mediaId = null;
				}

				if (!$mediaId)
				{
					$mediaId = urldecode($matches['id']);
				}

				return $mediaId;
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function getRegexes()
	{
		$urls = Arr::stringToArray($this->match_urls, '/(\r?\n)+/');
		$urlsAreRegex = $this->match_is_regex;
		$regexes = [];
		foreach ($urls AS $url)
		{
			if (!$urlsAreRegex)
			{
				$url = preg_quote($url, '#');
				$url = str_replace('\\*', '.*', $url);
				$url = str_replace('\{\$id\}', '(?P<id>[^"\'?&;/<>\#\[\]]+)', $url);
				$url = str_replace('\{\$id\:digits\}', '(?P<id>[0-9]+)', $url);
				$url = str_replace('\{\$id\:alphanum\}', '(?P<id>[a-z0-9]+)', $url);
				$url = '#' . $url . '#i';
			}
			else if (preg_match('/\W[\s\w]*e[\s\w]*$/', $url))
			{
				// no e modifier allowed
				continue;
			}

			$regexes[] = $url;
		}

		return $regexes;
	}

	public function getTemplateName()
	{
		return '_media_site_embed_' . $this->media_site_id;
	}

	/**
	 * @return null|Template
	 */
	public function getMasterTemplate()
	{
		$template = $this->MasterTemplate;
		if (!$template)
		{
			$template = $this->_em->create('XF:Template');
			$template->title = $this->_getDeferredValue(function() { return $this->getTemplateName(); });
			$template->type = 'public';
			$template->style_id = 0;
			$template->addon_id = '';
		}

		return $template;
	}

	/**
	 * @return string
	 */
	public function getEmbedHtml()
	{
		$template = $this->MasterTemplate;
		return $template ? $template->template : '';
	}


	protected function verifyMediaSiteId($id)
	{
		if ($this->exists() && $id != $this->getExistingValue('media_site_id'))
		{
			$this->error('It is not possible to alter the media site ID once it has been set');
			return false;
		}

		return true;
	}

	protected function verifyMatchUrls(&$urls)
	{
		$urlOptions = Arr::stringToArray($urls, '/(\r?\n)+/');
		foreach ($urlOptions AS $key => &$url)
		{
			if ($url === '')
			{
				unset($urlOptions[$key]);
				continue;
			}

			$url = preg_replace('/\*{2,}/', '*', $url);

			if ($url[0] == '*')
			{
				$url = substr($url, 1);
			}

			if (substr($url, -1) == '*')
			{
				$url = substr($url, 0, -1);
			}
		}

		$urls = implode("\n", $urlOptions);
		return true;
	}

	protected function validateCallback($class, $method)
	{
		if ($class && !\XF\Util\Php::validateCallbackPhrased($class, $method, $errorPhrase))
		{
			$this->error($errorPhrase, 'callback_method');
			return false;
		}

		return true;
	}

	protected function validateOembed()
	{
		if ($this->oembed_enabled)
		{
			if (!$this->oembed_api_endpoint || !$this->oembed_url_scheme)
			{
				$this->error(\XF::phrase('oembed_options_error'), 'oembed_enabled');
				return false;
			}
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->isChanged('match_callback_class') || $this->isChanged('match_callback_method'))
		{
			$this->validateCallback($this->match_callback_class, $this->match_callback_method);
		}

		if ($this->isChanged(['embed_html_callback_class', 'embed_html_callback_method']))
		{
			$this->validateCallback($this->embed_html_callback_class, $this->embed_html_callback_method);
		}

		if ($this->isChanged('oembed_enabled') || $this->isChanged('oembed_api_endpoint') || $this->isChanged('oembed_url_scheme'))
		{
			$this->validateOembed();
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			if ($this->isChanged('media_site_id'))
			{
				// BB code media site templates not associated with add-on so no need to adjust dev output value.
				$template = $this->getExistingRelation('MasterTemplate');
				if ($template)
				{
					$template->title = $this->getTemplateName();
					$template->save();
				}
			}
		}

		$this->rebuildBbCodeMediaSiteCache();
	}

	protected function _postDelete()
	{
		// BB code media site templates not associated with add-on so no need to adjust dev output value.
		$template = $this->MasterTemplate;
		if ($template)
		{
			$template->delete();
		}

		$this->rebuildBbCodeMediaSiteCache();
	}

	protected function rebuildBbCodeMediaSiteCache()
	{
		$repo = $this->getBbCodeMediaSiteRepo();

		\XF::runOnce('bbCodeMediaSiteCache', function() use ($repo)
		{
			$repo->rebuildBbCodeMediaSiteCache();
		});
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_bb_code_media_site';
		$structure->shortName = 'XF:BbCodeMediaSite';
		$structure->primaryKey = 'media_site_id';
		$structure->columns = [
			'media_site_id' => ['type' => self::STR, 'maxLength' => 25,
				'required' => 'please_enter_valid_media_site_id',
				'unique' => 'media_site_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'site_title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title'
			],
			'site_url' => ['type' => self::STR, 'maxLength' => 100, 'default' => '',
				'match' => 'url_empty'
			],
			'match_urls' => ['type' => self::STR, 'default' => ''],
			'match_is_regex' => ['type' => self::BOOL, 'default' => false],
			'match_callback_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'match_callback_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'embed_html_callback_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'embed_html_callback_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'oembed_enabled' => ['type' => self::BOOL, 'default' => false],
			'oembed_api_endpoint' => ['type' => self::STR, 'maxLength' => 250, 'default' => ''],
			'oembed_url_scheme' => ['type' => self::STR, 'maxLength' => 250, 'default' => ''],
			'oembed_retain_scripts' => ['type' => self::BOOL, 'default' => true],
			'supported' => ['type' => self::BOOL, 'default' => true],
			'active' => ['type' => self::BOOL, 'default' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'regexes' => false,
			'embed_html' => false
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'MasterTemplate' => [
				'entity' => 'XF:Template',
				'type' => self::TO_ONE,
				'conditions' => [
					['style_id', '=', 0],
					['type', '=', 'public'],
					['title', '=', '_media_site_embed_', '$media_site_id']
				]
			]
		];
		$structure->options = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\BbCodeMediaSite
	 */
	protected function getBbCodeMediaSiteRepo()
	{
		return $this->repository('XF:BbCodeMediaSite');
	}
}