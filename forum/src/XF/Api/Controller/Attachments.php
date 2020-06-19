<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Attachments
 */
class Attachments extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('attachment');
	}

	/**
	 * @api-desc Gets the attachments associated with the provided API attachment key. Only returns attachments that have not been associated with content.
	 *
	 * @api-in <req> str $key The API attachment key
	 *
	 * @api-out Attachment[] $attachments List of matching attachments.
	 */
	public function actionGet()
	{
		$this->assertRequiredApiInput('key');

		$keyValue = $this->filter('key', 'str');
		$key = $this->assertRecordExists('XF:ApiAttachmentKey', $keyValue);

		if (!$key->temp_hash)
		{
			return $this->noPermission();
		}

		$attachments = $this->getAttachmentRepo()->findAttachmentsByTempHash($key->temp_hash)->fetch();

		return $this->apiResult([
			'attachments' => $attachments->toApiResults()
		]);
	}

	/**
	 * @api-desc Uploads an attachment. An API attachment key must be created first. Must be submitted using multipart/form-data encoding.
	 *
	 * @api-in <req> str $key The API attachment key to associated with
	 * @api-in <req> file $attachment The attachment file
	 *
	 * @api-out Attachment $attachment The attachment record of the successful upload
	 *
	 * @api-error attachment_key_user_wrong Triggered if the user making the request does not match the user that created the attachment key.
	 */
	public function actionPost()
	{
		$this->assertRequiredApiInput('key');

		$keyValue = $this->filter('key', 'str');
		$key = $this->assertRecordExists('XF:ApiAttachmentKey', $keyValue);

		$upload = $this->request->getFile('attachment', false, false);
		if (!$upload)
		{
			return $this->requiredInputMissing('attachment');
		}

		$handler = $this->getAttachmentRepo()->getAttachmentHandler($key->content_type);
		if (!$handler)
		{
			return $this->notFound();
		}

		if (\XF::isApiCheckingPermissions() && !$handler->canManageAttachments($key->context, $error))
		{
			return $this->noPermission($error);
		}

		if ($key->user_id !== \XF::visitor()->user_id)
		{
			return $this->apiError(
				\XF::phrase('api_error.attachment_key_user_wrong'),
				'attachment_key_user_wrong',
				['expected' => \XF::visitor()->user_id]
			);
		}

		$attachment = $this->uploadFile($upload, $handler, $key->context, $key->temp_hash);

		return $this->apiResult([
			'attachment' => $attachment->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @api-desc Creates a new attachment key, allowing attachments to be uploaded separately from the related content.
	 *
	 * @api-in <req> str $type The content type of the attachment. Default types include post, conversation_message. Add-ons may add more.
	 * @api-in str[] $context Key-value pairs representing the context of the attachment. This will vary depending on content type and the action being taken. See relevant actions for further details.
	 * @api-in file $attachment The first attachment to be associated with the new key.
	 *
	 * @api-out str $key The attachment key created. This should be used to upload additional files or to associate uploaded attachments with other content.
	 * @api-out Attachment $attachment If a file was provided and the upload was successful, this will describe the new attachment.
	 */
	public function actionPostNewKey()
	{
		$this->assertRequiredApiInput(['type']);

		$type = $this->filter('type', 'str');
		$handler = $this->getAttachmentRepo()->getAttachmentHandler($type);
		if (!$handler)
		{
			return $this->notFound();
		}

		$context = $this->filter('context', 'array-str');
		if (\XF::isApiCheckingPermissions() && !$handler->canManageAttachments($context, $error))
		{
			if (!$context)
			{
				return $this->requiredInputMissing('context');
			}

			return $this->noPermission($error);
		}

		$key = $this->em()->create('XF:ApiAttachmentKey');
		$key->content_type = $type;
		$key->context = $context;
		$key->preSave();

		$upload = $this->request->getFile('attachment', false, false);
		$attachment = $upload ? $this->uploadFile($upload, $handler, $context, $key->temp_hash) : null;

		$key->save();

		$result = [
			'key' => $key->attachment_key
		];
		if ($attachment)
		{
			$result['attachment'] = $attachment->toApiResult(Entity::VERBOSITY_VERBOSE);
		}

		return $this->apiResult($result);
	}

	protected function uploadFile(
		\XF\Http\Upload $upload, \XF\Attachment\AbstractHandler $handler, array $context, $tempHash
	)
	{
		/** @var \XF\Api\ControllerPlugin\Attachment $plugin */
		$plugin = $this->plugin('XF:Api:Attachment');
		return $plugin->uploadFile($upload, $handler, $context, $tempHash);
	}

	/**
	 * @return \XF\Repository\Attachment
	 */
	protected function getAttachmentRepo()
	{
		return $this->repository('XF:Attachment');
	}
}