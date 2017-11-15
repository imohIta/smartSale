<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class LogoutModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(){
		global $registry;
		$thisUser = unserialize($registry->get('session')->read('thisUser'));

		# delete user from logged In users table
		//$registry->get('appDb')->logoutAppUser($thisUser->get('id'));

		# user sessions
		$registry->get('session')->write('loggedIn', null);
		$registry->get('session')->write('thisUser', null);

		$registry->get('session')->destroy();

		#redirect to login page
		$registry->get('uri')->redirect();
	}

	public function execute2(Array $options)
	{
		# code...
		$this->_viewParams = $options;
		$this->notify();
	}


	#end of class
}
