<?php

if (!defined('_PS_VERSION_'))
	exit;

	
require_once(_DPDPOLAND_CLASSES_DIR_.'Configuration.php');
require_once(_DPDPOLAND_MODULE_DIR_.'dpdpoland.php');

class DpdPolandLog
{	
	const LOG_DEBUG = 'LOG_DEBUG';
	const LOG_ERROR = 'LOG_ERROR';

	public static function addLog($message)
	{
		if(!in_array(Configuration::get(DpdPolandConfiguration::LOG_MODE), array(self::LOG_DEBUG)))
			return;

		$logger = new FileLogger(0);
		$logger->setFilename(_DPDPOLAND_MODULE_DIR_."/log/logs.log");
		$logger->logDebug($message);
	}

	public static function addError($message)
	{
		if(!in_array(Configuration::get(DpdPolandConfiguration::LOG_MODE), array(self::LOG_DEBUG, self::LOG_ERROR)))
			return;

		$logger = new FileLogger(0);
		$logger->setFilename(_DPDPOLAND_MODULE_DIR_."/log/logs.log");
		$logger->logError($message);
	}
}
