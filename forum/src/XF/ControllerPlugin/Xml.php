<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Finder;

class Xml extends AbstractPlugin
{
	public function actionExport(Finder $finder, $serviceClass, $viewClass = null)
	{
		if ($token = $this->filter('t', 'str'))
		{
			$this->assertValidCsrfToken($token);
		}
		else
		{
			$this->assertPostOnly();
		}

		$exportService = $this->service($serviceClass);
		if (!($exportService instanceof \XF\Service\AbstractXmlExport))
		{
			throw $this->exception($this->message(\XF::phrase('class_x_must_extend_abstract_xml_export_class', ['exportService' => get_class($exportService)])));
		}
		$xml = $exportService->export($finder);

		$this->setResponseType('xml');

		$viewParams = [
			'xml' => $xml
		];
		return $this->view($viewClass ?: $serviceClass, '', $viewParams);
	}

	public function actionImport($urlPrefix, $rootName, $serviceClass, $viewClass = null)
	{
		if ($this->isPost())
		{
			$upload = $this->request->getFile('upload', false);
			if (!$upload)
			{
				return $this->error(\XF::phrase('please_provide_valid_xml_file'));
			}

			try
			{
				$xml = \XF\Util\Xml::openFile($upload->getTempFile());
			}
			catch (\Exception $e)
			{
				$xml = null;
			}

			if (!$xml || $xml->getName() != $rootName)
			{
				return $this->error(\XF::phrase('please_provide_valid_x_xml_file', ['rootName' => $rootName]));
			}

			$importService = $this->service($serviceClass);
			if (!($importService instanceof \XF\Service\AbstractXmlImport))
			{
				throw $this->exception($this->message(\XF::phrase('class_x_must_extend_abstract_xml_import_class', ['importService' => get_class($importService)])));
			}
			$importService->import($xml);

			return $this->redirect($this->buildLink($urlPrefix));
		}
		else
		{
			$viewParams = [
				'urlPrefix' => $urlPrefix
			];
			return $this->view($serviceClass ?: $viewClass, 'xml_import', $viewParams);
		}
	}
}