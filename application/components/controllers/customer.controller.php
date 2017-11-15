<?php

defined('ACCESS') or Error::exitApp();

class CustomerController extends BaseController{

	protected $_urlAllowedMthds = array('render', 'addInfo', 'sendBulkMail', 'sendBulkSMS', 'viewAll');


	public function render(){
		global $registry;
	}


	public function viewAll()
	{

		$this->_model->execute(array('action'=>'render', 'tmpl' => 'viewAllCustomers', 'widget' => '', 'msg' => ''));
	}

	public function addInfo()
	{
		if(isset($_POST['submit'])){
			$this->_model->addCustomerInfo($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addCustomerInfo', 'widget' => '', 'msg' => ''));
		}
	}



}
