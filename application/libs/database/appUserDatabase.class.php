<?php
namespace application\libs\database;
use \PDO;
use core\libs\Database as Db;
/**
*  This database class performs actions that are too generic
*	( generals database operations )
* 	Also that class used by the authenticator
*/
defined('ACCESS') || AppError::exitApp();

class AppUserDatabase extends Db{


	public function fetchAppUser($appUserId)
	{
		# code...
		$query = 'select * from users where id = :id';
		return parent::query($query, array('id' => $appUserId));
	}

	public function getPrivilege($privId){
		$query = 'select privilege from privileges where id = :privId';
		$response =  parent::bindFetch($query, array('privId' => $privId), array('privilege'));
		return $response['privilege'];
	}

	public function fetchNotifications($appUserId){
		$query = 'select * from notifications where userId = :userId';
		return parent::query($query, array('userId' => $appUserId), true);
	}

	public function addNewUserAcct(Array $data)
	{
		# code...
		parent::insert('users', array('name' => $data['name'], 'username' => $data['username'], 'password' => $data['pwd'], 'privilege' => $data['privilege']));
	}

	public function fetchAllUsers($includeMgtStaff)
	{
		# code...
		global $registry;

		$query = 'select * from `users` where `privilege` != 1';
        if(!$includeMgtStaff){
            $query .= ' and `privilege` not in (2,3)';
        }

		return $registry->get('db')->query($query, array(), true);

	}



//	public function updatePwdHash($id, $newHash)
//	{
//		# code...
//		parent::update('appUsers',array('password'=>$newHash), array('id'=>$id));
//		return $this;
//	}



#end of class
}
