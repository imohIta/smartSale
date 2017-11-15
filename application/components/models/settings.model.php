<?php
    /**
     *
     *
     */
    defined('ACCESS') || Error::exitApp();

    class SettingsModel extends BaseModel
    {

        protected $_param;
        protected $_viewParams;

        public function execute(Array $options = array( 'action' => 'render', 'tmpl' => 'settings', 'widget' => '', 'msg' => '' ))
        {
            $this->_viewParams = $options;
            $this->notify();
        }

        public function proccessSettings(... $data)
        {
            # code...
            global $registry;

            # check required fields
            $requiredFields = array()

            # sanitize inputs
            # update database

            $registry->get('db')->update('settings', json_encode(array(
                'alertStatus' => $alertStus,
                'zbajhbjasbda' => ''
            )), array('id' => 1))

            $msg = 'Settings Saved successfully';
            $this->execute(array( 'action' => 'display', 'tmpl' => 'settings', 'widget' => 'success', 'msg' => '' ))
        }



        # end of class
    }
