<?php

namespace XF;

class CssRenderException extends \Exception
{
	const LINE = 0;
	const INDEX = 1;

	public $templateName = '';

	public $contents = '';

	public $renderErrorLine;

	public function __construct($message, $templateName, $contents, $errorRefType, $errorReference, $previous = null)
	{
		$this->templateName = $templateName;
		$this->contents = $contents;

		if ($errorRefType == self::LINE)
		{
			$this->renderErrorLine = intval($errorReference);
		}
		else
		{
			$this->renderErrorLine = substr_count($contents, "\n", 0, intval($errorReference)) + 1;
		}

		$message .= " (on or near line $this->renderErrorLine)";

		parent::__construct($message, 0, $previous);
	}

	public function getContextLines()
	{
		if ($this->renderErrorLine === null)
		{
			return [];
		}

		$lines = explode("\n", $this->contents);

		$start = max(0, $this->renderErrorLine - 4);
		$end = min(count($lines), $this->renderErrorLine + 3);

		$context = [];

		for ($i = $start; $i < $end; $i++)
		{
			$context[$i + 1] = rtrim($lines[$i]);
			// + 1 as indexes are 0 based
		}

		return $context;
	}

	public function getContextLinesPrintable()
	{
		if ($this->renderErrorLine === null)
		{
			return [];
		}

		$output = [];
		foreach ($this->getContextLines() AS $lineNum => $code)
		{
			if ($this->renderErrorLine === $lineNum)
			{
				$output[] = "*$lineNum*| " . $code;
			}
			else
			{
				$output[] = " $lineNum | " . $code;
			}
		}

		return $output;
	}

	public static function createFromLessException(\Less_Exception_Parser $e, $templateName, $contents)
	{
		$parts = explode("\n", $e->getMessage());
		$message = $parts[0];
		$message = preg_replace("/anonymous-file-\d+\.less/i", $templateName, $message);

		return new self($message, $templateName, $contents, self::INDEX, $e->index, $e);
	}
}