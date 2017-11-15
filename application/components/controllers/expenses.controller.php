<?php

defined('ACCESS') or Error::exitApp();

class ExpensesController extends BaseController{

	protected $_urlAllowedMthds = array('render', 'addNewCategory', 'viewAll', 'addNew', 'summary');


	public function render(){
		global $registry;
		/*$newPriv = $registry->get('router')->get('params')[1];
	   	$this->_model->changePrivilege($newPriv);*/
	   	//$this->_model->execute();
	}


	public function viewAll()
	{
        if(isset($_POST['submit'])){
            $this->_model->setDate($_POST);
        }else{
            $this->_model->execute(array('action'=>'render', 'tmpl' => 'viewExpenses', 'widget' => '', 'msg' => ''));
        }


	}

	public function addNew()
	{
		if(isset($_POST['submit'])){
			$this->_model->addNew($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewExpense', 'widget' => '', 'msg' => ''));
		}
	}

	public function addNewCategory()
	{
        if(isset($_POST['submit'])){
			$this->_model->addNewCategory($_POST);
		}else{
			$this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewExpensesCategory', 'widget' => '', 'msg' => ''));
		}
	}


	public function summary()
	{
		# code...
		$this->_model->execute(array('action'=>'render', 'tmpl' => 'expensesSummary', 'widget' => '', 'msg' => ''));
	}



}
