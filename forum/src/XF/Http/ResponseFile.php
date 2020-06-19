<?php

namespace XF\Http;

class ResponseFile
{
	protected $fileName = '';
	protected $contents = null;

	public function __construct($fileName)
	{
		if (!file_exists($fileName))
		{
			throw new \InvalidArgumentException('File does not exist');
		}
		if (!is_readable($fileName))
		{
			throw new \InvalidArgumentException('File is not readable');
		}

		$this->fileName = $fileName;
	}

	public function __toString()
	{
		return $this->getContents();
	}

	public function output()
	{
		if ($this->contents === null)
		{
			readfile($this->fileName);
		}
		else
		{
			echo $this->contents;
		}
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function getLength()
	{
		return $this->contents === null ? filesize($this->fileName) : strlen($this->contents);
	}

	public function getContents()
	{
		if ($this->contents === null)
		{
			$this->contents = file_get_contents($this->fileName);
		}

		return $this->contents;
	}
}