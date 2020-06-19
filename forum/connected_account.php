<?php

$dir = __DIR__;
require ($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');
$session = $app->session();

$response = $app->response();
$request = $app->request();
$provider = null;

$connectedAccountRequest = $session->get('connectedAccountRequest');

if (!is_array($connectedAccountRequest) || !isset($connectedAccountRequest['provider']))
{
	$message = \XF::phrase('there_is_no_valid_connected_account_request_available');
	$response->httpCode(404);
}
else
{
	$provider = $app->em()->find('XF:ConnectedAccountProvider', $connectedAccountRequest['provider']);
	if (!$provider)
	{
		$message = \XF::phrase('connected_account_provider_specified_cannot_be_found');
		$response->httpCode(404);

		$session->remove('connectedAccountRequest');
		$session->save();
	}
}

if ($response->httpCode() !== 200)
{
	$response
		->body($message)
		->contentType('text/plain')
		->send($request);

	exit;
}

$visitor = \XF::visitor();

if ($provider->isAssociated($visitor))
{
	$response
		->redirect($connectedAccountRequest['returnUrl'])
		->send($request);

	exit;
}

$handler = $provider->getHandler();
$storageState = $handler->getStorageState($provider, $visitor);

// If we're in test mode, we'll bypass getting the existing token from the session.
if (!$token = $handler->requestProviderToken($storageState, $request, $error, $connectedAccountRequest['test']))
{
	$response
		->body($error)
		->contentType('text/plain')
		->send($request);

	exit;
}

$connectedAccountRequest['tokenStored'] = true;
$session->set('connectedAccountRequest', $connectedAccountRequest);
$session->save();

if ($connectedAccountRequest['test'])
{
	$redirect = $app->router('admin')->buildLink('connected-accounts/perform-test', $provider);
}
else
{
	$redirect = $app->router('public')->buildLink('register/connected-accounts', $provider);
}

$response->redirect($redirect, 302);
$response->send($request);