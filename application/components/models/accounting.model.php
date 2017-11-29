<?php
    /**
     *
     *
     */
    defined('ACCESS') || Error::exitApp();

    class AccountingModel extends BaseModel
    {

        protected $_param;
        protected $_viewParams;

        public function execute(Array $options = array( 'action' => 'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => '' ))
        {
            $this->_viewParams = $options;
            $this->notify();
        }


        public function setProfitNLossDate(Array $data)
        {
            global $registry;
            $session = $registry->get('session');


            $month = filter_var($data['month'], FILTER_SANITIZE_STRING);
            $year = filter_var($data['year'], FILTER_SANITIZE_STRING);

            $session->write('PL-month', $month);
            $session->write('PL-year', $year);


            $this->execute(array( 'action' => 'render', 'tmpl' => 'profitNLoss', 'widget' => '', 'msg' => '' ));

        }

        public function addIncomingCash(Array $data)
        {
            # code...
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));


            $requiredFields = array('date', 'source', 'amount');
    		# get all form fields into an array...
    		$formFields = array();
    		foreach ($data as $key => $value) {
    			# code...
    			$formFields[] = $key;
    		}

    		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

    		#if some required fields where not filled
    		if($checkReq->status == 'error'){
    			$this->execute(array('action'=>'display', 'tmpl' => 'addIncomingCash', 'widget' => 'error', 'msg' => $checkReq->msg));
    		}

    		#sanitize each of the fields & append to sanitized array
    		$sanitized = array();
    		foreach ($formFields as $key) {
    			# code...

    			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

    			$sanitized[$key] = $$key;

    		}


            $registry->get('db')->insert('incomingcash', array(
                'date' => $sanitized['date'],
                'source' => $sanitized['source'],
                'amount' => $sanitized['amount'],
                'staffId'=> $thisUser->get('id')
            ));

            $msg = 'Incoming Cash successfully added';
    		$this->execute(array('action'=>'display', 'tmpl' => 'addIncomingCash', 'widget' => 'success', 'msg' => $msg));
        }


        # end of class
    }
