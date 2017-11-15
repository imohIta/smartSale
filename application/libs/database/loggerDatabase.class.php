<?php
namespace application\libs\database;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class LoggerDatabase extends Db{
	
	public function logNotification(Array $data)
	{
		$data['targetStaffId'] = isset($data['targetStaffId']) ? $data['targetStaffId'] : 0;
		$query = '';

		$st = $this->_driver->prepare($query);
		foreach ($data as $key => $value) {
			# code...
			if(in_array($key, array('notType','staffId','targetStaffId')) !== false){
				$st->bindValue(':'.$key, $value, PDO::PARAM_INT);
			}else{
				$st->bindValue(':'.$key, $value, PDO::PARAM_STR);
			}
		}
		return $st->execute() ? true : false;

	}

	public function logUserLogin($data){
		parent::insert('loggedInUsers', array('date' => $data['date'], 'time' => $data['time'], 'appUserId' => $data['appUserId']));
	}

	public function logPriceChange(Array $data){
		parent::insert('notifications', array('date' => $data['date'], 'time' => $data['time'], 'msg' => $data['msg'], 'userId' => $data['userId'], 'privilege' => $data['privilege']));
	}

	public function logBadItemRemoval(Array $data){
		parent::insert('notifications', array(
			'date' => $data['date'],
			'time' => $data['time'],
			'msg' => $data['msg'],
			'userId' => $data['staffId'],
			'privilege' => $data['privilege']
		));
	}


#end of class	
}