<?php

namespace XF\ControllerPlugin;

/**
 * @property \XF\App $app
 * @property \XF\Mvc\Entity\Manager $em
 * @property \XF\Http\Request $request
 * @property \XF\Session\Session $session
 *
 * @method \XF\Mvc\Reply\Redirect redirect($url, $message = null, $type = 'temporary')
 * @method \XF\Mvc\Reply\Message message($message, $code = 200)
 * @method \XF\Mvc\Reply\Error error($error, $code = 200)
 * @method \XF\Mvc\Reply\View view($viewClass = '', $templateName = '', array $params = [])
 * @method \XF\Mvc\Reply\Exception exception(\XF\Mvc\Reply\AbstractReply $reply)
 * @method \XF\Mvc\Reply\AbstractReply noPermission($message = null)
 * @method \XF\Mvc\Reply\AbstractReply notFound($message = null)
 * @method void assertPostOnly()
 * @method void assertValidCsrfToken($token)
 * @method \XF\Mvc\Entity\Entity assertRecordExists($identifier, $id, $with = null, $phraseKey = null)
 * @method void setResponseType($type)
 * @method bool isPost()
 * @method string buildLink($link, $data = null, array $parameters = [])
 * @method string getDynamicRedirect($fallbackUrl = null, $useReferrer = true)
 * @method mixed filter($key, $type = null, $default = null)
 * @method mixed filterPage($key = 'page', $type = 'uint')
 * @method \XF\Mvc\Entity\Repository repository($identifier)
 * @method \XF\Mvc\Entity\Finder finder($identifier)
 * @method \XF\Mvc\Entity\Manager em()
 * @method \XF\Mvc\FormAction formAction()
 * @method \ArrayObject options()
 * @method \XF\Searcher\AbstractSearcher searcher($class, array $criteria = null)
 * @method \XF\Service\AbstractService service($class, ...$params)
 * @method mixed data($class)
 * @method AbstractPlugin plugin($name)
 * @method string buildLinkHash($hash)
 * @method \XF\Session\Session session()
 */
abstract class AbstractPlugin
{
	/**
	 * @var \XF\Mvc\Controller
	 */
	protected $controller;

	public function __construct(\XF\Mvc\Controller $controller)
	{
		$this->controller = $controller;
	}

	public function __get($key)
	{
		switch ($key)
		{
			case 'app': return $this->controller->app();
			case 'em': return $this->controller->em();
			case 'request': return $this->controller->request();
			case 'session': return $this->controller->session();
			case 'responseType': return $this->controller->responseType();
			case 'sectionContext': return $this->controller->sectionContext();
		}

		return $this->controller->$key;
	}

	public function __set($key, $value)
	{
		switch ($key)
		{
			case 'responseType': $this->controller->setResponseType($value); return;
			case 'sectionContext': $this->controller->setSectionContext($value); return;
		}

		$this->controller->$key = $value;
	}

	public function __call($method, array $args)
	{
		return call_user_func_array([$this->controller, $method], $args);
	}
}