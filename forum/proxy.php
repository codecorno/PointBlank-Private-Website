<?php

$dir = __DIR__;
require ($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');

$proxy = $app->proxy()->controller();

$request = $app->request();
$input = $request->filter([
	'image' => 'str',
	'link' => 'str',
	'hash' => 'str',
	'_xfResponseType' => 'str',
	'referrer' => 'str',
	'return_error' => 'bool'
]);

if ($input['image'])
{
	$recursed = $proxy->resolveImageProxyRecursion($request, $input['image']);
	if ($recursed)
	{
		$input['image'] = $recursed[0];
		$input['hash'] = $recursed[1];
	}

	if ($input['return_error'])
	{
		$proxy->setReturnError(true);
	}
	$response = $proxy->outputImage($input['image'], $input['hash']);
	$response->send($request);
}
else if ($input['link'])
{
	if ($input['referrer'])
	{
		$proxy->setReferrer($input['referrer']);
	}
	$response = $proxy->outputLink($input['link'], $input['hash']);
	$response->send($request);
}
else
{
	header('Content-type: text/plain; charset=utf-8', true, 400);
	echo "Unknown type";
}