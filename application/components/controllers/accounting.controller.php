<?php

    defined('ACCESS') or Error::exitApp();

    class AccountingController extends BaseController{

        protected $_urlAllowedMthds = array('render','profitNLoss', 'incomingCash', 'addIncomingCash');


        public function render(){
            $this->_model->attach(new GeneralView());
            $this->_model->execute();
        }

        public function profitNLoss()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->setProfitNLossDate($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'profitNLoss', 'widget' => '', 'msg' => ''));
            }
        }


        public function addIncomingCash()
        {
            if(isset($_POST['submit'])){
                $this->_model->addIncomingCash($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'addIncomingCash', 'widget' => '', 'msg' => ''));
            }
        }

        public function incomingCash(){
            global $registry;
            $this->_model->execute(array('action'=>'render', 'tmpl' => 'viewIncomingCash', 'widget' => '', 'msg' => ''));
        }



    }
