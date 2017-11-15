<?php

/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class LoginModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'login', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function authenticate($values)
	{
		# code...
		global $registry;


		#check if all required fields where filled
		$check = json_decode($registry->get('form')->checkRequiredFields(array('username','pwd')));
		if($check->status == 'error'){


			//set tmpl value bcos i want it to display the widget with the original form
			$this->execute(array('action'=>'display', 'tmpl' => 'login', 'widget' => 'error', 'msg' => $check->msg));
			return false;
		}


		$username = $registry->get('form')->sanitize($values['username'], 'string');
		$pwd = $registry->get('form')->sanitize($values['pwd'], 'string');

		# fetch user hashed password from Db using username
		$query = 'select id, password from users where username = :username';
		$result = (object)$registry->get('db')->bindFetch($query, array('username' => $username), array('id', 'password'));


		if(is_null($result->password)){
			$this->execute(array('action'=>'display', 'tmpl' => 'login', 'widget' => 'error', 'msg' => 'Invalid Username or Password'));
			return false;
		}

		# verify password_verify
		$passwordCheck = $registry->get('authenticator')->verifyPassword(array(
			'username' => $username,
			'password' => $pwd,
			'passwordHash' => $result->password
		));


		if(!$passwordCheck){
			$this->execute(array('action'=>'display', 'tmpl' => 'login', 'widget' => 'error', 'msg' => 'Invalid Username or Password'));
			return false;
		}



		$thisUser = new AppUser($result->id);

		# store user details in session
		$registry->get('session')->write('thisUser', serialize($thisUser));


		#if user is admin or duty manager
		if(array_search($thisUser->get('privilege'), array(1)) !== false){
			$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/login/options');
		}


		# login user
		$registry->get('session')->write('loggedIn', true);

		# log user loggin
		$registry->get('logger')->logUserLogin($thisUser->get('id'));

		$registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/dashboard');

	}

    public function showLoginOptions()
    {
    	# code...
    	$this->execute(array('action'=>'render', 'tmpl' => 'loginOptions', 'widget' => '', 'msg' => ''));
    }


	#end of class
}
