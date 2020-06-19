<?php

namespace XF\BbCode\ProcessorAction;

use XF\BbCode\Processor;

class LimitTags implements FiltererInterface
{
	protected $disabledTags = [];
	protected $stripDisabled = true;
	protected $disabledSeen = [];

	protected $maxTextSize = -1;

	public function addFiltererHooks(FiltererHooks $hooks)
	{
		$hooks->addSetupHook('setup')
			->addGlobalTagHook('filterTag')
			->addTagHook('size', 'filterSizeTag');
	}

	public function setStripDisabled($value)
	{
		$this->stripDisabled = (bool)$value;

		return $this;
	}

	public function getStripDisabled()
	{
		return $this->stripDisabled;
	}

	public function hasDisabledTags()
	{
		return $this->disabledSeen ? true : false;
	}

	public function disableTag($tag)
	{
		if (is_array($tag))
		{
			foreach ($tag AS $t)
			{
				$this->disabledTags[$t] = true;
			}
		}
		else
		{
			$this->disabledTags[$tag] = true;
		}

		return $this;
	}

	public function getDisabledTags()
	{
		return $this->disabledTags;
	}

	public function isTagDisabled($tag)
	{
		return !empty($this->disabledTags[$tag]);
	}

	public function setMaxTextSize($size)
	{
		$this->maxTextSize = $size;

		return $this;
	}

	public function setup()
	{
		$this->disabledSeen = [];
	}

	public function filterTag(array $tag)
	{
		$tagName = $tag['tag'];
		if (isset($this->disabledTags[$tagName]))
		{
			$this->disabledSeen[$tagName] = true;

			if ($this->stripDisabled)
			{
				return false; // remove the tag wrapping, keep the children
			}
		}

		return null; // do nothing
	}

	public function filterSizeTag(array $tag, array $options, Processor $processor)
	{
		if ($this->maxTextSize < 0)
		{
			return null; // all allowed
		}

		if ($this->maxTextSize == 0)
		{
			return false; // strip all size tags
		}

		$size = $tag['option'];
		if (preg_match('/^[0-9]+$/', $size))
		{
			$size = intval($size);
			if ($size > $this->maxTextSize)
			{
				// push down to expected size
				$tag['original'][0] = $processor->replaceOptionInTagOpen($tag['original'][0], $this->maxTextSize);
				return $processor->renderValidTag($tag, $options);
			}
			else
			{
				return null; // allowed size
			}
		}
		else
		{
			return false; // different format which we can't easily parse, so remove it
		}
	}

	public static function factory(\XF\App $app)
	{
		return new static();
	}
}