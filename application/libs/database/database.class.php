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

class Database extends Db{

	public function __construct(array $options)
	{
		parent::__construct($options);

	}

	public function getPwdHash($username)
	{
		# code...
		$query = 'select * from users where username = :username';
		return parent::query($query, array('username' => $username));
	}

	public function updatePwdHash($id, $newHash)
	{
		# code...
		parent::update('users',array('password'=>$newHash), array('id'=>$id));
		return $this;
	}

	public function fetchAllPrivileges($includeMgt)
	{
		# code...
		$query = 'select * from privileges';
		if(!$includeMgt){
			$query .= ' where id not in ( 1, 2 )';
		}
		return parent::query($query, array(), true);
	}


	public function logoutAppUser($appUserId){
		parent::delete('loggedinusers', array('appUserId' => $appUserId));
		return $this;
	}

	public function checkUserExist($username, $pwd)
	{
		# code...
		return parent::query('select * from users where username = :username and password = :password', array('username' => $username, 'password' => $pwd));
	}


	public function truncateTbl($tbl)
    {
        # code...
        $this->_driver->exec('truncate ' . $tbl);
    }

    public function updateTbl($tbl)
    {
        # code...
        $st = $this->_driver->prepare('update `' . $tbl . '` set `qtyInStock` = 0');
        return $st->execute() ? true : false;

    }




#end of class
}
