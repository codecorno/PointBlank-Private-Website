<?php

$dir = __DIR__;
require ($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App', [
	'preLoad' => ['masterStyleModifiedDate', 'smilieSprites']
]);

$request = $app->request();
$input = $request->filter([
	'css' => 'str',
	's' => 'uint',
	'l' => 'uint',
	'k' => 'str'
]);

$cssWriter = $app->cssWriter();

$showDebugOutput = (\XF::$debugMode && $request->get('_debug'));

if (!$showDebugOutput && $cssWriter->canSend304($request))
{
	$cssWriter->get304Response()->send($request);
}
else
{
	$css = $input['css'] ? explode(',', $input['css']) : [];
	$response = $cssWriter->run($css, $input['s'], $input['l'], $input['k']);
	if ($showDebugOutput)
	{
		$response->contentType('text/html', 'utf-8');
		$response->body($app->debugger()->getDebugPageHtml($app));
	}
	$response->send($request);
}