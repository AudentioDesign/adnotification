<?php
class Audentio_Notification_Listener_CodeEvent
{
	/**
	 * Loads classes via the XenForo Class Proxy
	 *
	 * @param $class
	 * @param array $extend
	 */
	public static function loadClass($class, array &$extend)
	{
		switch ($class)
		{
			case 'XenForo_ControllerAdmin_Home':
				$extend[] = 'Audentio_Notification_Proxy_XenForo_ControllerAdmin_Home';
				break;
			case 'XenForo_Model_ContentType':
				$extend[] = 'Audentio_Notification_Proxy_XenForo_Model_ContentType';
				break;
		}
	}
}