<?php
class Audentio_Notification_Listener_Uninstall
{
	protected $_db = null;

	public static function getInstance()
	{
		$class = XenForo_Application::resolveDynamicClass(__CLASS__);

		return new $class();
	}

	public static function uninstall($existingAddOn)
	{
		$installer = self::getInstance();

		$installedVersion = $existingAddOn['version_id'];
		$startPosition = $installedVersion;
		$stopPosition = 1000000;

		$db = XenForo_Application::getDb();
		for ($i=$startPosition;$i>=$stopPosition;$i--)
		{
			$method = '_remove'.$i;
			if (method_exists($installer, $method))
			{
				$installer->$method();
			}
		}
	}

	protected function _remove1000001()
	{
		$db = $this->_getDb();
		$db->query("DROP TABLE IF EXISTS `adnotification_admin`  ;");
		$db->query("DROP TABLE IF EXISTS `adnotification_admin_dismiss` ;");
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