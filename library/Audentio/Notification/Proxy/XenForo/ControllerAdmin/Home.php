<?php
class Audentio_Notification_Proxy_XenForo_ControllerAdmin_Home extends XFCP_Audentio_Notification_Proxy_XenForo_ControllerAdmin_Home
{
	public function actionIndex()
	{
		$response = parent::actionIndex();

		$response->params['adnotification_numAdminNotifications'] = $this->_getAdminNotificationModel()->countActiveAdminNotifications(XenForo_Visitor::getUserId());

		return $response;
	}

	/**
	 * Returns the admin notification model
	 *
	 * @return Audentio_Notification_Model_AdminNotification
	 */
	protected function _getAdminNotificationModel()
	{
		return $this->getModelFromCache('Audentio_Notification_Model_AdminNotification');
	}
}
if (false)
{
	class XFCP_Audentio_Notification_Proxy_XenForo_ControllerAdmin_Home extends XenForo_ControllerAdmin_Home {}
}