<?php

namespace XF\Service\User;

use XF\Entity\User;

class SignatureEdit extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	protected $perms = [
		'basic' => true,
		'extended' => true,
		'align' => true,
		'link' => true,
		'image' => true,
		'media' => true,
		'block' => true,
		'list' => true,
		'smilie' => true,

		'maxPrintable' => -1,
		'maxLines' => -1,
		'maxLinks' => -1,
		'maxImages' => -1,
		'maxSmilies' => -1,
		'maxTextSize' => -1
	];

	protected $permTagMap = [
		'basic' => ['b', 'i', 'u', 's'],
		'extended' => ['color', 'font', 'size', 'icode'],
		'align' => ['left', 'center', 'right', 'indent'],
		'link' => ['url', 'email', 'user'],
		'image' => ['img', 'attach'],
		'media' => ['media'],
		'block' => ['code', 'php', 'html', 'quote', 'spoiler', 'table', 'tr', 'th', 'td'],
		'list' => ['list']
	];

	protected $signature;

	protected $skipSpamCheck = false;
	protected $allowAutoLink = true;

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);
		$this->setUser($user);
	}

	public function setUser(User $user)
	{
		$this->user = $user;
		$this->applyPermissions();
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	protected function applyPermissions()
	{
		$user = $this->user;

		$this->perms['basic'] = $user->hasPermission('signature', 'basicText');
		$this->perms['extended'] = $user->hasPermission('signature', 'extendedText');
		$this->perms['align'] = $user->hasPermission('signature', 'align');
		$this->perms['link'] = $user->hasPermission('signature', 'link');
		$this->perms['image'] = $user->hasPermission('signature', 'image');
		$this->perms['media'] = $user->hasPermission('signature', 'media');
		$this->perms['block'] = $user->hasPermission('signature', 'block');
		$this->perms['list'] = $user->hasPermission('signature', 'list');

		$this->perms['maxPrintable'] = $user->hasPermission('signature', 'maxPrintable');
		$this->perms['maxLines'] = $user->hasPermission('signature', 'maxLines');
		$this->perms['maxTextSize'] = $user->hasPermission('signature', 'maxTextSize');

		$this->perms['maxLinks'] = $user->hasPermission('signature', 'maxLinks');
		if (!$this->perms['maxLinks'])
		{
			$this->perms['link'] = false;
		}

		$this->perms['maxImages'] = $user->hasPermission('signature', 'maxImages');
		if (!$this->perms['maxImages'])
		{
			$this->perms['image'] = false;
		}

		$this->perms['maxSmilies'] = $user->hasPermission('signature', 'maxSmilies');
		$this->perms['smilie'] = $this->perms['maxSmilies'] != 0;
	}

	public function getDisabledEditorButtons()
	{
		$disabled = [];

		if (!$this->perms['basic'])
		{
			$disabled[] = '_basic';
		}
		if (!$this->perms['extended'])
		{
			$disabled[] = '_extended';
		}
		if (!$this->perms['link'])
		{
			$disabled[] = '_link';
		}
		if (!$this->perms['align'])
		{
			$disabled[] = '_align';
			$disabled[] = '_indent';
		}
		if (!$this->perms['list'])
		{
			$disabled[] = '_list';
			$disabled[] = '_indent';
		}
		if (!$this->perms['smilie'])
		{
			$disabled[] = '_smilies';
		}
		if (!$this->perms['image'])
		{
			$disabled[] = '_image';
		}
		if (!$this->perms['media'])
		{
			$disabled[] = '_media';
		}
		if (!$this->perms['block'])
		{
			$disabled[] = '_block';
		}

		foreach ($this->app->bbCode()->get('custom') AS $tag => $info)
		{
			if (!$info['allow_signature'])
			{
				// make sure this matches with the name in editor.js
				$disabled[] = 'xfCustom_' . $tag;
			}
		}

		return $disabled;
	}

	public function setSkipSpamCheck($skip)
	{
		$this->skipSpamCheck = (bool)$skip;

		return $this;
	}

	public function setSignature($signature, &$errors = [])
	{
		$signature = trim($signature);
		if ($signature === '')
		{
			$this->signature = $signature;
			return true;
		}

		$bbCodeContainer = $this->app->bbCode();
		$parser = $bbCodeContainer->parser();
		$rules = $bbCodeContainer->rules('user:signature');

		$processor = $bbCodeContainer->processor();
		$usage = $bbCodeContainer->processorAction('usage');

		/** @var \XF\BbCode\ProcessorAction\LimitTags $limit */
		$limit = $bbCodeContainer->processorAction('limit');

		$this->setupBbCodeLimits($limit);

		$processor->addProcessorAction('usage', $usage)
			->addProcessorAction('limit', $limit);

		if ($this->allowAutoLink && $this->perms['link'])
		{
			/** @var \XF\BbCode\ProcessorAction\AutoLink $autoLinker */
			$autoLinker = $bbCodeContainer->processorAction('autolink');
			$autoLinker->enableUnfurling(false);

			$processor->addProcessorAction('autolink', $autoLinker);
		}

		$renderOptions = [
			'user' => $this->user
		];

		$signature = $processor->render($signature, $parser, $rules, $renderOptions);

		if ($limit->hasDisabledTags())
		{
			$limit->setStripDisabled(false);
			$signature = $processor->render($signature, $parser, $rules, $renderOptions);
		}

		if (!$this->validateSignature($signature, $processor, $errors))
		{
			$this->signature = null;
			return false;
		}

		if (!$this->skipSpamCheck && $this->user->isSpamCheckRequired())
		{
			$checker = $this->app->spam()->contentChecker();
			$checker->check($this->user, $signature, [
				'content_type' => 'user_signature'
			]);

			switch ($checker->getFinalDecision())
			{
				case 'moderated':
				case 'denied':
					$checker->logSpamTrigger('user_signature', $this->user->user_id);
					$errors[] = \XF::phrase('your_content_cannot_be_submitted_try_later');

					$this->signature = null;
					return false;
			}
		}

		$this->signature = $signature;

		return true;
	}

	public function getNewSignature()
	{
		return $this->signature;
	}

	public function save()
	{
		$profile = $this->user->Profile;
		if (!$profile)
		{
			throw new \LogicException("User profile record missing");
		}

		if ($this->signature === null)
		{
			return false;
		}

		$profile->signature = $this->signature;
		$profile->save();

		return true;

	}

	protected function setupBbCodeLimits(\XF\BbCode\ProcessorAction\LimitTags $limit)
	{
		foreach ($this->permTagMap AS $perm => $disabledTags)
		{
			if (!$this->perms[$perm])
			{
				$limit->disableTag($disabledTags);
			}
		}

		foreach ($this->app->bbCode()->get('custom') AS $tag => $info)
		{
			if (!$info['allow_signature'])
			{
				$limit->disableTag($tag);
			}
		}

		$limit->setMaxTextSize($this->perms['maxTextSize']);
	}

	protected function validateSignature($signature, \XF\BbCode\Processor $processor, &$errors = [])
	{
		$errors = [];

		/** @var \XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
		$usage = $processor->getAnalyzer('usage');

		$maxPrintable = $this->perms['maxPrintable'];
		if ($maxPrintable != -1 && $usage->getPrintableLength() > $maxPrintable)
		{
			$diff = $usage->getPrintableLength() - $maxPrintable;
			$errors[] = \XF::phraseDeferred('your_signature_is_x_characters_too_long', ['count' => $diff]);
		}

		$maxLines = $this->perms['maxLines'];
		if ($maxLines != -1)
		{
			$bbCodeContainer = $this->app->bbCode();
			$parser = $bbCodeContainer->parser();
			$rules = $bbCodeContainer->rules('user:signature');
			/** @var \XF\BbCode\Renderer\SimpleHtml $renderer */
			$renderer = $bbCodeContainer->renderer('simpleHtml');

			$options = $bbCodeContainer->getFullRenderOptions(
				$this->user, 'user:signature', 'simpleHtml', ['allowUnfurl' => false]
			);

			$lines = $renderer->countLines($signature, $parser, $rules, $options);
			if ($lines > $maxLines)
			{
				$diff = $lines - $maxLines;
				$errors[] = \XF::phraseDeferred('your_signature_is_x_liness_too_long', ['count' => $diff]);
			}
		}

		$maxLinks = $this->perms['maxLinks'];
		$linkCount = $usage->getTagCount('url') + $usage->getTagCount('email');
		if ($maxLinks != -1 && $linkCount > $maxLinks)
		{
			$errors[] = \XF::phraseDeferred('your_signature_may_only_have_x_links', ['count' => $maxLinks]);
		}

		$maxImages = $this->perms['maxImages'];
		if ($maxImages != -1 && $usage->getTagCount('img') > $maxImages)
		{
			$errors[] = \XF::phraseDeferred('your_signature_may_only_have_x_images', ['count' => $maxImages]);
		}

		$maxSmilies = $this->perms['maxSmilies'];
		if ($maxSmilies != -1 && $usage->getSmilieCount() > $maxSmilies)
		{
			$errors[] = \XF::phraseDeferred('your_signature_may_only_have_x_smilies', ['count' => $maxSmilies]);
		}

		/** @var \XF\BbCode\ProcessorAction\LimitTags $limit */
		$limit = $processor->getFilterer('limit');
		if ($limit->hasDisabledTags())
		{
			$errors[] = \XF::phraseDeferred('your_signature_may_not_contain_disabled_tags');
		}

		return count($errors) == 0;
	}
}