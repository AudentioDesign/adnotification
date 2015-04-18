<?php
class Audentio_Notification_ControllerAdmin_AdminNotification extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		$model = $this->_getAdminNotificationModel();
		$adminNotifications = $model->getActiveAdminNotifications(XenForo_Visitor::getUserId());
		$model->prepareAdminNotifications($adminNotifications);

		$viewParams = array(
			'canDelete'		=> XenForo_Visitor::getInstance()->hasAdminPermission('deleteAdminNotifications'),
			'canDismiss'	=> XenForo_Visitor::getInstance()->hasAdminPermission('dismissAdminNotifications'),
			'notifications'	=> $adminNotifications,
		);
		return $this->responseView('Audentio_Notification_ViewAdmin_AdminNotification_List', 'adnotification_admin_notification_list', $viewParams);
	}

	public function actionAll()
	{
		$model = $this->_getAdminNotificationModel();
		$adminNotifications = $model->getAdminNotifications();
		$model->prepareAdminNotifications($adminNotifications);

		$viewParams = array(
			'canDelete'		=> XenForo_Visitor::getInstance()->hasAdminPermission('deleteAdminNotifications'),
			'notifications'	=> $adminNotifications,
		);
		return $this->responseView('Audentio_Notification_ViewAdmin_AdminNotification_List', 'adnotification_admin_notification_list', $viewParams);
	}

	public function actionDismiss()
	{
		$this->assertAdminPermission('dismissAdminNotifications');
		$model = $this->_getAdminNotificationModel();

		$notificationId = $this->_input->filterSingle('notification_id', XenForo_Input::UINT);

		$notification = $model->getAdminNotificationById($notificationId);
		if (!$notification)
		{
			return $this->responseError(new XenForo_Phrase('adnotification_you_have_selected_an_invalid_notification'));
		}

		$model->dismissNotification($notification['notification_id'], XenForo_Visitor::getUserId());

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('adnotifications'));
	}

	public function actionDelete()
	{
		$this->assertAdminPermission('dismissAdminNotifications');
		$model = $this->_getAdminNotificationModel();

		$notificationId = $this->_input->filterSingle('notification_id', XenForo_Input::UINT);

		$notification = $model->getAdminNotificationById($notificationId);
		if (!$notification)
		{
			return $this->responseError(new XenForo_Phrase('adnotification_you_have_selected_an_invalid_notification'));
		}

		//$model->dismissNotification($notification['notification_id'], XenForo_Visitor::getUserId());
		$writer = XenForo_DataWriter::create('Audentio_Notification_DataWriter_AdminNotification');
		$writer->setExistingData($notificationId);
		$writer->delete();

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('adnotifications'));
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