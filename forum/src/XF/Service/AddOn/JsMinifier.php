<?php

namespace XF\Service\AddOn;

use XF\Service\AbstractService;
use XF\Util\File;

class JsMinifier extends AbstractService
{
	protected $jsPath;
	protected $minPath;

	protected $options;

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $client;

	public function __construct(\XF\App $app, $jsPath, $minPath = null, array $compilerOptions = [])
	{
		parent::__construct($app);

		$this->jsPath = $jsPath;
		if ($minPath !== null)
		{
			$this->minPath = $minPath;
		}
		else
		{
			$this->minPath = preg_replace('(\.js$)', '.min.js', $jsPath, 1);
		}

		$this->setCompilerOptions($compilerOptions);
		$this->setHttpClient();
	}

	protected function setCompilerOptions(array $options = [])
	{
		$this->options = array_replace([
			'js_code' => file_get_contents($this->jsPath),
			'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
			'output_info' => 'compiled_code',
			'output_format' => 'json'
		], $options);
	}

	protected function setHttpClient()
	{
		$this->client = $this->app->http()->client();
	}

	/**
	 * @return string|null
	 * @throws \ErrorException
	 */
	public function minify()
	{
		$result = $this->request();

		if (isset($result['serverErrors']))
		{
			$this->processErrors($result['serverErrors'], 'Server errors encountered while compiling: ');
		}
		else if (isset($result['compiledCode']) && $result['compiledCode'] === '')
		{
			$errors = $this->request(true);
			if (!empty($errors['errors']))
			{
				$this->processErrors($errors['errors'], 'Syntax errors encountered while compiling: ');
			}

			$result = null;
		}
		else if (empty($result['compiledCode']))
		{
			$result = null;
		}

		if (!$result)
		{
			throw new \ErrorException('Empty result provided by the compiler.');
		}

		$result = $result['compiledCode'];
		File::writeFile($this->minPath, trim($result), false);

		return $result;
	}

	protected function request($getErrors = false)
	{
		$client = $this->client;
		$options = $this->options;

		if ($getErrors)
		{
			$options['output_info'] = 'errors';
		}

		try
		{
			$response = $client->post('https://closure-compiler.appspot.com/compile', [
				'form_params' => $options
			]);
			return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
		}
		catch (\GuzzleHttp\Exception\RequestException $e)
		{
			return null;
		}
	}

	protected function processErrors(array $errors, $errorPrefix = '')
	{
		$output = [];
		foreach ($errors AS $error)
		{
			$output[] = $error['error'];
		}
		throw new \ErrorException(($errorPrefix ? $errorPrefix . ' ' : '') . implode(', ', $output));
	}
}