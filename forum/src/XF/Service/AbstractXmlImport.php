<?php

namespace XF\Service;

abstract class AbstractXmlImport extends AbstractService
{
	abstract public function import(\SimpleXMLElement $xml);
}