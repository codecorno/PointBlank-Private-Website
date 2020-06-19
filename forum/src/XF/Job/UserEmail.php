<?php

namespace XF\Job;

class UserEmail extends AbstractUserCriteriaJob
{
	protected $defaultData = [
		'email' => []
	];

	protected function executeAction(\XF\Entity\User $user)
	{
		if (!$user->email)
		{
			return;
		}

		$options = $this->app->options();
		$language = $this->app->language($user->language_id);
		$email = $this->data['email'];

		$email = array_replace([
			'from_name' => $options->emailSenderName ? $options->emailSenderName : $options->boardTitle,
			'from_email' => $options->defaultEmailAddress,
			'email_body' => '',
			'email_title' => '',
			'email_format' => 'text',
			'email_wrapped' => true,
			'email_unsub' => false,
		], $email);

		$email['email_body'] = $this->replacePhrases($email['email_body'], $language);
		$email['email_title'] = $this->replacePhrases($email['email_title'], $language);

		if ($email['email_format'] == 'html')
		{
			if ($email['email_unsub'])
			{
				$email['email_body'] .= "\n\n<div class=\"minorText\" align=\"center\"><a href=\"{unsub}\">"
					. $language->renderPhrase('unsubscribe_from_mailing_list')
					. '</a></div>';
			}

			$tokens = $this->prepareTokens($user, true);
			$html = strtr($email['email_body'], $tokens);
			$text = $this->app->mailer()->generateTextBody($html);
		}
		else
		{
			if ($email['email_unsub'])
			{
				$email['email_body'] .= "\n\n"
					. $language->renderPhrase('unsubscribe_from_mailing_list:')
					. ' {unsub}';
			}

			$tokens = $this->prepareTokens($user, false);
			$text = strtr($email['email_body'], $tokens);
			$html = null;
		}

		$titleTokens = $this->prepareTokens($user, false);
		$title = strtr($email['email_title'], $titleTokens);

		$mail = $this->getMail($user)->setFrom($email['from_email'], $email['from_name']);
		$mail->setTemplate('prepared_email', [
			'title' => $title,
			'htmlBody' => $html,
			'textBody' => $text,
			'raw' => $email['email_wrapped'] ? false : true
		]);
		$mail->send();
	}

	protected function getActionDescription()
	{
		$actionPhrase = \XF::phrase('emailing');
		$typePhrase = \XF::phrase('users');

		return sprintf('%s... %s', $actionPhrase, $typePhrase);
	}

	protected function wrapTransaction()
	{
		return false;
	}

	protected function prepareTokens(\XF\Entity\User $user, $escape)
	{
		$unsubLink = $this->app->router('public')->buildLink('canonical:email-stop/mailing-list', $user);

		$tokens = [
			'{name}' => $user->username,
			'{email}' => $user->email,
			'{id}' => $user->user_id,
			'{unsub}' => $unsubLink
		];

		if ($escape)
		{
			array_walk($tokens, function(&$value)
			{
				if (is_string($value))
				{
					$value = htmlspecialchars($value);
				}
			});
		}

		return $tokens;
	}

	protected function replacePhrases($string, \XF\Language $language)
	{
		return $this->app->stringFormatter()->replacePhrasePlaceholders($string, $language);
	}

	/**
	 * @param User $user
	 *
	 * @return \XF\Mail\Mail
	 */
	protected function getMail(\XF\Entity\User $user)
	{
		$mailer = $this->app->mailer();
		$mail = $mailer->newMail();

		$options = $this->app->options();
		$unsubEmail = $options->unsubscribeEmailAddress;
		$useVerp = $options->enableVerp;

		$mail->setToUser($user);

		return $mail->setListUnsubscribe($unsubEmail, $useVerp);
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}