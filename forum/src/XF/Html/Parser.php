<?php

namespace XF\Html;

/**
 * Parses HTML into a tree of tags.
 */
class Parser
{
	/**
	 * String of HTML to be parsed.
	 *
	 * @var string
	 */
	protected $_html = '';

	/**
	 * Length of the HTML string.
	 *
	 * @var integer
	 */
	protected $_length = 0;

	/**
	 * Current position in the HTML string.
	 *
	 * @var integer
	 */
	protected $_position = 0;

	/**
	 * @var Tag
	 */
	protected $_rootTag = null;

	/**
	 * @var Tag
	 */
	protected $_currentTag = null;

	/**
	 * Constructor. Sets up HTML.
	 *
	 * @param string $html HTML to parse
	 */
	public function __construct($html)
	{
		$this->_html = $html;
		$this->_length = strlen($html);
	}

	/**
	 * Parses the HTML.
	 *
	 * @return Tag The root tag of the tree. This tag isn't really in the tree, so traversal should start with its children.
	 */
	public function parse()
	{
		$this->_currentTag = $this->_rootTag = new Tag('');

		do
		{
			$text = $this->readUntilString('<');
			if ($text === false)
			{
				break;
			}

			$this->appendText($text);

			$currentPosition = $this->_position;
			$this->step();

			if (!$this->stateTag())
			{
				$this->appendText(substr($this->_html, $currentPosition, $this->_position - $currentPosition));
			}
		}
		while ($this->_position < $this->_length);

		$this->appendText($this->readUntilEnd());

		return $this->_rootTag;
	}

	/**
	 * Function is called when entering the tag state. The opening < has been found and passed.
	 *
	 * @return boolean True if tag was parsed; if false, the tag is treated as plain text
	 */
	public function stateTag()
	{
		switch ($this->peak())
		{
			case '/': // closing tag
				$this->step();
				$isClose = true;
				break;

			case '!': // comment, ignore it
				$this->step();
				$commentEnd = $this->readUntilString('-->');
				if ($commentEnd !== false)
				{
					$this->step();
					$this->step();
					$this->step();
				}
				return true; // tag was handled

			default:
				$isClose = false;
		}

		$tagName = $this->readUntilCharacters(" \t\r\n/>");
		if ($tagName === '' || $tagName === false)
		{
			return false;
		}

		$isSelfClose = false;
		$attributes = [];

		do
		{
			switch ($this->step())
			{
				case '/':
					if ($this->peak() == '>')
					{
						$this->step();
						$isSelfClose = true;
					}
					break 2;

				case '>':
					break 2;

				// otherwise it's whitespace
			}

			$this->skipWhiteSpace();

			if (!$this->stateAttribute($attributes))
			{
				return false;
			}
		}
		while ($this->_position < $this->_length);

		$tagName = strtolower($tagName);

		if ($isClose)
		{
			$this->pushTagClose($tagName);
		}
		else
		{
			$childTag = $this->pushTagOpen($tagName, $attributes);
			if ($isSelfClose && !$childTag->isVoid())
			{
				$this->pushTagClose($tagName);
			}
		}

		return true;
	}

	/**
	 * Function is called when entering the attribute state.
	 *
	 * @param array $attributes Existing attribute list. New attributes are pushed onto this.
	 *
	 * @return boolean If false is returned, the containing tag is considered invalid and treated as text
	 */
	public function stateAttribute(array &$attributes)
	{
		$attributeName = $this->readUntilCharacters(" \t\r\n/>=");
		if ($attributeName === false)
		{
			return false;
		}

		if ($this->peak() == '=')
		{
			$this->step();

			$this->skipWhiteSpace();

			$delimiter = $this->peak();

			switch ($delimiter)
			{
				case '"':
				case "'":
					$this->step();
					$attributeValue = $this->readUntilString($delimiter);
					$this->step();
					break;

				default:
					$attributeValue = $this->readUntilCharacters(" \t\r\n>");
					if (substr($attributeValue, -1) == '/' && $this->peak() == '>')
					{
						// match a "/" in an unquoted attr, unless it's the last value
						$attributeValue = substr($attributeValue, 0, -1);
						$this->stepBack();
					}
			}

			if ($attributeValue === false)
			{
				return false;
			}
		}
		else
		{
			$attributeValue = $attributeName;
		}

		if ($attributeName !== '')
		{
			$attributes[strtolower($attributeName)] = $attributeValue;
		}

		return true;
	}

	/**
	 * Parses CSS into its component rules.
	 *
	 * @param string $css
	 *
	 * @return array Key-value rule pairs
	 */
	public function parseCss($css)
	{
		// TODO: this isn't completely correct and should be revisited

		$rules = [];
		preg_match_all('/(?<=^|\W)([a-z0-9-]+)\s*:\s*?(.*)\s*?(;|$)/siU', $css, $matches, PREG_SET_ORDER);
		foreach ($matches AS $match)
		{
			$match[2] = trim(preg_replace('/!\s*important$/i', '', trim($match[2])));

			if (strlen($match[2]) == 0)
			{
				continue; // parse empty rules, but ignore them
			}

			$rules[strtolower($match[1])] = $match[2];
		}

		return $rules;
	}

	/**
	 * Read until (but not including) the given string is found.
	 *
	 * @param string $string
	 *
	 * @return false|string False if the string is not found; otherwise, text to string
	 */
	public function readUntilString($string)
	{
		$foundPosition = strpos($this->_html, $string, $this->_position);
		if ($foundPosition === false)
		{
			return false;
		}
		else if ($foundPosition == $this->_position)
		{
			return '';
		}
		else
		{
			$string = substr($this->_html, $this->_position, $foundPosition - $this->_position);
			$this->_position = $foundPosition;

			return $string;
		}
	}

	/**
	 * Read until one of the characters is found.
	 *
	 * @param string $characters List of characters
	 *
	 * @return false|string False if the none of the characters are found; otherwise, text to first character found
	 */
	public function readUntilCharacters($characters)
	{
		$length = strcspn($this->_html, $characters, $this->_position);
		if ($length == 0)
		{
			return '';
		}
		else if ($length + $this->_position == $this->_length)
		{
			return false;
		}
		else
		{
			$string = substr($this->_html, $this->_position, $length);
			$this->_position += $length;

			return $string;
		}
	}

	/**
	 * Skip over any amount of whitespace.
	 */
	public function skipWhiteSpace()
	{
		$length = strspn($this->_html, " \t\r\n", $this->_position);
		$this->_position += $length;
	}

	/**
	 * Read until the end of the string.
	 *
	 * @return false|string False if at the end; otherwise, text until end
	 */
	public function readUntilEnd()
	{
		if ($this->_position >= $this->_length)
		{
			return false;
		}

		$string = substr($this->_html, $this->_position);
		$this->_position = $this->_length;

		return $string;
	}

	/**
	 * Read the current character and advance one position.
	 *
	 * @return string
	 */
	public function step()
	{
		if (!isset($this->_html[$this->_position]))
		{
			return false;
		}

		$value = $this->_html[$this->_position];
		$this->_position++;

		return $value;
	}

	public function stepBack()
	{
		if ($this->_position > 0)
		{
			$this->_position--;
		}
	}

	/**
	 * Peak at the current character without changing the current position.
	 *
	 * @return false|string False if past the end; character otherwise
	 */
	public function peak()
	{
		if (isset($this->_html[$this->_position]))
		{
			return $this->_html[$this->_position];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Appends text to the current tag. Text is cleaned of HTML entities first.
	 *
	 * @param string|false $text Text to append; false is ignored
	 */
	public function appendText($text)
	{
		if ($text !== '' && $text !== false)
		{
			$this->_currentTag->appendText($this->decodeEntities($text));
		}
	}

	/**
	 * Push a tag close to the current tag and have it return the new
	 * value for the current tag.
	 *
	 * @param string $tagName
	 */
	public function pushTagClose($tagName)
	{
		$this->_currentTag = $this->_currentTag->closeTag($tagName);
		if (!$this->_currentTag)
		{
			$this->_currentTag = $this->_rootTag;
		}
	}

	/**
	 * Push a tag open.
	 *
	 * @param string $tagName
	 * @param array $attributes Key-value attributes; cleaned of HTML entities within function
	 *
	 * @return Tag Child tag that was added
	 */
	public function pushTagOpen($tagName, array $attributes)
	{
		$cleanAttributes = [];
		foreach ($attributes AS $attributeName => $attribute)
		{
			$cleanAttributes[$this->decodeEntities($attributeName)] = $this->decodeEntities($attribute);
		}

		if (!empty($cleanAttributes['style']))
		{
			$cleanAttributes['style'] = $this->parseCss($cleanAttributes['style']);
		}

		$childTag = $this->_currentTag->addChildTag($tagName, $cleanAttributes);
		if (!$childTag->isVoid())
		{
			$this->_currentTag = $childTag;
		}

		return $childTag;
	}

	/**
	 * Decodes HTML entities in a string. Note that non-breaking spaces are treated as normal spaces.
	 *
	 * @param string $string
	 *
	 * @return string Decoded string
	 */
	public function decodeEntities($string)
	{
		return html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}

	/**
	 * Debug helper to print the tag tree in HTML format.
	 *
	 * @param Tag $tag Current/root tag
	 * @param string $prefix Prefix for each line
	 */
	public function printTags(Tag $tag, $prefix = '')
	{
		echo $prefix . 'Tag: ' . $tag->tagName() . ' ' . json_encode($tag->attributes()) . "<br />\n";
		$childPrefix = "$prefix&nbsp; &nbsp; ";

		foreach ($tag->children() AS $child)
		{
			if ($child instanceof Tag)
			{
				$this->printTags($child, $childPrefix);
			}
			else
			{
				echo $childPrefix . "'" . htmlspecialchars($child) . "'<br />\n";
			}
		}
	}
}
