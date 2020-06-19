<?php

$dir = __DIR__ . '/..';
require ($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');

$request = $app->request();

$addOnId = $request->filter('addon_id', 'str');
$jsPath = $request->filter('js', 'str');

$jsResponse = $app->developmentJsResponse();
$response = $jsResponse->run($jsPath, $addOnId);

$response->send($request);