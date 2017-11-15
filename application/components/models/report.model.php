<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class ReportModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();
	}

    public function setSalesReportParams(Array $data)
    {
        # code...
        global $registry;
        $session = $registry->get('session');

        $b_day = filter_var($data['b-day'], FILTER_SANITIZE_STRING);
        $b_month = filter_var($data['b-month'], FILTER_SANITIZE_STRING);
        $b_year = filter_var($data['b-year'], FILTER_SANITIZE_STRING);

        $e_day = filter_var($data['e-day'], FILTER_SANITIZE_STRING);
        $e_month = filter_var($data['e-month'], FILTER_SANITIZE_STRING);
        $e_year = filter_var($data['e-year'], FILTER_SANITIZE_STRING);

        $b_date = $b_year . '-' . $b_month . '-' . $b_day;
        $e_date = $e_year . '-' . $e_month . '-' . $e_day;

        $datetime1 = date_create($b_date);
        $datetime2 = date_create($e_date);
        $interval = date_diff($datetime1, $datetime2);

        if($interval->format('%R%') == '-' || $interval->format('%R%a') == '+0'){

            $msg = 'Sort Begin Date must be earlier than Sort End Date';
            $this->execute(array( 'action' => 'display', 'tmpl' => 'salesReport', 'widget' => 'error', 'msg' => $msg ));
            return;
        }

        $session->write('salesReportBeginDate', $b_date);
        $session->write('salesReportEndDate', $e_date);

        $session->write('salesReportDate-b-day', $b_day);
        $session->write('salesReportDate-b-month', $b_month);
        $session->write('salesReportDate-b-year', $b_year);

        $session->write('salesReportDate-e-day', $e_day);
        $session->write('salesReportDate-e-month', $e_month);
        $session->write('salesReportDate-e-year', $e_year);

        $this->execute(array( 'action' => 'render', 'tmpl' => 'salesReport', 'widget' => '', 'msg' => '' ));

    }

	public function setStockGroupReportParams(Array $data)
	{
		# code...
		global $registry;
        $session = $registry->get('session');

        $b_day = filter_var($data['b-day'], FILTER_SANITIZE_STRING);
        $b_month = filter_var($data['b-month'], FILTER_SANITIZE_STRING);
        $b_year = filter_var($data['b-year'], FILTER_SANITIZE_STRING);

        $e_day = filter_var($data['e-day'], FILTER_SANITIZE_STRING);
        $e_month = filter_var($data['e-month'], FILTER_SANITIZE_STRING);
        $e_year = filter_var($data['e-year'], FILTER_SANITIZE_STRING);

		
        $b_date = $b_year . '-' . $b_month . '-' . $b_day;
        $e_date = $e_year . '-' . $e_month . '-' . $e_day;

        $datetime1 = date_create($b_date);
        $datetime2 = date_create($e_date);
        $interval = date_diff($datetime1, $datetime2);

        if($interval->format('%R%') == '-' || $interval->format('%R%a') == '+0'){

            $msg = 'Sort Begin Date must be earlier than Sort End Date';
            $this->execute(array( 'action' => 'display', 'tmpl' => 'stockGroupReport', 'widget' => 'error', 'msg' => $msg ));
            return;
        }

        $session->write('salesGroupReportBeginDate', $b_date);
        $session->write('salesGroupReportEndDate', $e_date);

        $session->write('salesGroupReportDate-b-day', $b_day);
        $session->write('salesGroupReportDate-b-month', $b_month);
        $session->write('salesGroupReportDate-b-year', $b_year);

        $session->write('salesGroupReportDate-e-day', $e_day);
        $session->write('salesGroupReportDate-e-month', $e_month);
        $session->write('salesGroupReportDate-e-year', $e_year);

        $this->execute(array( 'action' => 'render', 'tmpl' => 'stockGroupReport', 'widget' => '', 'msg' => '' ));
	}




	#end of class
}
