<?php

namespace XF\ControllerPlugin;

class NodePermission extends AbstractPermission
{
	protected $viewFormatter = 'XF:Permission\Node%s';
	protected $templateFormatter = 'permission_node_%s';
	protected $routePrefix = 'permissions/nodes';
	protected $contentType = 'node';
	protected $entityIdentifier = 'XF:Node';
	protected $primaryKey = 'node_id';
	protected $privatePermissionGroupId = 'general';
	protected $privatePermissionId = 'viewNode';
}