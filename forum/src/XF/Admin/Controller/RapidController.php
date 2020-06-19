<?php
/**
 * Created by PhpStorm.
 * User: kier
 * Date: 22/01/2018
 * Time: 20:44
 */

namespace XF\Admin\Controller;

use XF\Mvc\Entity\RapidEntity;
use XF\Mvc\ParameterBag;

abstract class RapidController extends AbstractController
{
	protected $structure;

	abstract protected function _entity();
	abstract protected function _route();

	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->structure = $this->em()->getEntityStructure($this->_entity());
	}

	public function actionAdd(ParameterBag $params)
	{
		$record = $this->em()->create($this->_entity());
		return $this->recordAddEdit($record);
	}

	public function actionEdit(ParameterBag $params)
	{
		$record = $this->assertContentExists($params[$this->structure->primaryKey]);
		return $this->recordAddEdit($record);
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params[$this->structure->primaryKey])
		{
			$record = $this->assertContentExists($params[$this->structure->primaryKey]);
		}
		else
		{
			$record = $this->em()->create($this->_entity());
		}

		$this->recordSaveProcess($record)->run();

		return $this->redirect($this->buildLink($this->_route()));
	}

	protected function recordAddEdit(RapidEntity $record)
	{
		$viewParams = [
			'record' => $record
		];

		return $this->view($this->getViewName('Edit'), $this->getTemplateName('edit'), $viewParams);
	}

	protected function assertContentExists($id, $with = null)
	{
		return $this->assertRecordExists($this->_entity(), $id, $with);
	}

	/**
	 * @param $action
	 *
	 * @return string {$entityShortName}\{$action}
	 */
	protected function getViewName($action)
	{
		return sprintf('%\%', $this->_entity(), $action);
	}

	/**
	 * @param $action
	 *
	 * @return string {content_type}_{$action}
	 */
	protected function getTemplateName($action)
	{
		return sprintf('%_%', $this->structure->contentType, $action);
	}
}