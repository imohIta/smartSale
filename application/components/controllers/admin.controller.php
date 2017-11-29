<?php

defined('ACCESS') or Error::exitApp();

class AdminController extends BaseController{

	protected $_urlAllowedMthds = array('resetApp', 'sync');


	public function render(){
		$this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}


	public function resetApp()
	{

		//if(isset($_POST['submit'])){
			$this->_model->resetApp($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam(0)[1]);
		// }else{
		// 	$this->_model->execute(array('action'=>'render', 'tmpl' => 'resetApp', 'widget' => '', 'msg' => ''));
		// }
	}


	public function sync()
	{
		# code...
		$this->_model->runSynManager();
	}





}
