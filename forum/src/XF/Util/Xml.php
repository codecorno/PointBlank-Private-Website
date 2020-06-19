<?php

namespace XF\Util;

class Xml
{
	/**
	 * @param string $file
	 * @param bool $simple
	 *
	 * @return bool|\DOMDocument|\SimpleXMLElement
	 */
	public static function openFile($file, $simple = true)
	{
		if (!file_exists($file))
		{
			throw new \InvalidArgumentException("$file does not exist");
		}

		return self::open(file_get_contents($file), $simple);
	}

	/**
	 * @param string $xml
	 * @param bool $simple
	 *
	 * @return \DOMDocument|\SimpleXMLElement|bool
	 */
	public static function open($xml, $simple = true)
	{
		$dom = new \DOMDocument();

		$entityLoader = libxml_disable_entity_loader(true);
		$internalErrors = libxml_use_internal_errors(true);

		//if (!$dom->loadXML($xml, LIBXML_NOCDATA))
		if (!$dom->loadXML($xml))
		{
			libxml_disable_entity_loader($entityLoader);
			libxml_use_internal_errors($internalErrors);

			throw new \InvalidArgumentException("Invalid XML in file.");
		}

		foreach ($dom->childNodes AS $child)
		{
			if ($child->nodeType == \XML_DOCUMENT_TYPE_NODE)
			{
				throw new \InvalidArgumentException("XML with a doctype cannot be trusted");
			}
		}

		libxml_disable_entity_loader($entityLoader);
		libxml_use_internal_errors($internalErrors);

		if ($simple)
		{
			return simplexml_import_dom($dom);
		}

		return $dom;
	}

	/**
	 * Creates a DOM element. This automatically escapes the value (unlike createElement).
	 *
	 * @param \DOMDocument $document
	 * @param string $tagName
	 * @param string|\DOMNode|false $value If not false, the value for child nodes
	 *
	 * @return \DOMElement
	 */
	public static function createDomElement(\DOMDocument $document, $tagName, $value = false)
	{
		$e = $document->createElement($tagName);
		if (is_scalar($value))
		{
			$e->appendChild($document->createTextNode($value));
		}
		else if ($value instanceof \DOMNode)
		{
			$e->appendChild($value);
		}
		return $e;
	}

	public static function createDomElements(\DOMElement $rootNode, array $pairs)
	{
		$document = $rootNode->ownerDocument;

		foreach ($pairs AS $key => $value)
		{
			$rootNode->appendChild(self::createDomElement($document, $key, $value));
		}

		return $rootNode;
	}

	public static function createDomCdataSection(\DOMDocument $document, $text)
	{
		$text = str_replace(']]>', ']-]->', strval($text));
		return $document->createCDATASection($text);
	}

	public static function processSimpleXmlCdata($cdata)
	{
		return str_replace(']-]->', ']]>', strval($cdata));
	}
}