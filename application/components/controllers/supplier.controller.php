<?php

defined('ACCESS') or Error::exitApp();

class SupplierController extends BaseController{

	protected $_urlAllowedMthds = array('render', 'addNew', 'viewAll', 'purchaseHistory', 'editInfo', 'fetchSupplierInfo');


	public function render(){
		global $registry;
		/*$newPriv = $registry->get('router')->get('params')[1];
	   	$this->_model->changePrivilege($newPriv);*/
	   	//$this->_model->execute();
	}


	public function viewAll()
	{

		$this->_model->execute(array('action'=>'render', 'tmpl' => 'viewAllSuppliers', 'widget' => '', 'msg' => ''));
	}

	public function addNew()
	{
		if(isset($_POST['submit'])){
			$this->_model->addNew($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewSupplier', 'widget' => '', 'msg' => ''));
		}
	}

	public function delete()
	{
		# code...
		if(isset($_POST['submit'])){
			$this->_model->delete($_POST);
		}
	}

	public function editInfo()
	{
		# code...
		if(isset($_POST['edit'])){
			$this->_model->editSupplierInfo($_POST);
		}elseif(isset($_POST['delete'])){
			$this->_model->deleteSupplierInfo($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'editSupplierInfo', 'widget' => '', 'msg' => ''));
		}
	}

	public function fetchSupplierInfo()
	{
		# code...
		global $registry;
		$this->_model->fetchSupplierInfo($registry->get('router')->getParam(0)[0]);
	}

	public function purchaseHistory()
	{
		# code...
		global $registry;
		if(isset($registry->get('router')->getParam(0)[0])){
			$this->_model->fetchPurchaseHistory($registry->get('router')->getParam(0)[0]);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'supplierPurchaseHistory', 'widget' => '', 'msg' => ''));
		}

	}


}
