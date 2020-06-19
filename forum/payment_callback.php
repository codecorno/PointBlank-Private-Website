<?php

$dir = __DIR__;
require ($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');

$response = $app->response();
$response->contentType('text/plain');
$request = $app->request();

$legacy = false;
if (!$providerId = $request->filter('_xfProvider', 'str'))
{
	$providerId = 'paypal';
	$legacy = true;
}

/** @var \XF\Entity\PaymentProvider $provider */
$provider = $app->em()->find('XF:PaymentProvider', $providerId);

if (!$provider)
{
	$response->httpCode(404)
		->body('Unknown payment provider.')
		->send($request);

	return;
}

$handler = $provider->handler;
$state = $handler->setupCallback($request);
$state->legacy = $legacy;

if (!$handler->validateCallback($state))
{
	$response->httpCode($state->httpCode ?: 403);
}
else if (!$handler->validateTransaction($state))
{
	// We generally don't need these to retry, so send a successful response.
	$response->httpCode($state->httpCode ?: 200);
}
else if (!$handler->validatePurchaseRequest($state)
	|| !$handler->validatePurchasableHandler($state)
	|| !$handler->validatePaymentProfile($state)
	|| !$handler->validatePurchaser($state)
)
{
	$response->httpCode($state->httpCode ?: 404);
}
else if (!$handler->validatePurchasableData($state)
	|| !$handler->validateCost($state)
)
{
	$response->httpCode($state->httpCode ?: 403);
}
else
{
	$handler->setProviderMetadata($state);
	$handler->getPaymentResult($state);
	$handler->completeTransaction($state);
}

if ($state->logType)
{
	try
	{
		$handler->log($state);
	}
	catch (\Exception $e)
	{
		\XF::logException($e, false, "Error logging payment to payment provider: ");
	}
}

$response
	->body(htmlspecialchars($state->logMessage))
	->send($request);