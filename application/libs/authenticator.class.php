<?php
namespace application\libs;
/*
* To use this class...your php version must be
* v 5.5 or higher
* admin : iamlegend@1
*/
use core\libs\Database;
use core\libs\Authenticator as Auth;

defined('ACCESS') || AppError::exitApp();

/**
*
*/
class Authenticator extends Auth
{

	public function verifyPassword($data = null)
	{
        global $registry;

        if(is_null($data)){
            return false;
        }

        $data = (object) $data;

        if(password_verify($data->password, trim($data->passwordHash))){

            if(password_needs_rehash($data->password, PASSWORD_DEFAULT)){
                $newHash = password_hash($data->password, PASSWORD_DEFAULT);
                $this->_db->update('users', array('password' => $newHash), array('username' => $data->username));
            }
            return true;
        }
        return false;

	}



}
