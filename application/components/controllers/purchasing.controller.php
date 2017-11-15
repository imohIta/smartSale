<?php

    defined('ACCESS') or Error::exitApp();

    class PurchasingController extends BaseController{

        protected $_urlAllowedMthds = array('render','addNew', 'summary', 'addTemp',  'deleteDocketItem', 'editDocketItem', 'clearDocket', 'addItemsToStock', 'fetchPrevious', 'fetchPreviousDetails', 'summary');


        public function render(){
            $this->_model->attach(new GeneralView());
            $this->_model->execute();
        }

        public function addNew()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->addNew($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewPurchase', 'widget' => '', 'msg' => ''));
            }
        }


        public function summary()
        {
            if(isset($_POST['submit'])){
                $this->_model->setSummaryDuration($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'puchasesSummary', 'widget' => '', 'msg' => ''));
            }
        }


        public function addTemp()
        {
            global $registry;

            $data = json_decode($_POST['data'], true);
            $this->_model->addTemp($data);
        }

        public function editDocketItem()
        {
            global $registry;

            $data = json_decode($_POST['data'], true);
            $this->_model->editDocketItem($data);
        }


        public function deleteDocketItem()
        {
            global $registry;
            $this->_model->deleteDocketItem($registry->get('router')->getParam(0)[0]);
        }

        public function clearDocket(){
            global $registry;
            $this->_model->clearDocket();
        }

        public function addItemsToStock(){
            global $registry;
            $data = json_decode($_POST['data'], true);
            $this->_model->addItemsToStock($data);
        }

        public function fetchPrevious()
        {
            # code...
            global $registry;
            $this->_model->fetchPrevious($registry->get('router')->getParam(0)[0]);
        }

        public function fetchPreviousDetails()
        {
            # code...
            global $registry;
            $this->_model->fetchPreviousDetails($registry->get('router')->getParam(0)[0]);
        }



    }
