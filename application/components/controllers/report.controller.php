<?php

defined('ACCESS') or Error::exitApp();

class ReportController extends BaseController{

	protected $_urlAllowedMthds = array('render', 'sales', 'stockGroup', 'outOfStock');


	public function render(){
		global $registry;
		/*$newPriv = $registry->get('router')->get('params')[1];
	   	$this->_model->changePrivilege($newPriv);*/
	   	//$this->_model->execute();
	}

	public function changePrivilege(){
		global $registry;
		$newPriv = $registry->get('router')->get('params')[1];
	   	$this->_model->changePrivilege($newPriv);
	}

	public function sales()
	{

        if(isset($_POST['submit'])){
            $this->_model->setSalesReportParams($_POST);
        }else {
            $this->_model->execute(array( 'action' => 'render', 'tmpl' => 'salesReport', 'widget' => '', 'msg' => '' ));
        }
	}


    public function stockGroup()
	{

        if(isset($_POST['submit'])){
            $this->_model->setStockGroupReportParams($_POST);
        }else {
            $this->_model->execute(array( 'action' => 'render', 'tmpl' => 'stockGroupReport', 'widget' => '', 'msg' => '' ));
        }
	}

	public function outOfStock()
	{
		# code...
		$this->_model->execute(array( 'action' => 'render', 'tmpl' => 'outOfStockReport', 'widget' => '', 'msg' => '' ));
	}

}
