<?php
namespace application\libs;
use core\libs\Logger as CoreLogger;
use application\libs\database\LoggerDatabase as LoggerDb;
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

/**
*
*/
class Logger
{

	private $_db;


	public function __construct(LoggerDb $db)
	{
		$this->_db = $db;
	}


	public function logUserLogin($appUserId)
	{
		# code...

		$this->_db->logUserLogin(array(
			'date' => date('Y-m-d'),
			'time' => time(),
			'appUserId' => $appUserId
		));


	}

	public function logPriceChange(Array $data){
		$this->_db->logPriceChange($data);
	}


	public function logBadItemRemoval(Array $data){
		$this->_db->logBadItemRemoval($data);
	}




	# End of Class
}
