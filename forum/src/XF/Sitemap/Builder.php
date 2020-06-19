<?php

namespace XF\Sitemap;

use XF\App;
use XF\Entity\SitemapLog;
use XF\Util\Arr;

class Builder
{
	/** @var App */
	protected $app;

	/** @var \XF\Entity\User */
	protected $actor;

	/** @var BuildState */
	protected $buildState;

	protected $file;

	protected $tempFileName;

	protected $jobMap = [
		'file_set' => 'fileSet',
		'file_count' => 'fileCount',
		'file_size' => 'fileSize',
		'file_entry_count' => 'fileEntryCount',
		'total_entry_count' => 'totalEntryCount',
		'pending_types' => 'pendingTypes',
		'current_type' => 'currentType',
		'last_type_id' => 'lastTypeId',
		'coreWritten' => 'coreWritten'
	];

	const MAX_FILE_SIZE = 10000000;
	const MAX_FILE_ENTRIES = 50000;

	public function __construct(App $app, \XF\Entity\User $actor, array $types)
	{
		$this->app = $app;
		$this->actor = $actor;
		$this->buildState = new BuildState($types);
	}

	public function setActor(\XF\Entity\User $actor)
	{
		$this->actor = $actor;
	}

	public function getActor()
	{
		return $this->actor;
	}

	public function getBuildState()
	{
		return $this->buildState;
	}

	public function build($maxRunTime = null)
	{
		$state = $this->buildState;

		$originalVisitor = \XF::visitor();
		\XF::setVisitor($this->actor);

		$buildType = $state->getActiveType();
		if (!$buildType)
		{
			$this->completeBuild();
			$hasMore = false;
		}
		else
		{
			if (!$state->coreWritten)
			{
				$this->writeCoreData();
				$state->coreWritten = true;
			}

			$this->buildType($buildType, $maxRunTime);

			$this->closeFile();
			$this->saveTempFile();
			$this->logPending();

			$hasMore = true;
		}

		\XF::setVisitor($originalVisitor);

		return $hasMore;
	}

	public function buildIndex(SitemapLog $sitemap)
	{
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
			. '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		$sitemapBase = $this->app->options()->boardUrl . '/sitemap.php?c=';

		for ($i = 1; $i <= $sitemap->file_count; $i++)
		{
			$url = $sitemapBase . $i;
			$output .= "\t"
				. '<sitemap>'
				. '<loc>' . htmlspecialchars($url) . '</loc>'
				. '<lastmod>' . gmdate(\DateTime::W3C, $sitemap->complete_date) . '</lastmod>'
				. '</sitemap>' . "\n";
		}

		$output .= '</sitemapindex>';

		return $output;
	}

	protected function writeCoreData()
	{
		$app = $this->app;

		$entries = [];
		$entries[] = Entry::create($app->router('public')->buildLink('canonical:index'));

		$options = $this->app->options();
		$extras = Arr::stringToArray($options->sitemapExtraUrls, '/\r?\n/');
		foreach ($extras AS $extra)
		{
			$url = \XF::canonicalizeUrl($extra);
			if (strpos($url, $options->boardUrl) === 0)
			{
				// right prefix
				$entries[] = Entry::create($extra);
			}
		}

		foreach ($entries AS $entry)
		{
			$this->writeEntry($entry);
		}
	}

	protected function buildType($contentType, $maxRunTime)
	{
		$state = $this->buildState;

		$buildResult = $this->writeContentTypeData($contentType, $state->lastTypeId, $maxRunTime);
		if ($buildResult === null)
		{
			// finished the type, move on
			$state->resetCurrentType();
		}
		else
		{
			$state->lastTypeId = $buildResult;
		}
	}

	protected function writeContentTypeData($contentType, $lastId, $maxRunTime)
	{
		$start = microtime(true);

		$handler = $this->getHandler($contentType);
		if (!$handler || !$handler->basePermissionCheck())
		{
			return null;
		}

		$records = $handler->getRecords($lastId);
		if (!$records || !count($records))
		{
			return null;
		}

		$indexUrl = $this->app->router('public')->buildLink('canonical:index');

		$newLast = null;
		foreach ($records AS $key => $record)
		{
			$newLast = $key;

			if ($handler->isIncluded($record))
			{
				/** @var Entry[] $entries */
				$entries = $handler->getEntry($record);
				if (!is_array($entries))
				{
					$entries = [$entries];
				}
				if ($entries)
				{
					foreach ($entries AS $entry)
					{
						if ($entry->loc == $indexUrl)
						{
							continue;
						}

						$this->writeEntry($entry);
					}
				}
			}

			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				break;
			}
		}

		return $newLast;
	}

	protected function writeEntry(Entry $entry)
	{
		$state = $this->buildState;

		if ($state->fileEntryCount >= self::MAX_FILE_ENTRIES)
		{
			$this->completeFile();
		}

		$state->entryAdded();

		$content = $this->buildEntryXml($entry);
		$this->writeToFile("\t" . trim($content) . "\n");
	}

	protected function buildEntryXml(Entry $entry)
	{
		$content = '<url>' . $this->buildSimpleTag($entry, 'loc');

		if ($entry->lastmod)
		{
			$content .= $this->buildSimpleTag($entry, 'lastmod', gmdate(\DateTime::W3C, $entry->lastmod));
		}

		if ($entry->priority)
		{
			$content .= $this->buildSimpleTag($entry, 'priority');
		}

		if ($entry->changefreq)
		{
			$content .= $this->buildSimpleTag($entry, 'changefreq');
		}

		if ($entry->image)
		{
			if (!is_array($entry->image) || isset($entry->image['loc']))
			{
				$images = [$entry->image];
			}
			else
			{
				$images = $entry->image;
			}

			foreach ($images AS $image)
			{
				if (!is_array($image))
				{
					$image = ['loc' => $image];
				}
				$content .= '<image:image>';
				foreach ($image AS $tag => $value)
				{
					$content .= $this->buildSimpleTag($entry, "image:$tag", $value);
				}
				$content .= '</image:image>';
			}
		}

		$content .= '</url>';

		return $content;
	}

	protected function buildSimpleTag(Entry $entry, $property, $value = null)
	{
		return "<$property>" . htmlspecialchars($value ?: $entry->{$property}, ENT_QUOTES, 'UTF-8') . "</$property>";
	}

	protected function writeToFile($content, $allowComplete = true)
	{
		if (!$this->file)
		{
			$this->openFile();
		}

		$state = $this->buildState;

		if ($state->fileSize == 0)
		{
			$preamble = $this->getPreamble();
			fwrite($this->file, $preamble);
			$state->fileSize += strlen($preamble);
		}

		fwrite($this->file, $content);
		$state->fileSize += strlen($content);

		if ($state->fileSize > self::MAX_FILE_SIZE && $allowComplete)
		{
			$this->completeFile();
		}
	}

	protected function openFile()
	{
		if (!$this->file)
		{
			$persistentTempFile = $this->getCurrentPersistentTempFileName();
			$fs = $this->app->fs();

			if ($fs->has($persistentTempFile))
			{
				$tempFileName = \XF\Util\File::copyAbstractedPathToTempFile($persistentTempFile);
			}
			else
			{
				$tempFileName = \XF\Util\File::getTempFile();
			}

			$this->tempFileName = $tempFileName;
			$this->file = fopen($tempFileName, 'a');
			flock($this->file, LOCK_EX);
		}
	}

	protected function getCurrentPersistentTempFileName()
	{
		$state = $this->buildState;

		return $this->getSitemapRepo()->getAbstractedSitemapFileName(
			$state->fileSet, $state->fileCount, false, true
		);
	}

	protected function completeFile()
	{
		$state = $this->buildState;
		if ($state->fileSize == 0)
		{
			return;
		}

		$this->writeToFile($this->getPostamble(), false);
		$this->closeFile();

		$tempFile = $this->tempFileName;
		$persistentTempFile = $this->getCurrentPersistentTempFileName();

		$canCompress = $this->canCompress();
		if ($canCompress)
		{
			$tempFile = $this->compressTempFile($tempFile); // old file removed by this
		}

		$fileName = $this->getSitemapRepo()->getAbstractedSitemapFileName(
			$state->fileSet, $state->fileCount, $canCompress
		);

		\XF\Util\File::copyFileToAbstractedPath($tempFile, $fileName);
		\XF\Util\File::deleteFromAbstractedPath($persistentTempFile);
		unlink($tempFile);
		$this->tempFileName = null;

		$state->incrementFile();
	}

	protected function compressTempFile($tempFile)
	{
		$readFile = fopen($tempFile, 'rb');

		$compressedFileName = $tempFile . '.gz';
		$compressedFile = gzopen($compressedFileName, 'wb1');

		$blockSize = 512 * 1024;

		while (!feof($readFile))
		{
			gzwrite($compressedFile, fread($readFile, $blockSize));
		}

		fclose($readFile);
		gzclose($compressedFile);

		unlink($tempFile);

		return $compressedFileName;
	}

	protected function closeFile()
	{
		if ($this->file)
		{
			fflush($this->file);
			flock($this->file, LOCK_UN);
			fclose($this->file);
			$this->file = null;
		}
	}

	protected function saveTempFile()
	{
		if ($this->tempFileName)
		{
			$persistentTempFile = $this->getCurrentPersistentTempFileName();
			\XF\Util\File::copyFileToAbstractedPath($this->tempFileName, $persistentTempFile);
		}
	}

	protected function completeBuild()
	{
		$this->completeFile();

		$state = $this->buildState;
		$siteMapRepo = $this->getSitemapRepo();

		$siteMapRepo->logCompletedBuild(
			$state->fileSet,
			$state->totalEntryCount,
			$state->fileCount - 1, // because we just completed a file, this will be 1 too high
			$this->canCompress()
		);

		$siteMapRepo->deactivateOldSitemaps($state->fileSet);
		$siteMapRepo->deleteOldSitemapLogs();

		$this->sendPing();
	}

	protected function canCompress()
	{
		return function_exists('gzopen');
	}

	protected function getPreamble()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>'
		. "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
		. ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
	}
	
	protected function getPostamble()
	{
		return '</urlset>';
	}

	public function getDataForJob()
	{
		$state = $this->buildState;
		$output = [];
		foreach ($this->jobMap AS $dataKey => $stateField)
		{
			$output[$dataKey] = $state->{$stateField};
		}

		return $output;
	}

	public function setupFromJobData(array $data)
	{
		$state = $this->buildState;
		foreach ($this->jobMap AS $dataKey => $stateField)
		{
			if (isset($data[$dataKey]))
			{
				$state->{$stateField} = $data[$dataKey];
			}
		}
	}

	protected function logPending()
	{
		$state = $this->buildState;

		$this->getSitemapRepo()->logPendingBuild(
			$state->fileSet,
			$state->totalEntryCount,
			$state->fileCount,
			$this->canCompress()
		);
	}

	protected function sendPing()
	{
		$options = $this->app->options();
		$autoSubmit = $options->sitemapAutoSubmit;
		if (!$autoSubmit || !$autoSubmit['enabled'] || $this->buildState->totalEntryCount <= 1)
		{
			// an entry count of 1 really just means the main URL, so it's almost certainly
			// a totally private board
			return;
		}

		$sitemapUrl = urlencode($options->boardUrl . '/sitemap.php');
		$pingUrls = Arr::stringToArray($autoSubmit['urls']);

		foreach ($pingUrls AS $pingUrl)
		{
			$url = str_replace('{$url}', $sitemapUrl, $pingUrl);

			try
			{
				$this->app->http()->client()->get($url);
			}
			catch(\GuzzleHttp\Exception\RequestException $e)
			{
				\XF::logException($e, false, "Error submitting sitemap to $url: ");
			}
		}
	}

	/**
	 * @param $contentType
	 *
	 * @return \XF\Sitemap\AbstractHandler|null
	 */
	protected function getHandler($contentType)
	{
		return $this->getSitemapRepo()->getSitemapHandler($contentType);
	}

	/**
	 * @return \XF\Repository\SitemapLog
	 */
	protected function getSitemapRepo()
	{
		return $this->app->repository('XF:SitemapLog');
	}
}