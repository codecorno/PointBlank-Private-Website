<?php

namespace XF\Import\Data;

use XF\Import\DataManager;

abstract class AbstractData
{
	/**
	 * @var DataManager
	 */
	protected $dataManager;

	/**
	 * @var bool
	 */
	protected $log = true;

	protected $checkExisting = true;

	protected $useTransaction = true;

	/**
	 * @var bool
	 */
	protected $allowRetainIds = true;

	/**
	 * This text will be used as content for any required text field that comes through empty
	 *
	 * @var array
	 */
	protected $defaultText = [
		'title' => '[No title]',
		'message' => '[No message]',
		'response' => '[No response text]',
		'username' => 'Guest'
	];

	protected $logExtra = [];

	abstract public function getImportType();
	abstract public function set($field, $value, array $options = []);
	abstract public function get($field);
	abstract protected function write($oldId);
	abstract protected function importedIdFound($oldId, $newId);

	public function __construct(DataManager $dataManager, $log = true)
	{
		$this->dataManager = $dataManager;
		$this->log = $log;

		$this->init();
	}

	protected function init()
	{
	}

	/**
	 * Use this to prevent IDs being retained, even when the import has been configured to retain them.
	 * This is necessary when the source ID format does is not compatible with the destination ID format.
	 */
	public function preventRetainIds()
	{
		$this->allowRetainIds = false;
	}

	public function retainIds()
	{
		return $this->allowRetainIds && $this->dataManager->getRetainIds();
	}

	public function log($log)
	{
		$this->log = $log;
	}

	public function checkExisting($check)
	{
		$this->checkExisting = $check;
	}

	public function useTransaction($use)
	{
		$this->useTransaction = $use;
	}

	public function isLogged()
	{
		return $this->log;
	}

	public function bulkSet(array $values, array $options = [])
	{
		foreach ($values AS $key => $value)
		{
			$this->set($key, $value, $options);
		}
	}

	public function save($oldId)
	{
		if ($oldId !== false && $this->log && $this->checkExisting)
		{
			$mappedId = $this->dataManager->lookup($this->getImportType(), $oldId);
			if ($mappedId !== false)
			{
				return $mappedId;
			}
		}

		$preSave = $this->preSave($oldId);
		if ($preSave === false)
		{
			return false;
		}

		$db = $this->dataManager->db();
		if ($this->useTransaction)
		{
			$db->beginTransaction();
		}

		try
		{
			$newId = $this->write($oldId);

			if ($newId !== false)
			{
				if ($oldId !== false && $this->log)
				{
					$this->dataManager->log($this->getImportType(), $oldId, $newId);
				}

				$this->postSave($oldId, $newId);
			}
		}
		catch (\Exception $e)
		{
			if ($this->useTransaction)
			{
				$db->rollback();
			}

			throw $e;
		}

		if ($this->useTransaction)
		{
			$db->commit();
		}

		return $newId;
	}

	protected function preSave($oldId)
	{
		return null; // return false to prevent save
	}

	protected function postSave($oldId, $newId)
	{
	}

	public function convertToUtf8($string, $fromCharset = null, $convertHtml = null)
	{
		return $this->dataManager->convertToUtf8($string, $fromCharset, $convertHtml);
	}

	public function stripExtendedUtf8IfNeeded($string)
	{
		return $this->dataManager->stripExtendedUtf8IfNeeded($string);
	}

	public function convertToUtf8IfNeeded($string, $fromCharset = null, $convertHtml = null)
	{
		return $this->dataManager->convertToUtf8IfNeeded($string, $fromCharset, $convertHtml);
	}


	/**
	 * If (strval($string) === '') then replace it with the default text defined by $this->defaultText[$fieldType]
	 * in order to prevent validation / insertion errors.
	 *
	 * $contentId and $fieldName are there for future use, and represent the name of the content ID of the item to
	 * which this field belongs, and the name of the field, if different from the $fieldType string (often identical)
	 *
	 * @param      $string
	 * @param      $fieldType
	 * @param null $contentId
	 * @param null $fieldName
	 *
	 * @return mixed
	 */
	public function validTextOrDefault($string, $fieldType, $contentId = null, $fieldName = null)
	{
		if (trim(strval($string)) === '')
		{
			if (!isset($this->defaultText[$fieldType]))
			{
				$fieldType = 'message';
			}

			// TODO: perhaps make a session note saying that this text was defaulted using $oldId and $fieldName?
			/*if ($contentId !== null)
			{
				if ($fieldName === null)
				{
					$fieldName = $fieldType;
				}

				// save note...
			}*/

			return $this->defaultText[$fieldType];
		}

		return $string;
	}

	/**
	 * Allows extra data to be logged in the event that we need to map other content types to the inserted data
	 * such as 'node', 'node-url-fragment', $newId or 'blog_user', $blogId, $newNodeId
	 *
	 * @param $contentType (whatever we need to map from)
	 * @param $oldId
	 * @param $newId
	 */
	protected function logExtra($contentType, $oldId, $newId)
	{
		$this->dataManager->log($contentType, $oldId, $newId);
	}

	protected function insertMasterPhrase($title, $value, array $extra = [], $silent = false, $convertToUtf8 = true)
	{
		$phraseText = $convertToUtf8 ? $this->convertToUtf8IfNeeded($value) : $value;

		/** @var \XF\Entity\Phrase $phrase */
		$phrase = $this->dataManager->em()->create('XF:Phrase');

		$phrase->title = $title;
		$phrase->phrase_text = (string)$phraseText;
		$phrase->language_id = 0;
		$phrase->addon_id = '';
		$phrase->bulkSet($extra);

		if ($phrase->save($silent ? false : true, false))
		{
			$this->dataManager->em()->detachEntity($phrase);
		}
	}

	protected function insertTemplate($title, $value, $type = 'public', $extra = [], $silent = true, $convertToUtf8 = true)
	{
		/** @var \XF\Entity\Template $template */
		$template = $this->dataManager->em()->create('XF:Template');

		$template->title = $title;
		$template->type = $type;
		$template->style_id = 0;
		$template->addon_id = '';
		$template->setTemplateUnchecked($convertToUtf8 ? $this->convertToUtf8IfNeeded($value) : $value);
		$template->bulkSet($extra);

		$template->save($silent ? false : true, false);

		$this->dataManager->em()->detachEntity($template);
	}

	protected function importRawIp($userId, $contentType, $contentId, $action, $ip, $date)
	{
		$ip = \XF\Util\Ip::convertIpStringToBinary($ip);

		$this->db()->insert('xf_ip', [
			'user_id' => $userId,
			'content_type' => $contentType,
			'content_id' => $contentId,
			'action' => $action,
			'ip' => $ip,
			'log_date' => $date
		]);

		return $this->db()->lastInsertId();
	}

	protected function insertCustomFieldValues($tableName, $contentColumn, $newId, array $customFields)
	{
		$insert = [];
		foreach ($customFields AS $key => $value)
		{
			$insert[] = [
				$contentColumn => $newId,
				'field_id' => $key,
				'field_value' => is_array($value) ? serialize($value) : $value
			];
		}

		if ($insert)
		{
			$this->db()->insertBulk($tableName, $insert, false, 'field_value = VALUES(field_value)');
		}
	}

	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	public function __get($key)
	{
		return $this->get($key);
	}

	public function db()
	{
		return $this->dataManager->db();
	}

	public function repository($repo)
	{
		return $this->em()->getRepository($repo);
	}

	public function em()
	{
		return $this->dataManager->em();
	}

	public function app()
	{
		return $this->dataManager->app();
	}
}