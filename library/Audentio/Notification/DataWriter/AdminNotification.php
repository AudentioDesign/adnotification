<?php
class Audentio_Notification_DataWriter_AdminNotification extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array(
			'adnotification_admin' => array(
				'notification_id'		=> array('type' => self::TYPE_UINT, 'autoIncrement' => true),
				'content_type'			=> array('type' => self::TYPE_STRING, 'maxLength' => 25),
				'content_id'			=> array('type' => self::TYPE_UINT),
				'action'				=> array('type' => self::TYPE_STRING, 'maxLength' => 25),
				'extra_data'			=> array('type' => self::TYPE_SERIALIZED),
				'notification_date'		=> array('type' => self::TYPE_UINT, 'default' => XenForo_Application::$time)
			)
		);
	}

	/**
	 * Sets any extra data required for the notification
	 *
	 * @param array $extraData
	 */
	public function setExtra(array $extraData)
	{
		$extraData = @serialize($extraData);
		$this->set('extra_data', $extraData);
	}

	/**
	 * Gets the actual existing data out of data that was passed in. See parent for explanation.
	 *
	 * @param mixed
	 *
	 * @return array|false
	 */
	protected function _getExistingData($data)
	{
		if (!$notificationId = $this->_getExistingPrimaryKey($data, 'notification_id'))
		{
			return false;
		}

		return array('adnotification_admin' => $this->_getAdminNotificationModel()->getAdminNotificationById($notificationId));
	}

	/**
	 * Remove the dismiss rows associated with the deleted notification
	 */
	public function _postDelete()
	{
		$db = $this->_db;
		$db->beginTransaction();
		$db->query('
			DELETE
			FROM adnotification_admin_dismiss
			WHERE notification_id=?
			', $this->get('notification_id'));
		$db->commit();
	}

	/**
	 * Gets SQL condition to update the existing record.
	 *
	 * @return string
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'notification_id = ' . $this->_db->quote($this->getExisting('notification_id'));
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