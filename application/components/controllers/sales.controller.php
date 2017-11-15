<?php

    defined('ACCESS') or Error::exitApp();

    class SalesController extends BaseController{

        protected $_urlAllowedMthds = array('render', 'addNew', 'viewAll', 'reverse', 'addToDocket', 'clearDocket', 'deleteDocketItem', 'completeSale', 'viewTransactions', 'sort', 'summary', 'fetchTransactionsOnHold');


        public function render(){
            $this->_model->attach(new GeneralView());
            $this->_model->execute();
        }

        public function addNew()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->addToStore($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'newSale', 'widget' => '', 'msg' => ''));
            }
        }


        public function viewAll(){
            if(isset($_POST['submit'])){
                $this->_model->setSalesDate($_POST);
            }else {
                $this->_model->execute(array( 'action' => 'render', 'tmpl' => 'viewTransactions', 'widget' => '', 'msg' =>
                    '' ));
            }
        }


        public function addToDocket()
        {
            global $registry;

            $data = json_decode($_POST['data'], true);
            $this->_model->addToDocket($data);
        }


        public function clearDocket(){
            global $registry;
            $this->_model->clearDocket();
        }

        public function deleteDocketItem()
        {
            global $registry;
            $this->_model->deleteDocketItem($registry->get('router')->getParam(0)[0]);
        }

        public function completeSale(){
            global $registry;

            $data = json_decode($_POST['data'], true);
            $this->_model->completeSale($data);
        }


        public function sort(){
            if(isset($_POST['submit'])){
                $this->_model->setSortParams($_POST);
            }else {
                $this->_model->execute(array( 'action' => 'render', 'tmpl' => 'sortSales', 'widget' => '', 'msg' => '' ));
            }
        }


        public function summary()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->setSortParams($_POST);
            }else {
                $this->_model->execute(array( 'action' => 'render', 'tmpl' => 'salesSummary', 'widget' => '', 'msg' => '' ));
            }
        }

        public function fetchTransactionsOnHold()
        {
            # code...
            $this->_model->fetchTransactionsOnHold();
        }


    }
