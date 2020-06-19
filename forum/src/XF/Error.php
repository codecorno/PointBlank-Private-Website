<?php

namespace XF;

use XF\Util\Ip;

class Error
{
	/**
	 * @var App
	 */
	protected $app;

	protected $ignorePendingUpgrade = false;
	protected $forceShowTrace = false;

	protected $hasPendingUpgrade = null;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function setIgnorePendingUpgrade($pending)
	{
		$this->ignorePendingUpgrade = $pending;
	}

	public function setForceShowTrace($force)
	{
		$this->forceShowTrace = $force;
	}

	public function hasPendingUpgrade()
	{
		if ($this->hasPendingUpgrade !== null)
		{
			return $this->hasPendingUpgrade;
		}

		$this->hasPendingUpgrade = false;

		try
		{
			$db = $this->app->db();
			if (@$db->getConnection())
			{
				$dbVersionId = @$db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = 'currentVersionId'");
				if ($dbVersionId)
				{
					if ($dbVersionId != \XF::$versionId)
					{
						$this->hasPendingUpgrade = true;
					}
					else
					{
						$processingAddOn = @$db->fetchOne("
							SELECT addon_id
							FROM xf_addon
							WHERE is_processing = 1
							LIMIT 1
						");
						$this->hasPendingUpgrade = $processingAddOn ? true : false;
					}
				}
			}
		}
		catch (\Exception $e) {}

		return $this->hasPendingUpgrade;
	}

	public function logError($message, $forceLog = false)
	{
		$this->logException(new \ErrorException($message), false, '', $forceLog);
	}

	public function logException($e, $rollback = false, $messagePrefix = '', $forceLog = false)
	{
		/** @var \Throwable $e */

		try
		{
			$db = $this->app->db();
			if (@$db->getConnection())
			{
				if ($rollback)
				{
					@$db->rollbackAll();
				}

				if (!$forceLog)
				{
					if ($this->hasPendingUpgrade() && !$this->ignorePendingUpgrade)
					{
						// don't log when upgrades are pending
						return false;
					}

					if (!\XF\Util\File::installLockExists())
					{
						// install hasn't finished yet, don't write
						return false;
					}
				}

				$isValidArg = ($e instanceof \Exception || $e instanceof \Throwable);
				if (!$isValidArg)
				{
					$e = new \ErrorException('Non-exception passed to logException. See trace for details.');
				}

				$rootDir = \XF::getRootDirectory() . \XF::$DS;
				$file = str_replace($rootDir, '', $e->getFile());

				$requestInfo = $this->getRequestDataForExceptionLog();

				if ($messagePrefix)
				{
					$messagePrefix = trim($messagePrefix) . ' ';
				}

				$trace = $this->getTraceStringFromThrowable($e);

				$traceExtras = $this->addExtrasToTrace($e);
				if ($traceExtras)
				{
					$trace = $traceExtras . "\n------------\n\n" . $trace;
				}

				$exceptionMessage = $this->adjustExceptionMessage($e->getMessage(), $e);

				$db->insert('xf_error_log', [
					'exception_date' => \XF::$time,
					'user_id' => \XF::visitor()->user_id,
					'ip_address' => Ip::convertIpStringToBinary($this->app->request()->getIp()),
					'exception_type' => utf8_substr(get_class($e), 0, 75),
					'message' => utf8_substr($messagePrefix . $exceptionMessage, 0, 20000),
					'filename' => utf8_substr($file, 0, 255),
					'line' => $e->getLine(),
					'trace_string' => $trace,
					'request_state' => json_encode($requestInfo, JSON_PARTIAL_OUTPUT_ON_ERROR)
				]);

				return true;
			}
		}
		catch (\Exception $e) {}

		return false;
	}

	protected function getTraceStringFromThrowable($e)
	{
		/** @var \Throwable $e */

		$trace = $this->buildTraceString($e);

		while ($e->getPrevious())
		{
			$e = $e->getPrevious();

			$trace .= "\n\n-------------\n\n"
				. "Previous " . get_class($e) . ": " . $e->getMessage()
				. " - " . $e->getFile() . ':' . $e->getLine() . "\n"
				. $this->buildTraceString($e);
		}

		$rootDir = \XF::getRootDirectory() . \XF::$DS;
		$trace = str_replace($rootDir, '', $trace);

		return $trace;
	}

	protected function buildTraceString($e)
	{
		/** @var \Throwable $e */

		$traceElements = $e->getTrace();

		$traceString = "";

		$num = 0;

		foreach ($traceElements AS $num => $element)
		{
			if (isset($element['file']))
			{
				$file = $element['file'];
				$location = "$file({$element['line']})";
			}
			else
			{
				$location = "[internal function]";
			}

			$traceString .= "#$num $location: ";

			if (isset($element['class']) && isset($element['type']))
			{
				$traceString .= $element['class'] . $element['type'];
			}

			$traceString .= $element['function'] . '(';
			$traceString .= implode(', ', $this->buildTraceArgs($element));
			$traceString .= ")\n";
		}

		$traceString .= "#" . strval($num + 1) . " {main}";

		return $traceString;
	}

	protected function buildTraceArgs(array $traceElement)
	{
		$methodParameters = [];

		try
		{
			if (isset($traceElement['class']))
			{
				$class = new \ReflectionClass($traceElement['class']);
				$method = $class->getMethod($traceElement['function']);
				$methodParameters = $method->getParameters();
			}
			else if (isset($traceElement['function']))
			{
				$method = new \ReflectionFunction($traceElement['function']);
				$methodParameters = $method->getParameters();
			}
		} catch (\ReflectionException $e)
		{
			// Can happen with closures
		}

		if (empty($traceElement['args']))
		{
			return [];
		}

		$args = [];

		foreach ($traceElement['args'] AS $key => $arg)
		{
			// This might not be set
			$methodParameter = isset($methodParameters[$key]) ? $methodParameters[$key] : null;

			switch (gettype($arg))
			{
				case 'NULL':
					$args[] = "NULL";
					break;
				case 'string':
					if ($methodParameter && stripos($methodParameter->getName(), 'password') !== false)
					{
						$arg = '*****';
					}

					$tmp = substr($arg, 0, min(strlen($arg), 15));
					if (strlen($arg) > 15)
					{
						$tmp .= '...';
					}
					$tmp = str_replace('\\', '\\\\', $tmp);
					$args[] = "'$tmp'";
					break;
				case 'boolean':
					if ($arg)
					{
						$args[] = 'true';
					}
					else
					{
						$args[] = 'false';
					}
					break;
				case 'resource (closed)':
				case 'resource':
					$args[] = "Resource id #" . intval($arg);
					break;
				case 'integer':
					$args[] = $arg;
					break;
				case 'double':
					$args[] = sprintf('%.*G', $arg);
					break;
				case 'array':
					$args[] = 'Array';
					break;
				case 'object':
					$args[] = 'Object(' . get_class($arg) . ')';
					break;
				default:
					$args[] = '(Unknown parameter type)';
					break;
			}
		}

		return $args;
	}

	protected function addExtrasToTrace($e)
	{
		if ($e instanceof \XF\Db\Exception && $e->query)
		{
			return $e->query;
		}

		if ($e instanceof \XF\CssRenderException)
		{
			return implode("\n", $e->getContextLinesPrintable());
		}

		return '';
	}

	protected function adjustExceptionMessage($message, $e)
	{
		return $message;
	}

	protected function getRequestDataForExceptionLog()
	{
		if (PHP_SAPI == 'cli')
		{
			$command = isset($GLOBALS['argv']) ? implode(' ', $GLOBALS['argv']) : '';

			return [
				'cli' => $command
			];
		}

		$request = $this->app->request();

		return [
			'url' => $request->getRequestUri(),
			'referrer' => $request->getReferrer(),
			'_GET' => $_GET,
			'_POST' => $request->filterForLog($_POST)
		];
	}

	public function displayFatalExceptionMessage($e)
	{
		$upgradePending = $this->hasPendingUpgrade();
		$isInstalled = \XF\Util\File::installLockExists();
		$ignorePendingUpgrade = (!$isInstalled || $this->ignorePendingUpgrade || $this->forceShowTrace);

		if (\XF::$debugMode || !$isInstalled)
		{
			$showTrace = true;
		}
		else if (\XF::visitor()->user_id)
		{
			$showTrace = \XF::visitor()->is_admin;
		}
		else
		{
			$showTrace = false;
		}
		if ($this->forceShowTrace)
		{
			$showTrace = true;
		}

		@header('Content-Type: text/html; charset=utf-8', true, 500);

		if ($upgradePending && !$ignorePendingUpgrade)
		{
			echo $this->getPhrasedTextIfPossible(
				'The site is currently being upgraded. Please check back later.',
				'site_currently_being_upgraded'
			);
		}
		else if ($showTrace)
		{
			echo $this->getExceptionTraceHtml($e);
		}
		else if ($e instanceof Db\Exception)
		{
			$message = $e->getMessage();

			echo $this->getPhrasedTextIfPossible(
				'An unexpected database error occurred. Please try again later.',
				'unexpected_database_error_occurred'
			);
			echo "\n<!-- " . htmlspecialchars($message) . " -->";
		}
		else
		{
			echo $this->getPhrasedTextIfPossible(
				'An unexpected error occurred. Please try again later.',
				'unexpected_error_occurred'
			);
		}
	}

	protected function getPhrasedTextIfPossible($fallbackText, $phraseName, array $params = [])
	{
		try
		{
			$output = \XF::phrase($phraseName, $params)->render();
		}
		catch (\Exception $e)
		{
			$output = false;
		}

		if ($output === false || $output === $phraseName)
		{
			$output = $fallbackText;
		}

		return $output;
	}

	public function getExceptionTraceHtml($e)
	{
		/** @var \Throwable $e */

		$rootDir = \XF::getRootDirectory() . \XF::$DS;

		if (PHP_SAPI == 'cli' || \XF::app()->request()->isXhr())
		{
			$file = str_replace($rootDir, '', $e->getFile());
			$trace = str_replace($rootDir, '', $this->buildTraceString($e));

			$class = get_class($e);

			return PHP_EOL
				. "An exception occurred: [$class] {$e->getMessage()} in {$file} on line {$e->getLine()}"
				. PHP_EOL . $trace . PHP_EOL;
		}

		$traceHtml = '';

		foreach ($e->getTrace() AS $traceEntry)
		{
			$function = (isset($traceEntry['class']) ? $traceEntry['class'] . $traceEntry['type'] : '') . $traceEntry['function'];
			if (isset($traceEntry['file']))
			{
				$file = str_replace('\\', '/', str_replace($rootDir, '', $traceEntry['file']));
			}
			else
			{
				$file = '';
			}
			$traceHtml .= "\t<li><b class=\"function\">" . htmlspecialchars($function) . "()</b>" . (isset($traceEntry['file']) && isset($traceEntry['line']) ? ' <span class="shade">in</span> <b class="file">' . $file . "</b> <span class=\"shade\">at line</span> <b class=\"line\">$traceEntry[line]</b>" : '') . "</li>\n";
		}

		$class = htmlspecialchars(get_class($e));
		$message = htmlspecialchars($e->getMessage());
		$file = htmlspecialchars(str_replace($rootDir, '', $e->getFile()));
		$line = $e->getLine();

		return "<p>An exception occurred: [$class] $message in $file on line $line</p><ol>$traceHtml</ol>";
	}
}
