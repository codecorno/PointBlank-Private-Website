<?php

namespace XF;

use Symfony\Component\VarDumper\VarDumper;
use XF\Db\AbstractAdapter;

class Debugger
{
	/**
	 * @var App
	 */
	protected $app;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function dump($var)
	{
		VarDumper::dump($var);
	}

	public function dumpSimple($var, $echo = true, $escape = true)
	{
		ob_start();
		var_dump($var);
		$dump = ob_get_clean();

		$dump = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $dump);
		$dump = utf8_bad_replace($dump, "\xEF\xBF\xBD");

		if (PHP_SAPI == 'cli')
		{
			$output = $dump;
		}
		else
		{
			if ($escape)
			{
				$output = '<pre>' . htmlspecialchars($dump) . '</pre>';
			}
			else
			{
				$output = $dump;
			}
		}

		if ($echo)
		{
			echo $output;
		}

		return $output;
	}

	public function dumpConsole($var, $type = 'log')
	{
		$method = null;

		switch ($type)
		{
			case 'log':
			case 'warn':
			case 'error':
			case 'info':
				$method = $type;
				break;
			default:
				$method = 'log';
				break;
		}

		return \ChromePhp::$method($var);
	}

	public function dumpToFile($var, $logName = null)
	{
		if ($logName === null)
		{
			$logName = 'log_' . strval(\XF::$time - (\XF::$time % 86400));
		}

		$dump = $this->dumpSimple($var, false, false);

		return \XF\Util\File::log($logName, $dump);
	}

	public function getDebugPageHtml(App $app = null)
	{
		if (!$app)
		{
			$app = $this->app;
		}

		$pageTime = microtime(true) - $app['time.granular'];
		$memoryUsage = memory_get_usage();
		$memoryUsagePeak = memory_get_peak_usage();
		$dbDebug = $this->getDatabaseDebugInfo($app['db']);
		$dbPercent = ($dbDebug['totalQueryRunTime'] / $pageTime) * 100;

		$includedFiles = $this->getIncludedFilesDebugInfo(get_included_files());

		$return = "<h1>Page Time: " . number_format($pageTime, 4) . "s</h1>"
			. "<h2>Memory: " . number_format($memoryUsage / 1024 / 1024, 4) . " MB "
			. "(Peak: " . number_format($memoryUsagePeak / 1024 / 1024, 4) . " MB)</h2>"
			. "<h2>Queries ($dbDebug[queryCount], time: " . number_format($dbDebug['totalQueryRunTime'], 4) . "s, "
			. number_format($dbPercent, 1) . "%)</h2>"
			. $dbDebug['queryHtml']
			. "<h2>Included Files ($includedFiles[includedFileCount], XenForo Classes: $includedFiles[includedXenForoClasses])</h2>"
			. $includedFiles['includedFileHtml'];

		if ($dbDebug['connectionStatsHtml'])
		{
			$return .= "\n<h2>DB Connection Stats</h2>" . $dbDebug['connectionStatsHtml'];
		}

		return $this->getDebugPageWrapperHtml($return);
	}

	public function getDebugPageWrapperHtml($debugHtml)
	{
		return <<<DEBUG
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
	<title>XenForo Debug Output</title>
</head>
<body>
$debugHtml
</body>
</html>
DEBUG;
	}

	/**
	 * Gets database debug information, including query count and run time and
	 * the actual queries that were run.
	 *
	 * @param AbstractAdapter $db
	 *
	 * @return array Keys: queryCount, totalQueryRunTime, queryHtml
	 */
	public static function getDatabaseDebugInfo(AbstractAdapter $db)
	{
		$return = [
			'queryCount' => 0,
			'totalQueryRunTime' => 0,
			'queryHtml' => '',
			'connectionStats' => null,
			'connectionStatsHtml' => ''
		];

		$return['queryCount'] = $db->getQueryCount();

		$rootDir = \XF::getRootDirectory() . \XF::$DS;

		if ($return['queryCount'])
		{
			$return['queryHtml'] .= '<ol>';

			$queries = $db->getQueryLog();
			foreach ($queries AS $query)
			{
				$queryText = rtrim($query['query']);
				if (preg_match('#(^|\n)(\t+)([ ]*)(?=\S)#', $queryText, $match))
				{
					$queryText = preg_replace('#(^|\n)\t{1,' . strlen($match[2]) . '}#', '$1', $queryText);
				}

				$boundParams = [];
				if (is_array($query['params']))
				{
					foreach ($query['params'] AS $param)
					{
						$boundParams[] = htmlspecialchars($param);
					}
				}

				$explainOutput = '';

				if (preg_match('#^\s*SELECT\s#i', $queryText) && is_array($query['params']))
				{
					$explainQuery = $db->query(
						'EXPLAIN ' . $query['query'],
						$query['params']
					);
					$explainRows = $explainQuery->fetchAll();
					if ($explainRows)
					{
						$explainOutput .= '<table border="1">'
							. '<tr>'
							. '<th>Select Type</th><th>Table</th><th>Type</th><th>Possible Keys</th>'
							. '<th>Key</th><th>Key Len</th><th>Ref</th><th>Rows</th><th>Extra</th>'
							. '</tr>';

						foreach ($explainRows AS $explainRow)
						{
							foreach ($explainRow AS $key => $value)
							{
								if (trim($value) === '')
								{
									$explainRow[$key] = '&nbsp;';
								}
								else
								{
									$explainRow[$key] = htmlspecialchars($value);
								}
							}

							$explainOutput .= '<tr>'
								. '<td>' . $explainRow['select_type'] . '</td>'
								. '<td>' . $explainRow['table'] . '</td>'
								. '<td>' . $explainRow['type'] . '</td>'
								. '<td>' . $explainRow['possible_keys'] . '</td>'
								. '<td>' . $explainRow['key'] . '</td>'
								. '<td>' . $explainRow['key_len'] . '</td>'
								. '<td>' . $explainRow['ref'] . '</td>'
								. '<td>' . $explainRow['rows'] . '</td>'
								. '<td>' . $explainRow['Extra'] . '</td>'
								. '</tr>';
						}

						$explainOutput .= '</table>';
					}
				}

				$traceHtml = '';
				if (is_array($query['trace']))
				{
					foreach ($query['trace'] AS $traceEntry)
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

					$traceHtml = "<br /><ol>$traceHtml</ol>";
				}

				$queryComplete = isset($query['complete']) ? $query['complete'] : $query['start'];
				$queryTime = $queryComplete - $query['start'];

				$return['queryHtml'] .= '<li>'
					. '<pre>' . htmlspecialchars($queryText) . '</pre>'
					. ($boundParams ? '<div><strong>Params:</strong> ' . implode(', ', $boundParams) . '</div>' : '')
					. '<div><strong>Run Time:</strong> ' . number_format($queryTime, 6) . '</div>'
					. $explainOutput
					. $traceHtml
					. "</li>\n";

				$return['totalQueryRunTime'] += $queryTime;
			}

			$return['queryHtml'] .= '</ol>';
		}

		$return['connectionStats'] = $db->getConnectionStats();
		if ($return['connectionStats'])
		{
			$statsHtml = "<table>\n";

			foreach ($return['connectionStats'] AS $statName => $statValue)
			{
				$statsHtml .= "<tr><td>"
					. htmlspecialchars($statName) . "</td><td>"
					. htmlspecialchars($statValue) . "</td></tr>\n";
			}

			$statsHtml .= "</table>\n";

			$return['connectionStatsHtml'] = $statsHtml;
		}

		return $return;
	}

	/**
	 * Gets included files debug info.
	 *
	 * @param array $includedFiles
	 *
	 * @return array Keys: includedFileCount, incldedFileHtml, includedForoClasses
	 */
	public static function getIncludedFilesDebugInfo(array $includedFiles)
	{
		$return = [
			'includedFileCount' => count($includedFiles),
			'includedFileHtml' => '<ol>',
			'includedXenForoClasses' => 0
		];

		$baseDir = dirname(reset($includedFiles));

		foreach ($includedFiles AS $file)
		{
			$file = preg_replace('#^' . preg_quote($baseDir, '#') . '(\\\\|/)#', '', $file);
			$file = htmlspecialchars($file);

			if (preg_match('#^library(/|\\\\)XenForo(/|\\\\)|src(/|\\\\)XF(/|\\\\)#', $file))
			{
				$return['includedXenForoClasses']++;
			}
			$file = preg_replace('#^library(/|\\\\)XenForo(/|\\\\)|src(/|\\\\)XF(/|\\\\)#', '<b>$0</b>', $file);

			$return['includedFileHtml'] .= '<li>' . $file . '</li>' . "\n";
		}
		$return['includedFileHtml'] .= '</ol>';

		return $return;
	}
}