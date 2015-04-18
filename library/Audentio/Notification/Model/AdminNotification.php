<?php
class Audentio_Notification_Model_AdminNotification extends XenForo_Model
{
	protected static $_handlerCache = array();
	/**
	 * Create a new Admin Notification
	 *
	 * @param $userId
	 * @param $username
	 * @param $contentType
	 * @param $contentId
	 * @param $action
	 * @param array $extraData
	 */
	public static function notify($contentType, $contentId, $action, array $extraData=array())
	{
		XenForo_Model::create(__CLASS__)->notifyAdministrators($contentType, $contentId, $action, $extraData);
	}

	/**
	 * Create a new Admin Notification
	 *
	 * @param $userId
	 * @param $username
	 * @param $contentType
	 * @param $contentId
	 * @param $action
	 * @param array $extraData
	 */
	public function notifyAdministrators($contentType, $contentId, $action, array $extraData=array())
	{
		$writer = XenForo_DataWriter::create('Audentio_Notification_DataWriter_AdminNotification');
		$writer->set('content_type', $contentType);
		$writer->set('content_id', $contentId);
		$writer->set('action', $action);
		$writer->setExtra($extraData);
		$writer->save();
	}

	/**
	 * Get the contents needed by various notifications
	 *
	 * @param array $data
	 * @param $userId
	 * @param array $viewingUser
	 */
	protected function _getContentForAdminNotifications(&$notifications)
	{
		$fetchQueue = array();
		$fetchMap = array();

		// Build the fetch queue
		foreach ($notifications as $notification)
		{
			if (!array_key_exists($notification['content_type'], $fetchQueue))
			{
				$fetchQueue[$notification['content_type']] = array();
				$fetchMap[$notification['content_type']] = array();
			}

			$fetchQueue[$notification['content_type']][$notification['notification_id']] = $notification['content_id'];
			$fetchMap[$notification['content_type']][$notification['content_id']] = $notification['notification_id'];
		}

		foreach ($fetchQueue as $contentType=>$contentIds)
		{
			$class = $this->getAdminNotificationHandlerForContent($contentType);
			$handler = $this->getAdminNotificationHandlerFromCache($class);

			$viewingUser = XenForo_Visitor::getInstance()->toArray();
			$items = $handler->getContentByIds($contentIds, $viewingUser);
			foreach ($items as $itemId=>$item)
			{
				if (!array_key_exists($itemId, $fetchMap[$contentType])) continue;
				$notifications[$fetchMap[$contentType][$itemId]] = array_merge($notifications[$fetchMap[$contentType][$itemId]], $item);
			}
		}
	}

	public function dismissNotification($notificationId, $userId)
	{
		$db = $this->_getDb();
		$db->beginTransaction();
		$data['notification_id'] = $notificationId;
		$data['user_id'] = $userId;
		$data['dismiss_date'] = XenForo_Application::$time;
		$db->insert('adnotification_admin_dismiss', $data);
		$db->commit();
	}

	/**
	 * Prepare notifications for display
	 *
	 * @param $notifications
	 */
	public function prepareAdminNotifications(&$notifications)
	{
		foreach ($notifications as &$notification)
		{
			$notification['extra_data'] = @unserialize($notification['extra_data']);
		}
		$this->_getContentForAdminNotifications($notifications);
	}

	/**
	 * Get admin notifications
	 */
	public function getAdminNotifications()
	{
		return $this->fetchAllKeyed('
			SELECT notification.*
			FROM adnotification_admin AS notification
			', 'notification_id');
	}

	/**
	 * counts admin notifications that have not been dismissed
	 *
	 * @param $viewingUserId
	 */
	public function countActiveAdminNotifications($viewingUserId)
	{
		return $this->_getDb()->fetchOne('
			SELECT COUNT(notification.notification_id)
			FROM adnotification_admin AS notification
			LEFT JOIN adnotification_admin_dismiss AS dismiss ON (dismiss.user_id=? AND dismiss.notification_id = notification.notification_id)
			WHERE dismiss.dismiss_date IS NULL
			', $viewingUserId);
	}

	/**
	 * Get admin notifications that have not been dismissed
	 *
	 * @param $viewingUserId
	 */
	public function getActiveAdminNotifications($viewingUserId)
	{
		return $this->fetchAllKeyed('
			SELECT notification.*, dismiss.dismiss_date
			FROM adnotification_admin AS notification
			LEFT JOIN adnotification_admin_dismiss AS dismiss ON (dismiss.user_id=? AND dismiss.notification_id = notification.notification_id)
			WHERE dismiss.dismiss_date IS NULL
			', 'notification_id', $viewingUserId);
	}

	/**
	 * Get admin notifications that have been dismissed
	 *
	 * @param $viewingUserId
	 */
	public function getDismissedAdminNotifications($viewingUserId)
	{
		return $this->fetchAllKeyed('
			SELECT notification.*, dismiss.dismiss_date
			FROM adnotification_admin AS notification
			LEFT JOIN adnotification_admin_dismiss AS dismiss ON (dismiss.user_id=? AND dismiss.notification_id = notification.notification_id)
			WHERE dismiss.dismiss_date IS NOT NULL
			', 'notification_id', $viewingUserId);
	}

	/**
	 * Gets a single admin notification by its ID
	 *
	 * @param $notificationId
	 */
	public function getAdminNotificationById($notificationId)
	{
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM adnotification_admin
			WHERE notification_id=?
			', $notificationId);
	}

	/**
	 * Get the admin notification handler
	 * @param $class
	 */
	public function getAdminNotificationHandlerFromCache($class)
	{
		if (!$class || !class_exists($class))
		{
			return false;
		}

		if (!isset(self::$_handlerCache[$class]))
		{
			self::$_handlerCache[$class] = Audentio_Notification_AdminNotificationHandler_Abstract::create($class);
		}

		return self::$_handlerCache[$class];
	}

	public function getAdminNotificationHandlerForContent($contentType)
	{
		return $this->getContentTypeField($contentType, 'adnotification_admin');
	}
}