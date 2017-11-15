<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class CustomerModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function addCustomerInfo(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('name', 'phone');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addCustomerInfo', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		$registry->get('db')->insert('customerInfo', array(
			'name' => ucwords($sanitized['name']),
			'phone' => $sanitized['phone'],
			'email' => $sanitized['email'],
			'address' => $sanitized['address'],
			'birthDay' => $sanitized['birthDay'],
			'birthMonth' => $sanitized['birthMonth']
		));


		$msg = 'Customer Info of ' . ucwords($sanitized['name']) . ' successfully added';
		$this->execute(array('action'=>'display', 'tmpl' => 'addCustomerInfo', 'widget' => 'success', 'msg' => $msg));

	}



	#end of class
}
