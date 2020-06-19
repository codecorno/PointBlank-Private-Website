<?php

namespace XF\ConnectedAccount\Http;

use OAuth\Common\Http\Client\AbstractClient;
use OAuth\Common\Http\Uri\UriInterface;

class Client extends AbstractClient
{
	/**
	 * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
	 * They should return, in string form, the response body and throw an exception on error.
	 *
	 * @param UriInterface $endpoint
	 * @param mixed        $requestBody
	 * @param array        $extraHeaders
	 * @param string       $method
	 *
	 * @return string
	 *
	 * @throws HttpResponseException
	 * @throws \InvalidArgumentException
	 */
	public function retrieveResponse(UriInterface $endpoint, $requestBody, array $extraHeaders = [], $method = 'POST')
	{
		$method = strtoupper($method);

		if ($method === 'GET' && !empty($requestBody))
		{
			throw new \InvalidArgumentException('No body expected for "GET" request.');
		}

		$extraHeaders['Host'] = $endpoint->getHost();
		$extraHeaders['Connection'] = 'close';
		$extraHeaders['User-Agent'] = $this->userAgent;

		$client = \XF::app()->http()->client();

		$requestBodyField = 'body';
		if ($method === 'POST' || $method === 'PUT')
		{
			if (is_array($requestBody))
			{
				$requestBodyField = 'form_params';
			}
		}
		$extraHeaders['Content-length'] = ($requestBody && is_string($requestBody)) ? strlen($requestBody) : 0;

		$response = $client->request($method, $endpoint->getAbsoluteUri(), [
			'allow_redirects' => [
				'max' => $this->maxRedirects
			],
			$requestBodyField => $requestBody,
			'headers' => $extraHeaders,
			'timeout' => $this->timeout,
			'exceptions' => false
		]);

		$body = $response->getBody();
		$content = $body ? $body->getContents() : '';

		$code = $response->getStatusCode();
		if ($code >= 400)
		{
			$exception = new HttpResponseException("Failed to request resource. HTTP Code: $code", $code);
			$exception->setResponseContent($content);
			throw $exception;
		}

		if (!$body)
		{
			$exception = new HttpResponseException("Failed to request resource. No body.", $code);
			$exception->setResponseContent($content);
			throw $exception;
		}

		return $content;
	}
}