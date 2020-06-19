<?php

namespace XF\Service\StructuredText;

use XF\Mvc\Entity\Entity;

class Preparer extends \XF\Service\AbstractService
{
	protected $context;

	/**
	 * @var Entity|null
	 */
	protected $contentEntity;

	protected $filters = [
		'mentions' => true,
		'shortToEmoji' => true,
	];

	protected $filtererSetup;

	protected $outputFilters = [];

	protected $mentionedUsers = [];

	protected $isValid = false;

	protected $errors = [];

	protected $constraints = [
		'maxLength' => 0
	];

	public function __construct(\XF\App $app, $context, Entity $contentEntity = null)
	{
		$this->context = $context;
		$this->contentEntity = $contentEntity;
		parent::__construct($app);
	}

	protected function setup()
	{
		$options = $this->app->options();

		$this->constraints = [
			'maxLength' => $options->messageMaxLength
		];
	}

	public function getContentEntity()
	{
		return $this->contentEntity;
	}

	public function setContentEntity(Entity $contentEntity = null)
	{
		$this->contentEntity = $contentEntity;
	}

	public function setConstraint($key, $value)
	{
		$this->constraints[$key] = $value;

		return $this;
	}

	public function setConstraints(array $constraints)
	{
		$this->constraints = array_merge($this->constraints, $constraints);

		return $this;
	}

	public function disableAllFilters()
	{
		foreach ($this->filters AS $key => $value)
		{
			$this->filters[$key] = false;
		}

		return $this;
	}

	public function disableFilter($key)
	{
		if (isset($this->filters[$key]))
		{
			$this->filters[$key] = false;
		}

		return $this;
	}

	public function enableFilter($key)
	{
		if (isset($this->filters[$key]))
		{
			$this->filters[$key] = true;
		}

		return $this;
	}

	public function prepare($message)
	{
		$this->setupFilterer();

		$this->checkValidity($message);

		$message = $this->filterOutput($message);

		return $message;
	}

	public function setupFilterer()
	{
		if ($this->filtererSetup)
		{
			return;
		}

		$this->filtererSetup = true;

		if ($this->filters['mentions'])
		{
			$this->addUserMentionFilter();
		}
		if ($this->filters['shortToEmoji'])
		{
			$this->addShortToEmojiFilter();
		}
	}

	public function addUserMentionFilter()
	{
		$this->outputFilters[] = [$this, 'filterFinalUserMentions'];
	}

	protected function filterFinalUserMentions($null, $string)
	{
		$mentions = $this->app->stringFormatter()->getMentionFormatter();

		$string = $mentions->getMentionsStructuredText($string);
		$this->mentionedUsers = $mentions->getMentionedUsers();

		return $string;
	}

	public function getMentionedUsers()
	{
		return $this->mentionedUsers;
	}

	public function addShortToEmojiFilter()
	{
		$this->outputFilters[] = [$this, 'filterFinalShortToEmoji'];
	}

	protected function filterFinalShortToEmoji($null, $string)
	{
		if (!$this->app->options()->shortcodeToEmoji || !$this->app->config('fullUnicode'))
		{
			return $string;
		}

		$emoji = $this->app->stringFormatter()->getEmojiFormatter();

		$string = $emoji->formatShortnameToEmoji($string);

		return $string;
	}

	public function filterOutput($output)
	{
		foreach ($this->outputFilters AS $filter)
		{
			$output = call_user_func($filter, $this, $output);
		}

		return $output;
	}

	public function checkValidity($message)
	{
		$this->errors = [];

		$maxLength = $this->constraints['maxLength'];
		if ($maxLength && utf8_strlen($message) > $maxLength)
		{
			$this->errors[] = \XF::phraseDeferred(
				'please_enter_message_with_no_more_than_x_characters',
				['count' => $maxLength]
			);
		}

		$this->isValid = (count($this->errors) == 0);

		return $this->isValid;
	}

	public function isValid()
	{
		return $this->isValid;
	}

	public function pushEntityErrorIfInvalid(\XF\Mvc\Entity\Entity $entity, $fieldName = 'message')
	{
		if (!$this->isValid())
		{
			$entity->error($this->getFirstError(), $fieldName);
			return false;
		}
		else
		{
			return true;
		}
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getFirstError()
	{
		return reset($this->errors);
	}
}