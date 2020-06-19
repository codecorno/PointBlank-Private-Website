<?php

namespace XF\Report;

use XF\Entity\Report;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	public function canView(Report $report)
	{
		if (!$this->canViewContent($report))
		{
			return false;
		}

		if (!$this->canActionContent($report))
		{
			return false;
		}

		return true;
	}

	protected function canViewContent(Report $report)
	{
		return true;
	}

	protected function canActionContent(Report $report)
	{
		return true;
	}

	abstract public function setupReportEntityContent(Report $report, Entity $content);
	abstract public function getContentTitle(Report $report);
	abstract public function getContentMessage(Report $report);

	public function getContentLink(Report $report)
	{
		return '';
	}

	public function getContentForThreadReport(Report $report, $message)
	{
		$visitor = \XF::visitor();

		return [
			'link' => $this->getContentLink($report),
			'title' => $this->getContentTitle($report),
			'userLink' => $report->User ? \XF::app()->router('public')->buildLink('canonical:members', $report->User) : null,
			'username' => $report->User ? $report->User->username : \XF::phrase('guest'),
			'reporterLink' => \XF::app()->router('public')->buildLink('canonical:members', $visitor),
			'reporter' => $visitor->username,
			'reportReason' => $message,
			'message' => $this->getContentMessage($report),
			'extraDetails' => ''
		];
	}

	public function getTemplateName()
	{
		return 'public:report_content_' . $this->contentType;
	}

	public function getTemplateData(Report $report)
	{
		return [
			'report' => $report,
			'content' => $report->Content
		];
	}

	public function render(Report $report)
	{
		$template = $this->getTemplateName();
		if (!$template)
		{
			return '';
		}
		return \XF::app()->templater()->renderTemplate($template, $this->getTemplateData($report));
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}
}