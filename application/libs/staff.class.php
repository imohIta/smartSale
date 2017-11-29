<?php

defined('ACCESS') || Error::exitApp();

/**
* Had to edit this class froma normal class to a decorator object that collects a staff class
*/
class Staff extends FuniObject
{
	protected $_id = null;
	protected $_name = null;
	protected $_phone = null;
	protected $_gender = null;
	protected $_address = null;
	protected $_dept = null;
    protected $_salary = null;
	protected $_role = null;


	function __construct($obj)
	{

		# code...
		global $registry;

		# if staffId only is passed
		if(!is_object($obj)){

			# fetch staff details
			$obj = $registry->get('db')->query('select * from staffinfo where id = :id', array('id' => $obj));

		}

		if ( !is_null($obj) && false !== $obj) {

			$this->_id = $obj->id;
			$this->_name = $obj->name;
			$this->_phone = $obj->phone;
			$this->_gender = $obj->gender;
            $this->_address = $obj->address;
            $this->_dept = $obj->dept;
            $this->_salary = $obj->salary;
			$this->_role = $this->_getRole();
		}

	}

	private function _getRole()
	{
		# code...
		global $registry;
        $query = 'select privilege from privileges where id = :privId';
		$response = $registry->get('db')->bindFetch($query, array('privId' => $this->_dept), array('privilege'));
		return $response['privilege'];
	}

	public function subcharge(Array $data)
	{
		# code...
		global $registry;

		$registry->get('db')->insert('subcharges', array(
			'date' => $data['date'],
			'staffId' => $this->_id,
			'amount' => $data['amount'],
			'reason' => $data['reason']
		));
	}

	public function getSubcharges($month, $year)
	{
		# code...
		global $registry;

		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= $month == 2 ? '28' : '31';

		return $registry->get('db')->query('select * from subcharges where staffId = :staffId and date between :beginDate and :endDate', array('staffId' => $this->_id, 'beginDate' => $beginDate, 'endDate' => $endDate), true);

	}

	// public function getTotalSubcharges($month, $year)
	// {
	// 	# code...in
	// 	global $registry;
	//
	// 	$beginDate = $year . '-' . $month . '-01';
	// 	$endDate = $year . '-' . $month . '-';
	// 	$endDate .= $month == 2 ? '28' : '31';
	//
	// 	$result = $registry->get('db')->bindFetch('select sum(amount) as total from subcharges where staffId = :staffId and date between :beginDate and :endDate', array('staffId' => $this->_id, 'beginDate' => $beginDate, 'endDate' => $endDate), array('total'));
	//
	// 	return $result['total'];
	// }

   /********************************
    * ******************************
    *  Static Functions
	********************************/


    public static function fetchAll()
    {
        # code...
        global $registry;
        return $registry->get('db')->query('select * from staffinfo', array(), true);
    }



    public static function fetchStaffSubcharges($month, $year){
    	global $registry;
		$beginDate = $year . '-' . $month . '-' . '01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= ($month == 2) ? '28' : '31';

    	return $registry->get('db')->query('select * from subcharges where date between :beginDate and :endDate', array(
			'beginDate' => $beginDate,
			'endDate' => $endDate
		), true);

    }

	public static function fetchTotalSalaries()
	{
		# code...
		global $registry;
		$result = $registry->get('db')->bindFetch('select sum(salary) as total from staffinfo', array(), array('total'));
		return $result['total'];
	}

	public static function fetchTotalSubcharges($month, $year)
	{
		# code...
		global $registry;
		$beginDate = $year . '-' . $month . '-' . '01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		$result = $registry->get('db')->bindFetch('select sum(amount) as total from subcharges where date between :beginDate and :endDate', array(
			'beginDate' => $beginDate,
			'endDate' => $endDate
		), array('total'));

		return $result['total'];
	}




#end of class
}
