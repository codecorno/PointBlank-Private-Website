<?php

namespace XF\Api\Docs\Renderer;

use XF\Api\Docs\Annotation\AbstractBlock;
use XF\Api\Docs\Annotation\AbstractValueLine;
use XF\Api\Docs\Annotation\RouteBlock;
use XF\Api\Docs\Annotation\TypeBlock;

class Xf2Html implements RendererInterface
{
	use FileRendererTrait;

	protected $types = [];

	public function renderInternal(array $routeGroupings, array $types)
	{
		$this->types = $types;

		$html = [];

		$html[] = '<xf:sidenav>';
		$html[] = $this->renderTableOfContents($routeGroupings, $types);
		$html[] = '</xf:sidenav>';
		$html[] = '';

		foreach ($routeGroupings AS $group => $routes)
		{
			$html[] = $this->renderRouteGroup($group, $routes);
		}

		foreach ($types AS $type)
		{
			$html[] = $this->renderType($type);
		}

		$this->types = [];

		return implode("\n", $html);
	}

	public function renderTableOfContents(array $routeGroupings, array $types)
	{
		$routesHtml = [];
		foreach ($routeGroupings AS $group => $routes)
		{
			$routesHtml[] = '<h3 class="block-minorHeader">'
				. '<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">'
					. '<span class="block-formSectionHeader-aligner">' . htmlspecialchars($group) . '</span>'
				. '</span>'
				. '</h3><div class="block-body block-body--collapsible">';

			foreach ($routes AS $route)
			{
				/** @var RouteBlock $route */
				$routeId = $this->getRouteId($route);
				$routesHtml[] = '<a href="#' . htmlspecialchars($routeId) . '" class="blockLink u-smaller">'
					. $this->getRouteNameHtml($route)
					. '</a>';
			}

			$routesHtml[] = '</div>';
		}

		$routesHtml = implode("\n", $routesHtml);

		$typesHtml = [];
		foreach ($types AS $type)
		{
			$typeId = $this->getTypeId($type);
			$typesHtml[] = '<a href="#' . htmlspecialchars($typeId) . '" class="blockLink u-smaller">'
				. htmlspecialchars($type->type)
				. '</a>';
		}

		$typesHtml = implode("\n", $typesHtml);

		return <<< FULLHTML
<div class="block">
	<div class="block-container">
		<h2 class="block-header">Routes</h2>
		{$routesHtml}
	</div>
</div>

<div class="block">
	<div class="block-container">
		<h2 class="block-header">Data types</h2>
		<div class="block-body">
			{$typesHtml}
		</div>
	</div>
</div>
FULLHTML;
	}

	public function renderRouteGroup($group, array $routes)
	{
		$html = [];
		foreach ($routes AS $route)
		{
			$html[] = $this->renderRoute($route);
			$html[] = '';
		}

		$html[] = '';

		return implode("\n", $html);
	}

	public function renderRoute(RouteBlock $route)
	{
		if ($route->inputs)
		{
			$inputRowsHtml = [];

			foreach ($route->inputs AS $input)
			{
				$inputRowsHtml[] = $this->renderValueRow($input);
			}

			$inputRowsHtml = implode("\n", $inputRowsHtml);
		}
		else
		{
			$inputRowsHtml = $this->renderEmptyValuesPlaceholder($route);
		}
		if ($route->outputs)
		{
			$outputRowsHtml = [];

			foreach ($route->outputs AS $input)
			{
				$outputRowsHtml[] = $this->renderValueRow($input);
			}

			$outputRowsHtml = implode("\n", $outputRowsHtml);
		}
		else
		{
			$outputRowsHtml = $this->renderEmptyValuesPlaceholder($route);
		}

		if ($route->errors)
		{
			$errorRowsHtml = [];
			foreach ($route->errors AS $errorKey => $description)
			{
				$errorRowsHtml[] = "\t<xf:datarow>"
					. '<xf:cell>' . htmlspecialchars($errorKey) . '</xf:cell>'
					. '<xf:cell>' . htmlspecialchars($description) . '</xf:cell>'
					. '</xf:datarow>';
			}

			$errorRowsHtml = implode("\n", $errorRowsHtml);

			$errorHtml = <<< FULLHTML
	<div class="block">
		<div class="block-container">
			<h4 class="block-header">Errors</h4>
			<div class="block-body">
				<xf:datalist>
					<xf:datarow rowtype="header">
						<xf:cell>Error key</xf:cell>
						<xf:cell>Description</xf:cell>
					</xf:datarow>
					{$errorRowsHtml}
				</xf:datalist>
			</div>
		</div>
	</div>
FULLHTML;
		}
		else
		{
			$errorHtml = '';
		}

		$routeIdHtml = htmlspecialchars($this->getRouteId($route));
		$routeHeaderHtml = $this->getRouteNameHtml($route);

		$descHtml = htmlspecialchars($route->description)
			. ($route->incomplete ? ' [Incomplete]' : '');
		$routeDescHtml = $descHtml ? "<div class=\"blocks-desc\">{$descHtml}</div>" : '';

		return <<< FULLHTML
<div class="blocks blocks--close blocks--separated">
	<h3 class="blocks-header blocks-header--strong">
		<span class="u-anchorTarget" id="{$routeIdHtml}"></span>
		{$routeHeaderHtml}
		{$routeDescHtml}
	</h3>
	<div class="block">
		<div class="block-container">
			<h4 class="block-header">Parameters</h4>
			<div class="block-body">
				<xf:datalist>
					<xf:datarow rowtype="header">
						<xf:cell>Input</xf:cell>
						<xf:cell>Type</xf:cell>
						<xf:cell>Description</xf:cell>
					</xf:datarow>
					{$inputRowsHtml}
				</xf:datalist>
			</div>
		</div>
	</div>	
	<div class="block">
		<div class="block-container">		
			<h4 class="block-header">Response</h4>
			<div class="block-body">
				<xf:datalist>
					<xf:datarow rowtype="header">
						<xf:cell>Output</xf:cell>
						<xf:cell>Type</xf:cell>
						<xf:cell>Description</xf:cell>
					</xf:datarow>
					{$outputRowsHtml}
				</xf:datalist>
			</div>
		</div>
	</div>
			
	$errorHtml
</div>
FULLHTML;

		return implode("\n", $html);
	}

	protected function getRouteId(RouteBlock $route)
	{
		$routeId = preg_replace('#[{}]#', '', $route->route);
		$routeId = preg_replace('#[/\-]#', '_', $routeId);
		$routeId = preg_replace('#[^a-z0-9_]#i', '', $routeId);

		return strtolower('route_' . $route->method . '_' . $routeId);
	}

	protected function getRouteNameHtml(RouteBlock $route)
	{
		switch ($route->method)
		{
			case 'GET':
				$method = '<span class="label label--lightGreen">GET</span>';
				break;

			case 'POST':
				$method = '<span class="label label--skyBlue">POST</span>';
				break;

			case 'DELETE':
				$method = '<span class="label label--orange">DELETE</span>';
				break;

			default:
				$method = htmlspecialchars($route->method);
		}

		$name = htmlspecialchars($route->route);

		return $method . ' ' . $name;
	}

	public function renderType(TypeBlock $type)
	{
		if ($type->structure)
		{
			$dataRowHtml = [];

			foreach ($type->structure AS $element)
			{
				$dataRowHtml[] = $this->renderValueRow($element);
			}

			$dataRowHtml = implode("\n", $dataRowHtml);
		}
		else
		{
			$dataRowHtml = $this->renderEmptyValuesPlaceholder($type);
		}

		$typeIdHtml = htmlspecialchars($this->getTypeId($type));
		$typeHeaderHtml = htmlspecialchars($type->type);

		$descHtml = htmlspecialchars($type->description)
			. ($type->incomplete ? ' [Incomplete]' : '');
		$typeDescHtml = $descHtml ? "<div class=\"block-desc\">{$descHtml}</div>" : '';

		return <<< FULLHTML
<div class="block">
	<div class="block-container">
		<h3 class="block-header">
			<span class="u-anchorTarget" id="{$typeIdHtml}"></span>
			Data type: {$typeHeaderHtml}
			{$typeDescHtml}
		</h3>
		<div class="block-body">
			<xf:datalist>
				<xf:datarow rowtype="header">
					<xf:cell>Column</xf:cell>
					<xf:cell>Type</xf:cell>
					<xf:cell>Description</xf:cell>
				</xf:datarow>
				{$dataRowHtml}
			</xf:datalist>
		</div>
	</div>
</div>
FULLHTML;
	}

	protected function getTypeId(TypeBlock $type)
	{
		return 'type_' . $type->type;
	}

	protected function renderValueRow(AbstractValueLine $value)
	{
		$typeHtml = [];
		foreach ($value->types AS $type)
		{
			if (preg_match('#^([a-z0-9_]+)([\[\{].*)$#i', $type, $match))
			{
				$typeSimple = $match[1];
				$typeExtended = $match[2];
			}
			else
			{
				$typeSimple = $type;
				$typeExtended = '';
			}

			if (isset($this->types[$typeSimple]))
			{
				$typeId = $this->getTypeId($this->types[$typeSimple]);
				$typeHtml[] = '<a href="#' . htmlspecialchars($typeId) . '">'
					. htmlspecialchars($typeSimple)
					. '</a>'
					. htmlspecialchars($typeExtended);
			}
			else
			{
				$typeHtml[] = htmlspecialchars($type);
			}
		}

		$modifiersHtml = [];

		foreach ($value->modifiers AS $modifier)
		{
			if ($modifier === 'req')
			{
				$modifiersHtml[] = '<i class="far fa-info-circle u-featuredText" title="Required" data-xf-init="tooltip"></i>';
			}
			else if ($modifier === 'cond')
			{
				$modifiersHtml[] = '<i class="far fa-question-circle u-featuredText" title="Conditionally returned" data-xf-init="tooltip"></i>';
			}
			else if ($modifier === 'verbose')
			{
				$modifiersHtml[] = '<i class="far fa-file-alt u-featuredText" title="Verbose results only" data-xf-init="tooltip"></i>';
			}
			else if ($modifier === 'perm')
			{
				$modifiersHtml[] = '<i class="far fa-lock u-featuredText" title="Returned only if permissions are met" data-xf-init="tooltip"></i>';
			}
			else
			{
				$modifiersHtml[] = '[' . $modifier . ']';
			}
		}

		return "\t<xf:datarow>"
			. '<xf:cell>' . htmlspecialchars($value->name) . '</xf:cell>'
			. '<xf:cell>' . implode('|', $typeHtml) . '</xf:cell>'
			. '<xf:cell>'
				. ($modifiersHtml ? implode(' ', $modifiersHtml) . ' ' : '')
				. htmlspecialchars($value->description)
				. '</xf:cell>'
			. '</xf:datarow>';
	}

	protected function renderEmptyValuesPlaceholder(AbstractBlock $block)
	{
		return "\t" . '<xf:datarow><xf:cell colspan="3">'
			. ($block->incomplete ? 'Unknown, documentation incomplete' : 'None.')
			. '</xf:cell></xf:datarow>';
	}
}