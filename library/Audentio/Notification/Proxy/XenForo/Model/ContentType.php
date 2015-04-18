<?php
class Audentio_Notification_Proxy_XenForo_Model_ContentType extends XFCP_Audentio_Notification_Proxy_XenForo_Model_ContentType
{
	public function getContentTypeFieldNames()
	{
		$fieldNames = parent::getContentTypeFieldNames();
		if(!array_key_exists('adnotification_admin', $fieldNames))
		{
			$fieldNames['adnotification_admin'] = 'adnotification_admin';
		}

		ksort($fieldNames);

		return $fieldNames;
	}
}
if (false)
{
	class XFCP_Audentio_Notification_Proxy_XenForo_Model_ContentType extends XenForo_Model_ContentType {}
}