<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class StaffModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}


	public function addProfile(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('name', 'phone', 'address', 'gender', 'dept', 'salary');
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

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		$registry->get('db')->insert('staffinfo', array(
			'name' => ucwords($sanitized['name']),
			'phone' => $sanitized['phone'],
			'gender' => $sanitized['gender'],
			'address' => $sanitized['address'],
			'dept' => $sanitized['dept'],
			'salary' => $sanitized['salary']
		));


		$msg = 'Staff Profile was successfully created for ' . ucwords($sanitized['name']);
		$this->execute(array('action'=>'display', 'tmpl' => 'addStaffProfile', 'widget' => 'success', 'msg' => $msg));

	}

	public function editProfile(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('name', 'phone', 'address', 'gender', 'dept', 'salary');
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

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		$registry->get('db')->update('staffinfo', array(
			'name' => ucwords($sanitized['name']),
			'phone' => $sanitized['phone'],
			'gender' => $sanitized['gender'],
			'address' => $sanitized['address'],
			'dept' => $sanitized['dept'],
			'salary' => $sanitized['salary']
		), array('id' => $sanitized['id']));


		$msg = 'Staff Profile of ' . ucwords($sanitized['name']) . ' was successfully deleted';
		$this->execute(array('action'=>'display', 'tmpl' => 'editStaffProfile', 'widget' => 'success', 'msg' => $msg));
	}

	public function fetchStaffData(Array $data)
	{
		# code...
		global $registry;

		$staffId = filter_var($data['staffId'], FILTER_SANITIZE_NUMBER_INT);

		$registry->get('session')->write('staffData', serialize(new Staff($staffId)));

		$this->execute(array('action'=>'render', 'tmpl' => 'editStaffProfile', 'widget' => '', 'msg' => ''));

	}

	public function subchargeStaff(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('staffId', 'reason', 'amount');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addStaffSubcharge', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		$staff = new Staff($sanitized['staffId']);
		$staff->subcharge(array(
			'date' => date('Y-m-d'),
			'amount' => $amount,
			'reason' => $reason
		));

		$msg = 'Staff ' . ucwords($stff->get('name')) . ' was successfully subcharged';
		$this->execute(array('action'=>'display', 'tmpl' => 'addStaffSubcharge', 'widget' => 'success', 'msg' => $msg));

	}

	public function generateStaffPaySlip($staffId)
	{
		# code...
		global $registry;

		$registry->get('session')->write('slipStaffId', $staffId);

		$this->execute(array('action'=>'render', 'tmpl' => 'generateStaffPaySlip', 'widget' => '', 'msg' => ''));
	}



	#end of class
}
