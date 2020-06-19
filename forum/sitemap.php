<?php

$dir = __DIR__;
require ($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');

/** @var \XF\Sitemap\Renderer $renderer */
$renderer = $app['sitemap.renderer'];
$request = $app->request();
$response = $app->response();
$counter = $request->filter('c', 'uint');

$response = $renderer->outputSitemap($response, $counter);
$response->send($request);