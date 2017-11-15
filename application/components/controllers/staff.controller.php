<?php

defined('ACCESS') or Error::exitApp();

class StaffController extends BaseController{

	protected $_urlAllowedMthds = array('render', 'addProfile', 'viewAll', 'editProfile', 'viewSubcharges', 'subchargeStaff', 'generatePaySlip', 'generateStaffPaySlip');


	public function render(){
		global $registry;
	}


	public function viewAll()
	{

		$this->_model->execute(array('action'=>'render', 'tmpl' => 'viewAllCustomers', 'widget' => '', 'msg' => ''));
	}

	public function addProfile()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->addProfile($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addStaffProfile', 'widget' => '', 'msg' => ''));
		}
	}

	public function editProfile()
	{
		if(isset($_POST['submit'])){
			if(trim($_POST['submit']) == "Fetch Data"){
				$this->_model->fetchStaffData($_POST);
			}else{
				$this->_model->editProfile($_POST);
			}

		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'editStaffProfile', 'widget' => '', 'msg' => ''));
		}
	}


	public function viewSubcharges()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->setSubchargesDate($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'viewStaffSubcharges', 'widget' => '', 'msg' => ''));
		}
	}

	public function subchargeStaff()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->subchargeStaff($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addStaffSubcharge', 'widget' => '', 'msg' => ''));
		}
	}

	public function generatePaySlip()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'generatePaySlip', 'widget' => '', 'msg' => ''));

	}

	public function generateStaffPaySlip()
	{
		# code...
		global $registry;
		$this->_model->generateStaffPaySlip($registry->get('router')->getParam(0)[0]);
	}



}
