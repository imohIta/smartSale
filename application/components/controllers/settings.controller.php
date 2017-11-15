<?php

defined('ACCESS') or Error::exitApp();

class SettingsController extends BaseController{

	protected $_urlAllowedMthds = array('render');


	public function render(){

        if(isset($_POST)){
            $this->_model->proccessSettings($_POST);
        }else{
            $this->_model->attach(new GeneralView());
    	    $this->_model->execute();
        }

	}


}
