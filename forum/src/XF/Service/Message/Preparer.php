<?php

namespace XF\Service\Message;

use XF\Mvc\Entity\Entity;

class Preparer extends \XF\Service\AbstractService
{
	protected $context;

	/**
	 * @var Entity|null
	 */
	protected $messageEntity;

	/**
	 * @var \XF\BbCode\Processor
	 */
	protected $bbCodeProcessor;

	protected $filters = [
		'autolink' => true,
		'mentions' => true,
		'markdown' => true,
		'shortToEmoji' => true,
		'structuredText' => false
	];

	protected $attachments = [];

	protected $quotes = [];

	protected $mentionedUsers = [];

	protected $unfurls = [];

	protected $isValid = false;

	protected $errors = [];

	protected $constraints = [
		'maxLength' => 0,
		'maxImages' => 0,
		'maxMedia' => 0,
		'allowEmpty' => false
	];

	public function __construct(\XF\App $app, $context, Entity $messageEntity = null)
	{
		$this->context = $context;
		$this->messageEntity = $messageEntity;
		parent::__construct($app);
	}

	protected function setup()
	{
		$options = $this->app->options();

		$this->setConstraints([
			'maxLength' => $options->messageMaxLength,
			'maxImages' => $options->messageMaxImages,
			'maxMedia' => $options->messageMaxMedia
		]);
	}

	public function getMessageEntity()
	{
		return $this->messageEntity;
	}

	public function setMessageEntity(Entity $messageEntity = null)
	{
		$this->messageEntity = $messageEntity;
	}

	public function setConstraint($key, $value)
	{
		$this->constraints[$key] = $value;

		return $this;
	}

	public function setConstraints(array $constraints)
	{
		$this->constraints = array_replace($this->constraints, $constraints);

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

	public function prepare($message, $checkValidity = true)
	{
		$message = $this->processMessage($message);

		if ($checkValidity)
		{
			$this->checkValidity($message);
		}
		else
		{
			$this->isValid = true;
		}

		/** @var \XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
		$usage = $this->bbCodeProcessor->getAnalyzer('usage');

		/** @var \XF\BbCode\ProcessorAction\MentionUsers|null $mentions */
		$mentions = $this->bbCodeProcessor->getFilterer('mentions');

		$this->attachments = $usage->getAttachments();
		$this->quotes = $usage->getQuotes();
		$this->mentionedUsers = $mentions ? $mentions->getMentionedUsers() : [];
		$this->unfurls = $usage->getUnfurls();

		return $message;
	}

	protected function processMessage($message)
	{
		$this->bbCodeProcessor = $this->getBbCodeProcessor();

		$bbCodeContainer = $this->app->bbCode();

		$renderContext = $this->context . ':prepare';
		$options = $bbCodeContainer->getFullRenderOptions($this->messageEntity, $renderContext, null);

		return $this->bbCodeProcessor->render(
			$message,
			$bbCodeContainer->parser(),
			$bbCodeContainer->rules($renderContext),
			$options
		);
	}

	protected function getBbCodeProcessor()
	{
		$bbCodeContainer = $this->app->bbCode();

		$processor = $bbCodeContainer->processor();

		$processor->addProcessorAction('usage', $bbCodeContainer->processorAction('usage'));

		if ($this->filters['mentions'])
		{
			$processor->addProcessorAction('mentions', $bbCodeContainer->processorAction('mentions'));
		}
		if ($this->filters['markdown'])
		{
			$processor->addProcessorAction('markdown', $bbCodeContainer->processorAction('markdown'));
		}
		if ($this->filters['autolink'])
		{
			$processor->addProcessorAction('autolink', $bbCodeContainer->processorAction('autolink'));
		}
		if ($this->filters['shortToEmoji'])
		{
			$processor->addProcessorAction('shortToEmoji', $bbCodeContainer->processorAction('shortToEmoji'));
		}
		if ($this->filters['structuredText'])
		{
			$processor->addProcessorAction('structuredText', $bbCodeContainer->processorAction('structuredText'));
		}

		return $processor;
	}

	public function getEmbeddedAttachments()
	{
		return $this->attachments;
	}

	public function getEmbeddedUnfurls()
	{
		return $this->unfurls;
	}

	public function getEmbedMetadata()
	{
		$metadata = [];

		if ($this->attachments)
		{
			$metadata['attachments'] = $this->attachments;
		}

		if ($this->unfurls)
		{
			$metadata['unfurls'] = $this->unfurls;
		}

		return $metadata;
	}

	public function getQuotes()
	{
		return $this->quotes;
	}

	public function getQuotesKeyed($key)
	{
		$quotes = [];
		foreach ($this->quotes AS $quote)
		{
			if (isset($quote[$key]))
			{
				$quotes[$quote[$key]] = $quote;
			}
		}

		return $quotes;
	}

	public function getMentionedUsers()
	{
		return $this->mentionedUsers;
	}

	public function checkValidity($message)
	{
		$this->errors = [];

		/** @var \XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
		$usage = $this->bbCodeProcessor->getAnalyzer('usage');

		$maxLength = $this->constraints['maxLength'];
		if ($maxLength && utf8_strlen($message) > $maxLength)
		{
			$this->errors[] = \XF::phraseDeferred(
				'please_enter_message_with_no_more_than_x_characters',
				['count' => $maxLength]
			);
		}

		$maxImages = $this->constraints['maxImages'];
		if ($maxImages && $usage->getTagCount('img') > $maxImages)
		{
			$this->errors[] = \XF::phraseDeferred(
				'please_enter_message_with_no_more_than_x_images',
				['count' => $maxImages]
			);
		}

		$maxMedia = $this->constraints['maxMedia'];
		if ($maxMedia && $usage->getTagCount('media') > $maxMedia)
		{
			$this->errors[] = \XF::phraseDeferred(
				'please_enter_message_with_no_more_than_x_media',
				['count' => $maxMedia]
			);
		}

		if (!$this->constraints['allowEmpty'])
		{
			$rendered = $this->app->bbCode()->render(
				$message, 'simpleHtml', $this->context . ':prepare', $this->messageEntity
			);
			if (!strlen(trim($rendered)))
			{
				$this->errors[] = \XF::phraseDeferred('please_enter_valid_message');
			}
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