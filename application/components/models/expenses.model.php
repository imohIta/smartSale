<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ExpensesModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}



	public function addNew(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('date', 'category', 'description', 'amount');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addNewExpense', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		Expenses::addNew(array(
						'date' => $sanitized['date'],
						'description' => $sanitized['description'],
						'amount' => $sanitized['amount'],
                        'category' => $sanitized['category']
						));


		$msg = 'Expense successfully added';
		$this->execute(array('action'=>'display', 'tmpl' => 'addNewExpense', 'widget' => 'success', 'msg' => $msg));

	}

    public function setDate(Array $data){

        global $registry;
        $session = $registry->get('session');


        $day = filter_var($data['day'], FILTER_SANITIZE_STRING);
        $month = filter_var($data['month'], FILTER_SANITIZE_STRING);
        $year = filter_var($data['year'], FILTER_SANITIZE_STRING);

        $date = $year . '-' . $month . '-' . $day;
        $session->write('expensesDate', $date);
        $session->write('expensesDate-day', $day);
        $session->write('expensesDate-month', $month);
        $session->write('expensesDate-year', $year);


        $this->execute(array( 'action' => 'render', 'tmpl' => 'viewExpenses', 'widget' => '', 'msg' => '' ));


    }


	public function addNewCategory(Array $data)
	{
		# code...
		global $registry;
		$session = $registry->get('session');

		$requiredFields = array('category');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addNewExpensesCategory', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		Expenses::addNewCategory(array(
                        'category' => $sanitized['category']
						));


		$msg = 'New Expense Category successfully added';
		$this->execute(array('action'=>'display', 'tmpl' => 'addNewExpensesCategory', 'widget' => 'success', 'msg' => $msg));
	}





	#end of class
}
