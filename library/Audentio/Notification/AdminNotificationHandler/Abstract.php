<?php
abstract class Audentio_Notification_AdminNotificationHandler_Abstract
{
	protected static $_modelCache = array();

	/**
	 * Factory method to get the named admin notification handler.
	 * The class must exist and be autoloadable or an exception will be thrown.
	 *
	 * @param string Class to load
	 *
	 * @return Audentio_Notification_AdminNotificationHandler
	 */
	public static function create($class)
	{
		$class = XenForo_Application::resolveDynamicClass($class);
		if (XenForo_Application::autoload($class))
		{
			$obj = new $class();
			if ($obj instanceof Audentio_Notification_AdminNotificationHandler_Abstract)
			{
				return $obj;
			}
		}

		throw new XenForo_Exception("Invalid admin notification handler '$class' specified");
	}

	/**
	 * Fetches content required by notification
	 *
	 * @param array $contentIds
	 * @param array $viewingUser
	 */
	abstract public function getContentByIds(array $contentIds, array $viewingUser);

	/**
	 * Returns a template title in the form 'alert_{contentType}_{action}'
	 *
	 * @param string $contentType
	 * @param string $action
	 */
	protected function _getDefaultTemplateTitle($contentType, $action)
	{
		return 'admin_notification_' . $contentType . '_' . $action;
	}

	/**
	 * Renders an item content template
	 *
	 * @param array $item
	 * @param XenForo_View $view
	 *
	 * @return XenForo_Template_Public
	 */
	public function renderHtml(array $item, XenForo_View $view)
	{
		$item['templateTitle'] = $this->_getDefaultTemplateTitle($item['content_type'], $item['action']);

		$methodName = '_renderHtml' . ucfirst($item['action']);

		if (method_exists($this, $methodName))
		{
			return call_user_func(array($this, $methodName), $item, $view);
		}

		$tplParams = array(
			'item'			=> $item,
		);
		return $view->createTemplateObject($item['templateTitle'], $tplParams);
	}

	/**
	 * Gets Model From Cache
	 *
	 * @param $class
	 * @return XenForo_Model
	 */
	public function getModelFromCache($class)
	{
		if (!isset(self::$_modelCache[$class]))
		{
			self::$_modelCache[$class] = XenForo_Model::create($class);
		}

		return self::$_modelCache[$class];
	}
}