<?php

namespace XF\Mvc\Renderer;

use XF\Mvc\Reply\AbstractReply;

class Xml extends AbstractRenderer
{
	protected function initialize()
	{
		$this->response->contentType('application/xml');
	}

	public function getResponseType()
	{
		return 'xml';
	}

	public function renderRedirect($url, $type, $message = '')
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		$rootNode = $document->createElement('response');
		\XF\Util\Xml::createDomElements($rootNode, [
			'status' => $type,
			'url' => $url,
			'message' => (!$message ? \XF::phrase('redirect_changes_saved_successfully') : $message)
		]);
		$document->appendChild($rootNode);

		return $document->saveXML();
	}

	public function renderMessage($message)
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		$rootNode = $document->createElement('response');
		$rootNode->appendChild($document->createElement('status', 'ok'));
		$messageNode = $document->createElement('message');
		$messageNode->appendChild($document->createCDATASection($message));
		$rootNode->appendChild($messageNode);
		$document->appendChild($rootNode);

		return $document->saveXML();
	}

	public function renderErrors(array $errors)
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		$rootNode = $document->createElement('errors');
		$document->appendChild($rootNode);

		foreach ($errors AS $errorMessage)
		{
			$errorNode = $rootNode->appendChild($document->createElement('error'));
			$errorNode->appendChild($document->createCDATASection($errorMessage));
		}

		return $document->saveXML();
	}

	public function renderView($viewName, $templateName, array $params = [])
	{
		if (isset($params['innerContent']))
		{
			return $params['innerContent'];
		}

		$output = $this->renderViewObject($viewName, $templateName, $params);
		if ($output === null && $templateName)
		{
			$output = $this->renderErrors([\XF::phrase('requested_page_cannot_be_represented_in_this_format')]);
		}
		return $output;
	}
}