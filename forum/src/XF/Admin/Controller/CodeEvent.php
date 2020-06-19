<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class CodeEvent extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertDevelopmentMode();
	}

	public function actionIndex()
	{
		$viewParams = [
			'events' => $this->getEventRepo()->findEventsForList()->fetch()
		];
		return $this->view('XF:CodeEvent\Listing', 'code_event_list', $viewParams);
	}

	protected function eventAddEdit(\XF\Entity\CodeEvent $event)
	{
		$viewParams = [
			'event' => $event
		];
		return $this->view('XF:CodeEvent\Edit', 'code_event_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$event = $this->assertEventExists($params['event_id']);
		return $this->eventAddEdit($event);
	}

	public function actionAdd()
	{
		$event = $this->em()->create('XF:CodeEvent');
		return $this->eventAddEdit($event);
	}

	protected function eventSaveProcess(\XF\Entity\CodeEvent $event)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'event_id' => 'str',
			'description' => 'str',
			'addon_id' => 'str'
		]);
		$form->basicEntitySave($event, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['event_id'])
		{
			$event = $this->assertEventExists($params['event_id']);
		}
		else
		{
			$event = $this->em()->create('XF:CodeEvent');
		}

		$this->eventSaveProcess($event)->run();

		return $this->redirect($this->buildLink('code-events') . $this->buildLinkHash($event->event_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$event = $this->assertEventExists($params->event_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$event,
			$this->buildLink('code-events/delete', $event),
			$this->buildLink('code-events/edit', $event),
			$this->buildLink('code-events'),
			$event->event_id
		);
	}

	public function actionGetDescription()
	{
		/** @var \XF\ControllerPlugin\DescLoader $plugin */
		$plugin = $this->plugin('XF:DescLoader');
		return $plugin->actionLoadDescription('XF:CodeEvent');
	}

	public function actionListener()
	{
		$listeners = $this->getListenerRepo()
			->findListenersForList()
			->fetch();

		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->repository('XF:AddOn');
		$addOns = $addOnRepo->findAddOnsForList()->fetch();

		$viewParams = [
			'listeners' => $listeners->groupBy('addon_id'),
			'totalListeners' => $listeners->count(),
			'addOns' => $addOns
		];
		return $this->view('XF:CodeEvent\Listener\Listing', 'code_event_listener_list', $viewParams);
	}

	protected function listenerAddEdit(\XF\Entity\CodeEventListener $listener)
	{
		$events = $this->getEventRepo()
			->findEventsForList()
			->fetch()
			->pluckNamed('event_id', 'event_id');

		$viewParams = [
			'listener' => $listener,
			'events' => $events
		];
		return $this->view('XF:CodeEvent\Listener\Edit', 'code_event_listener_edit', $viewParams);
	}

	public function actionListenerEdit(ParameterBag $params)
	{
		$listener = $this->assertListenerExists($params['event_listener_id']);
		return $this->listenerAddEdit($listener);
	}

	public function actionListenerAdd()
	{
		$listener = $this->em()->create('XF:CodeEventListener');
		return $this->listenerAddEdit($listener);
	}

	protected function listenerSaveProcess(\XF\Entity\CodeEventListener $listener)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'event_id' => 'str',
			'execute_order' => 'uint',
			'description' => 'str',
			'callback_class' => 'str',
			'callback_method' => 'str',
			'active' => 'bool',
			'addon_id' => 'str',
			'hint' => 'str'
		]);
		$form->basicEntitySave($listener, $input);

		return $form;
	}

	public function actionListenerSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['event_listener_id'])
		{
			$listener = $this->assertListenerExists($params['event_listener_id']);
		}
		else
		{
			$listener = $this->em()->create('XF:CodeEventListener');
		}

		$this->listenerSaveProcess($listener)->run();

		return $this->redirect($this->buildLink('code-events/listeners') . $this->buildLinkHash($listener->event_listener_id));
	}

	public function actionListenerDelete(ParameterBag $params)
	{
		$listener = $this->assertListenerExists($params['event_listener_id']);

		$contentTitle = $listener->event_id;
		if ($listener->AddOn)
		{
			$contentTitle .= sprintf(" %s%s %s%s",
				\XF::language()->parenthesis_open,
				\XF::phrase('add_on:'),
				$listener->AddOn->title,
				\XF::language()->parenthesis_close
			);
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$listener,
			$this->buildLink('code-events/listeners/delete', $listener),
			$this->buildLink('code-events/listeners/edit', $listener),
			$this->buildLink('code-events/listeners'),
			$contentTitle

		);
	}

	public function actionListenerToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:CodeEventListener');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\CodeEvent
	 */
	protected function assertEventExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:CodeEvent', $id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\CodeEventListener
	 */
	protected function assertListenerExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:CodeEventListener', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\CodeEvent
	 */
	protected function getEventRepo()
	{
		return $this->repository('XF:CodeEvent');
	}

	/**
	 * @return \XF\Repository\CodeEventListener
	 */
	protected function getListenerRepo()
	{
		return $this->repository('XF:CodeEventListener');
	}
}