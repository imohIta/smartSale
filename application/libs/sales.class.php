<?php

defined('ACCESS') || Error::exitApp();

/**
* Had to edit this class froma normal class to a decorator object that collects a staff class
*/
class Sales extends FuniObject
{
	protected $_id = null;
	protected $_date = null;
	protected $_time = null;
	protected $_item = null;
	protected $_qty = null;
	protected $_transId = null;
	protected $_price = null;
	protected $_payType = null;
	protected $_discount = null;
	protected $_staff = null;


	function __construct($obj)
	{

		# code...
		global $registry;
		$db = $registry->get('salesDb');

		# if passed parameter is itemId and not object of itemDetail
		if(!is_object($obj)) {

			$obj = $db->fetchSale($obj);

		}

		if ( !is_null($obj) && false !== $obj) {

			$this->_id = $obj->id;
			$this->_date = $obj->date;
			$this->_time = $obj->time;
			$this->_qty = $obj->qty;
			$this->_price = $obj->price;
			$this->_transId = $obj->transId;
			$this->_discount = $obj->discount;

			$this->_item = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($obj->codeNo));
			$this->_staff = new AppUser($obj->userId);
		}

		//$transPayDetails = $db->fetchTransPaymentDetails($this->_transId);

		# fetch payment details
	//	$this->_payType = $transPayDetails->payType;


		return $this;

	}



	public function getTime(){
		return timeToString($this->get('time'));
	}





	/***********************************
	 * Static Functions
	 *
	 */

	public static function getTotal($date, $userId){
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		# if logged in user is admin or store ...
		if(in_array($thisUser->get('activeAcct'), array(2,3,4))){
			# fetch total sales
			return $registry->get('salesDb')->calculateTotalSum($date);
		}else{
			# fetch sales made by this user only
			return $registry->get('salesDb')->calculateTotalSumByStaff($date, $userId);
		}

	}



	public static function getTotalCash($date, $userId){
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		# if logged in user is admin or store ...
		if(in_array($thisUser->get('activeAcct'), array(2,3,4))){
			# fetch total cash sales
			return $registry->get('salesDb')->calculateTotalCash($date);
		}else{
			# fetch cash sales made by this user only
			return $registry->get('salesDb')->calculateTotalCashByStaff($date, $userId);
		}
	}

	public static function getTotalPOS($date, $userId){
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		# if logged in user is admin or store ...
		if(in_array($thisUser->get('activeAcct'), array(2,3,4))){
			# fetch total pos sales
			return $registry->get('salesDb')->calculateTotalPOS($date);
		}else{
			# fetch  pos sales made by this user only
			return $registry->get('salesDb')->calculateTotalPOSByStaff($date, $userId);
		}
	}

	public static function fetchAll($date, $staffId = null){
		global $registry;
		global $registry;
		$session = $registry->get('session');
		$thisUser = unserialize($session->read('thisUser'));

		# if logged in user is admin or store ...
		if(in_array($thisUser->get('activeAcct'), array(2,3,4))){
			# fetch all sales
			return $registry->get('salesDb')->fetchAll($date);
		}else{
			# fetch all sales by user
			return $registry->get('salesDb')->fetchAllByStaff($date, $staffId);
		}

	}

	public static function addNew(Array $data){
		global $registry;
		$registry->get('salesDb')->addSale($data);
	}

	public static function addTransDetails(Array $data){
		global $registry;
		$registry->get('salesDb')->addTransDetails($data);
	}

	public static function fetchSorted(Array $data)
	{
		# code...

		global $registry;
		$query = 'select * from sales where date between :beginDate and :endDate and userId = :userId';
		return $registry->get('db')->query($query, array(
			'beginDate' => $data['beginDate'],
			'endDate' => $data['endDate'],
			'userId' => $data['userId']
		), true);
	}


	public static function fetchSortedTransactions(Array $data)
	{
		# code...
		global $registry;
		$query = 'select * from transactions where date between :beginDate and :endDate';
		if(isset($data['userId'])){
			$query .= ' and userId = :userId';
		}

		$params = array(
			'beginDate' => $data['beginDate'],
			'endDate' => $data['endDate']
		);

		if(isset($data['userId'])){
			$params['userId'] = $data['userId'];
		}
		return $registry->get('db')->query($query, $params, true);
	}

	public static function getTotalForMonth($month, $year)
	{
		# code...
		global $registry;
		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		return $registry->get('db')->bindFetch('select sum(grandTotal) as total from transactions where date between :beginDate and :endDate', array('beginDate' => $beginDate, 'endDate' => $endDate), array('total'))['total'];

	}


	public static function fetchByTransId($transId)
	{
		# code...
		global $registry;
		return $registry->get('db')->query('select * from sales where transId = :transId', array('transId' => $transId), true);
	}

	public function fetchSalesByStockCategory(Array $data)
	{
		# code...
		global $registry;
		return $registry->get('db')->query('select * from sales where codeNo in ( select codeNo from stockCard where groupId = :categoryId ) and date between :beginDate and :endDate', array('categoryId' => $data['categoryId'], 'beginDate' => $data['beginDate'], 'endDate' => $data['endDate']), true);
	}

	public static function fetchTotalSalesForDateRange(Array $data)
	{
		# code...
		global $registry;
		$response = $registry->get('db')->bindFetch('select sum(amount) as totalSales, sum(discount) as totalDiscount from sales where date between :beginDate and :endDate', array('beginDate' => $data['beginDate'], 'endDate' => $data['endDate']), array('totalSales', 'totalDiscount'));

		$response['totalSales'] = is_null($response['totalSales']) ? 0 : $response['totalSales'];
		$response['totalDiscount'] = is_null($response['totalDiscount']) ? 0 : $response['totalDiscount'];

		return $response;

	}

	public static function fetchSalesTotalByStockCategory(Array $data)
	{
		# code...
		global $registry;
		$response =  $registry->get('db')->bindFetch('select sum(amount) as totalSales, sum(discount) as totalDiscount from sales where codeNo in ( select codeNo from stockCard where groupId = :categoryId ) and date between :beginDate and :endDate', array('categoryId' => $data['categoryId'], 'beginDate' => $data['beginDate'], 'endDate' => $data['endDate']), array('totalSales', 'totalDiscount'));

		$response['totalSales'] = is_null($response['totalSales']) ? 0 : $response['totalSales'];
		$response['totalDiscount'] = is_null($response['totalDiscount']) ? 0 : $response['totalDiscount'];

		return $response;

	}

	public static function fetchDocket($userId, $currentInvioceNo){
		global $registry;
		return $registry->get('salesDb')->fetchDocket($userId, $currentInvioceNo);
	}

	public static function clearDocket($transId)
	{
		# code...
		global $registry;

		$registry->get('salesDb')->clearDocket($transId);
	}

	public static function deleteFromDocket($docketId)
	{
		# code...
		global $registry;

		$registry->get('salesDb')->deleteDocketItem($docketId);
	}

#end of class
}
