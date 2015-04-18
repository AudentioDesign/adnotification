<?php
class Audentio_Notification_ViewAdmin_AdminNotification_List extends XenForo_ViewAdmin_Base
{
	public function renderHtml()
	{
		$model = XenForo_Model::create('Audentio_Notification_Model_AdminNotification');
		$notifications =& $this->_params['notifications'];
		foreach ($notifications as &$notification)
		{
			$class = $model->getAdminNotificationHandlerForContent($notification['content_type']);
			$handler = $model->getAdminNotificationHandlerFromCache($class);
			$notification['template_html'] = $handler->renderHtml($notification, $this);
		}
	}
}