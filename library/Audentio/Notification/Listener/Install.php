<?php
class Audentio_Notification_Listener_Install
{
	protected $_db = null;

	public static function getInstance()
	{
		$class = XenForo_Application::resolveDynamicClass(__CLASS__);

		return new $class();
	}
	public static function install($existingAddOn, $addOnData)
	{
		$installer = self::getInstance();

		$installedVersion = is_array($existingAddOn) ? $existingAddOn['version_id'] : 0;
		$startPosition = $installedVersion;
		$stopPosition = $addOnData['version_id'];
		if (!$installedVersion)
		{
			$startPosition = 1000000;
		}

		$db = XenForo_Application::getDb();
		for ($i=$startPosition;$i<=$stopPosition;$i++)
		{
			$method = '_update'.$i;
			if (method_exists($installer, $method))
			{
				$installer->$method();
			}
		}
	}

	protected function _update1000001()
	{
		$db = $this->_getDb();
		$db->query("
			CREATE TABLE IF NOT EXISTS `adnotification_admin` (
			  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `content_type` varbinary(25) NOT NULL,
			  `content_id` int(10) unsigned NOT NULL,
			  `action` varbinary(25) NOT NULL,
			  `notification_date` int(10) unsigned NOT NULL,
			  `extra_data` blob NOT NULL,
			  PRIMARY KEY (`notification_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
			");
		$db->query("
			CREATE TABLE IF NOT EXISTS `adnotification_admin_dismiss` (
			  `notification_id` int(10) unsigned NOT NULL,
			  `user_id` int(10) unsigned NOT NULL,
			  `dismiss_date` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`notification_id`,`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			");
	}

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	protected function _getDb()
	{
		if (!$this->_db)
		{
			$this->_db = XenForo_Application::getDb();
		}
		return $this->_db;
	}
}