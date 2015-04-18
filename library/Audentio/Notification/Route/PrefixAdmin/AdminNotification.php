<?php
class Audentio_Notification_Route_PrefixAdmin_AdminNotification implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithStringParam($routePath, $request, 'notification_id');
		return $router->getRouteMatch('Audentio_Notification_ControllerAdmin_AdminNotification', $action, 'adnotification');
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithStringParam($outputPrefix, $action, $extension, $data, 'notification_id');
	}
}