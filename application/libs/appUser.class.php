<?php

defined('ACCESS') || Error::exitApp();

/**
* Had to edit this class froma normal class to a decorator object that collects a staff class
*/
class AppUser extends FuniObject
{
	protected $_id = null;
	protected $_name = null;
	protected $_username = null;
	protected $_privilege = null;
	protected $_activeAcct = null;
	protected $_role = null;


	function __construct($obj)
	{

		# code...
		global $registry;
		$db = $registry->get('appUserDb');

		# if appUserId only is passed
		if(!is_object($obj)){

			# fetch user details
			$obj = $db->fetchAppUser($obj);

		}

		if ( !is_null($obj) && false !== $obj) {

			$this->_id = $obj->id;
			$this->_name = $obj->name;
			$this->_username = $obj->username;
			$this->_privilege = $this->_activeAcct = $obj->privilege;
			$this->_role = $this->_getRole();
		}

	}

	private function _getRole()
	{
		# code...
		global $registry;
		return $registry->get('appUserDb')->getPrivilege($this->_activeAcct);
	}

	public function getOriginalRole(){
		global $registry;
		return $registry->get('appUserDb')->getPrivilege($this->privilege);
	}

    #overides FuniObject Set
	public function set($key, $value, $prefix = true){
	   $key = ($prefix) ? '_'.trim($key,'_') : trim($key,'_');
	   if(property_exists(__CLASS__, $key)){
			$this->{$key} = $value;

			#if privilege is reset...reset role also
			if($key == '_activeAcct'){
				$this->role = $this->_getRole($this->_activeAcct);
			}
		}
	}



   /********************************
    * ******************************
    *  Static Functions
	********************************/



  public static function getRole($priv){
	global $registry;
	return $registry->get('appUserDb')->getPrivilege($priv);

  }

  public static function fetchAll($includeMgt = true)
  {
  	# code...
  	global $registry;
  	return $registry->get('appUserDb')->fetchAllUsers($includeMgt);
  }


  public static function fetchAllPrivileges($includeMgt = false)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->fetchAllPrivileges($includeMgt);
  }

  public static function addNew(Array $data)
  {
  	# code...
  	global $registry;
  	return $registry->get('appUserDb')->addNewUserAcct($data);
  }

  public static function delete($appUserId)
  {
  	# code...
  	global $registry;
  	return $registry->get('appUserDb')->deleteUser($appUserId);
  }

  public static function update($desc, $value, $userId)
  {
  	# code...
	global $registry;
	return $registry->get('db')->updateUserDetails($desc, $value, $userId);
  }

  public static function fetchDetails($appUserId)
  {
  	# code...
  	global $registry;
  	return $registry->get('db')->fetchUserDetails($appUserId);
  }

  public static function fetchSalesReps()
  {
  	# code...
	global $registry;

	return $registry->get('db')->query('select * from users where privilege = 5', array(), true);
  }



#end of class
}
