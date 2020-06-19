<?php

namespace XF\Html;

/**
 * Represents an individual tag within an HTML tree.
 */
class Tag
{
	/**
	 * Name of the tag (lower case).
	 *
	 * @var string
	 */
	protected $_tagName = '';

	/**
	 * Key-value pairs of attributes for the tag
	 *
	 * @var array
	 */
	protected $_attributes = [];

	/**
	 * Parent tag object.
	 *
	 * @var Tag|null Null for root tag
	 */
	protected $_parent = null;

	/**
	 * List of child tags and text.
	 *
	 * @var array Values are Tag or Text elements
	 */
	protected $_children = [];

	/**
	 * List of tags that are considered to be block tags.
	 *
	 * @var array
	 */
	protected $_blockTags = [
		'address', 'article', 'aside', 'audio', 'blockquote',
		'canvas', 'dd', 'div', 'dl', 'dt', 'fieldset', 'figcaption',
		'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
		'header', 'hgroup', 'hr', 'li', 'nav', 'ol', 'output', 'p', 'pre',
		'section', 'table', 'tbody', 'tfoot', 'thead', 'tr', 'ul', 'video'
		// note that "" is not here
	];

	protected $_voidTags = [
		'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'wbr'
	];

	protected $_allowEmptyTags = [
		'audio', 'video'
	];

	/**
	 * Constructor.
	 *
	 * @param string $tagName
	 * @param array $attributes
	 * @param Tag $parent
	 */
	public function __construct($tagName, array $attributes = [], Tag $parent = null)
	{
		$this->_tagName = strtolower($tagName);
		$this->_attributes = $attributes;
		$this->_parent = $parent;
	}

	/**
	 * Appends text to the tag. If the last child is text, it will be added
	 * to that child; otherwise, a new child will be created.
	 *
	 * @param string $text
	 */
	public function appendText($text)
	{
		if ($this->isVoid())
		{
			throw new \LogicException('Void tag ' . htmlspecialchars($this->_tagName) . ' cannot have children');
		}

		if ($this->_children)
		{
			$keys = array_keys($this->_children);
			$lastKey = end($keys);
			if ($this->_children[$lastKey] instanceof Text)
			{
				$this->_children[$lastKey]->addText($text);
				return;
			}
		}

		$this->_children[] = new Text($text, $this);
	}

	/**
	 * Adds a new child tag.
	 *
	 * @param string $tagName
	 * @param array $attributes
	 *
	 * @return Tag New child tag
	 */
	public function addChildTag($tagName, array $attributes = [])
	{
		if ($this->isVoid())
		{
			throw new \LogicException('Void tag ' . htmlspecialchars($this->_tagName) . ' cannot have children');
		}

		if (($tagName == 'li' || $tagName == 'p') && $tagName == $this->_tagName)
		{
			// can't child to this tag, should be a sibling
			$parent = $this->parent();
			if ($parent)
			{
				return $parent->addChildTag($tagName, $attributes);
			}
		}

		$child = new Tag($tagName, $attributes, $this);
		$this->_children[] = $child;

		return $child;
	}

	/**
	 * Closes the given tag. This generally does not require modifying the tag tree,
	 * unless invalid nesting occurred.
	 *
	 * @param string $tagName
	 *
	 * @return Tag The new "parent" tag that should be used by the parser
	 */
	public function closeTag($tagName)
	{
		$tagName = strtolower($tagName);
		if ($tagName == $this->_tagName || $this->isVoid())
		{
			return $this->_parent;
		}
		else
		{
			$stack = [];
			for ($tag = $this; $tag && $tag->tagName() != $tagName; $tag = $tag->parent())
			{
				$stack[] = $tag;
			}

			if ($tag)
			{
				$newParent = $tag->closeTag($tagName);
				while ($createTag = array_pop($stack))
				{
					$newParent = $newParent->addChildTag($createTag->tagName(), $createTag->attributes());
				}

				return $newParent;
			}
			else
			{
				// tag not found, ignore it
				return $this->_parent;
			}
		}
	}

	/**
	 * Gets the tag name.
	 *
	 * @return string
	 */
	public function tagName()
	{
		return $this->_tagName;
	}

	/**
	 * Gets the attributes.
	 *
	 * @return array
	 */
	public function attributes()
	{
		return $this->_attributes;
	}

	/**
	 * Gets the named attribute.
	 *
	 * @param string $attribute
	 *
	 * @return mixed|false
	 */
	public function attribute($attribute)
	{
		return (isset($this->_attributes[$attribute]) ? $this->_attributes[$attribute] : false);
	}

	public function hasClass($findClass)
	{
		$class = $this->attribute('class');
		if ($class)
		{
			return strpos(" $class ", " $findClass ") !== false;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gets the parent tag.
	 *
	 * @return Tag|null
	 */
	public function parent()
	{
		return $this->_parent;
	}

	/**
	 * Sets the parent tag. This does not check for circular references!
	 *
	 * @param Tag $parent
	 */
	public function setParent(Tag $parent)
	{
		$this->_parent = $parent;
	}

	/**
	 * Gets the child tags and text.
	 *
	 * @return array
	 */
	public function children()
	{
		return $this->_children;
	}

	/**
	 * Copies this tag. Does not copy any children tags or this tag's parent. The
	 * parent will need to be set manually later.
	 *
	 * @return Tag
	 */
	public function copy()
	{
		return new Tag($this->_tagName, $this->_attributes);
	}

	/**
	 * Determines if the tag has renderable content within.
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		switch ($this->_tagName)
		{
			case 'img':
			case 'br':
				return false;
		}

		foreach ($this->children() AS $child)
		{
			if ($child instanceof Tag)
			{
				if (!$child->isEmpty())
				{
					return false;
				}
			}
			else if ($child instanceof Text)
			{
				if (trim($child->text()) !== '')
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Determines if this tag is a block-level tag.
	 *
	 * @return boolean
	 */
	public function isBlock()
	{
		return in_array($this->_tagName, $this->_blockTags);
	}

	/**
	 * Determines if this tag is a void tag. Void tags can't have children.
	 *
	 * @return boolean
	 */
	public function isVoid()
	{
		return in_array($this->_tagName, $this->_voidTags);
	}

	/**
	 * Determines if this tag is valid even if it's empty. Automatically includes all void tags.
	 * Should only be used for tags that render content without children (such as a video tag).
	 *
	 * @return bool
	 */
	public function isAllowedEmpty()
	{
		return $this->isVoid() || in_array($this->_tagName, $this->_allowEmptyTags);
	}
}