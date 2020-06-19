<?php

namespace XF\Api\Docs\Renderer;

use XF\Api\Docs\Annotation\AbstractBlock;
use XF\Api\Docs\Annotation\AbstractValueLine;
use XF\Api\Docs\Annotation\RouteBlock;
use XF\Api\Docs\Annotation\TypeBlock;

class SimpleHtml implements RendererInterface
{
	use FileRendererTrait;

	protected $types = [];

	public function renderInternal(array $routeGroupings, array $types)
	{
		$this->types = $types;

		$html = [];

		$html[] = '<h1>Table of Contents</h1>';
		$html[] = $this->renderTableOfContents($routeGroupings, $types);
		$html[] = '';

		$html[] = '<h1>Routes</h1>';
		$html[] = '';

		foreach ($routeGroupings AS $group => $routes)
		{
			$html[] = '<h2>' . htmlspecialchars($group) . '</h2>';

			foreach ($routes AS $route)
			{
				$html[] = $this->renderRoute($route);
				$html[] = '';
			}

			$html[] = '';
		}

		$html[] = '<h1>Types</h1>';
		$html[] = '';

		foreach ($types AS $type)
		{
			$html[] = $this->renderType($type);
			$html[] = '';
		}

		$this->types = [];

		return implode("\n", $html);
	}

	public function renderTableOfContents(array $routeGroupings, array $types)
	{
		$html = [];

		$html[] = '<div>Routes<ul>';
		foreach ($routeGroupings AS $group => $routes)
		{
			$html[] = '<li>' . htmlspecialchars($group) . '<ul>';

			foreach ($routes AS $route)
			{
				/** @var RouteBlock $route */
				$routeId = $this->getRouteId($route);
				$html[] = '<li><a href="#' . htmlspecialchars($routeId) . '">'
					. htmlspecialchars($route->method . ' ' . $route->route)
					. '</a></li>';
			}

			$html[] = '</ul></li>';
		}
		$html[] = '</ul></div>';

		$html[] = '<div>Types<ul>';
		foreach ($types AS $type)
		{
			$typeId = $this->getTypeId($type);
			$html[] = '<li><a href="#' . htmlspecialchars($typeId) . '">' . htmlspecialchars($type->type) . '</a></li>';
		}
		$html[] = '</ul></div>';

		return implode("\n", $html);
	}

	public function renderRoute(RouteBlock $route)
	{
		$html = [];

		$routeId = $this->getRouteId($route);

		$html[] = '<h3 id="' . htmlspecialchars($routeId) . '">'
			. htmlspecialchars($route->method) . ' ' . htmlspecialchars($route->route)
			. '</h3>';
		$html[] = '<div>'
			. htmlspecialchars($route->description)
			. ($route->incomplete ? ' [Incomplete]' : '')
			. '</div>';

		$html[] = '<table border="1">';
		$html[] = "\t<tr>"
			. '<th>Inputs</th>'
			. '<th>Type</th>'
			. '<th>Description</th>'
			. '</tr>';
		if ($route->inputs)
		{
			foreach ($route->inputs AS $input)
			{
				$html[] = $this->renderValueRow($input);
			}
		}
		else
		{
			$html[] = $this->renderEmptyValuesPlaceholder($route);
		}
		$html[] = '</table>';
		$html[] = '<br />';

		$html[] = '<table border="1">';
		$html[] = "\t<tr>"
			. '<th>Outputs</th>'
			. '<th>Type</th>'
			. '<th>Description</th>'
			. '</tr>';
		if ($route->outputs)
		{
			foreach ($route->outputs AS $input)
			{
				$html[] = $this->renderValueRow($input);
			}
		}
		else
		{
			$html[] = $this->renderEmptyValuesPlaceholder($route);
		}
		$html[] = '</table>';
		$html[] = '<br />';

		if ($route->errors)
		{
			$html[] = '<table border="1">';
			$html[] = "\t<tr>"
				. '<th>Errors</th>'
				. '<th>Description</th>'
				. '</tr>';
			foreach ($route->errors AS $errorKey => $description)
			{
				$html[] = "\t<tr>"
					. '<td>' . htmlspecialchars($errorKey) . '</td>'
					. '<td>' . htmlspecialchars($description) . '</td>'
					. '</tr>';
			}
			$html[] = '</table>';
		}

		return implode("\n", $html);
	}

	protected function getRouteId(RouteBlock $route)
	{
		$routeId = preg_replace('#[{}]#', '', $route->route);
		$routeId = preg_replace('#[/\-]#', '_', $routeId);
		$routeId = preg_replace('#[^a-z0-9_]#i', '', $routeId);

		return strtolower('route_' . $route->method . '_' . $routeId);
	}

	public function renderType(TypeBlock $type)
	{
		$html = [];

		$typeId = $this->getTypeId($type);

		$html[] = '<h3 id="' . htmlspecialchars($typeId) . '">' . htmlspecialchars($type->type) . '</h3>';
		$html[] = '<div>'
			. htmlspecialchars($type->description)
			. ($type->incomplete ? ' [Incomplete]' : '')
			. '</div>';

		$html[] = '<table border="1">';
		$html[] = "\t<tr>"
			. '<th>Structure</th>'
			. '<th>Type</th>'
			. '<th>Description</th>'
			. '</tr>';
		if ($type->structure)
		{
			foreach ($type->structure AS $element)
			{
				$html[] = $this->renderValueRow($element);
			}
		}
		else
		{
			$html[] = $this->renderEmptyValuesPlaceholder($type);
		}
		$html[] = '</table>';

		return implode("\n", $html);
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
			if (preg_match('#^([a-z0-9_]+)(\[.*$)#i', $type, $match))
			{
				$typeSimple = $match[0];
				$typeExtended = $match[1];
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

		return "\t<tr>"
			. '<td>' . htmlspecialchars($value->name) . '</td>'
			. '<td>' . implode('|', $typeHtml) . '</td>'
			. '<td>'
				. ($value->modifiers ? '[' . implode(', ', $value->modifiers) . '] ' : '')
				. htmlspecialchars($value->description)
				. '</td>'
			. '</tr>';
	}

	protected function renderEmptyValuesPlaceholder(AbstractBlock $block)
	{
		return "\t" . '<tr><td colspan="3">'
			. ($block->incomplete ? 'Unknown, documentation incomplete' : 'None.')
			. '</td></tr>';
	}
}