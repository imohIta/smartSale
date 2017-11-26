<?php

    defined('ACCESS') or Error::exitApp();

    class StockController extends BaseController{

        protected $_urlAllowedMthds = array('render','removeItem', 'addNew', 'setItemByCodeNo', 'suggestByName', 'setItemById','fetchDocketItemById', 'viewAll', 'removeBadItem', 'fetchStockItemByCodeNo', 'changeItemPrice', 'viewBadItems', 'stockCard', 'createCategory', 'deleteCategory', 'viewCategories', 'addBrand', 'deletePerfumeBrand');


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
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewStock', 'widget' => '', 'msg' => ''));
            }
        }


        public function setItemByCodeNo()
        {
            # code...
            if(isset($_POST['itemQuery'])){
                $this->_model->setItemByCodeNo($_POST);
            }
//            else{
//                $this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewStock', 'widget' => '', 'msg' => ''));
//            }
        }

        public function suggestByName(){
            global $registry;
            $this->_model->suggestByName($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam
            (0)[1], $registry->get('router')->getParam
            (0)[2]);
        }


        public function setItemById(){
            global $registry;
            $this->_model->setItemById($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam(0)[1]);
        }

        public function fetchDocketItemById(){
            global $registry;
            $this->_model->fetchDocketItemById($registry->get('router')->getParam(0)[0]);
        }

        public function fetchStockItemByCodeNo(){
            global $registry;
            $this->_model->fetchStockItemByCodeNo($registry->get('router')->getParam(0)[0], $registry->get('router')->getParam(0)[1]);
        }


        public function removeBadItem()
        {
            # code...
            # code...
            if(isset($_POST['submit'])){
                $this->_model->removeBadItem($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'removeBadItem', 'widget' => '', 'msg' =>
                    ''));
            }
        }



        public function viewAll(){
            $this->_model->execute(array('action'=>'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => ''));
        }




        public function viewBadItems()
        {
            # code...
            $this->_model->execute(array('action'=>'render', 'tmpl' => 'viewBadItems', 'widget' => '', 'msg' => ''));
        }

        public function stockCard()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->proccessStockCard($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'stockCard', 'widget' => '', 'msg' =>
                    ''));
            }
        }

        public function createCategory()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->createCategory($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'createStockCategory', 'widget' => '', 'msg' =>
                    ''));
            }
        }

        public function deleteCategory()
        {
            global $registry;
            $this->_model->deleteCategory($registry->get('router')->getParam(0)[0]);
        }

        public function viewCategories()
        {
            # code...
            $this->_model->execute(array('action'=>'render', 'tmpl' => 'viewStockCategories', 'widget' => '', 'msg' => ''));
        }

        public function addBrand()
        {
            # code...
            if(isset($_POST['submit'])){
                $this->_model->addBrand($_POST);
            }else{
                $this->_model->execute(array('action'=>'render', 'tmpl' => 'addNewPerfumeBrand', 'widget' => '', 'msg' =>
                    ''));
            }
        }

        public function deletePerfumeBrand()
        {
            # code...
            global $registry;
            $this->_model->deletePerfumeBrand($registry->get('router')->getParam(0)[0]);
        }


    }
