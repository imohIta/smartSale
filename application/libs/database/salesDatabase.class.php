<?php
namespace application\libs\database;
use \PDO;
use core\libs\Database as Db;

defined('ACCESS') || AppError::exitApp();

class SalesDatabase extends Db{


	public function fetchSale($id)
	{
		# code...
		$query = 'select * from sales where id = :id';
		return parent::query($query, array('id' => $id));

	}

	public function addNew(Array $data){
//		parent::insert('sales', array('itemName' => $data['itemName'], 'codeNo' => $data['codeNo'], 'qtyInStock' => $data['qtyInStock'],'price' => $data['price']));
//		return $this;
	}

	public function calculateTotalSumByStaff($date, $userId){
		$query = "select sum(grandTotal) as total from transactions where date = :date and userId = :userId";
		$response = parent::bindFetch($query, array('date' => $date, 'userId' => $userId), array('total'));
		return $response['total'];
	}

	public function calculateTotalSum($date){
		$query = "select sum(grandTotal) as total from transactions where date = :date";
		$response = parent::bindFetch($query, array('date' => $date), array('total'));
		return $response['total'];
	}

	public function calculateTotalCash($date){
		$query = 'select sum(grandTotal) as total from transactions where date = :date and payType = 1';
		$response = parent::bindFetch($query, array('date' => $date), array('total'));
		return $response['total'];
	}

	public function calculateTotalCashByStaff($date, $userId){
		$query = 'select sum(grandTotal) as total from transactions where date = :date and userId = :userId and payType = 1';
		$response = parent::bindFetch($query, array('date' => $date, 'userId' => $userId), array('total'));
		return $response['total'];
	}

	public function calculateTotalPOS($date){
		$query = 'select sum(grandTotal) as total from transactions where date = :date and payType = 2';
		$response = parent::bindFetch($query, array('date' => $date), array('total'));
		return $response['total'];
	}

	public function calculateTotalPOSByStaff($date, $userId){
		$query = 'select sum(grandTotal) as total from transactions where date = :date and userId = :userId and payType = 2';
		$response = parent::bindFetch($query, array('date' => $date, 'userId' => $userId), array('total'));
		return $response['total'];
	}


	public function fetchAll($date){
		$query = 'select * from sales where date = :date';
		$params = array('date' => $date);
		return parent::query($query, $params, true);
	}

	public function fetchAllByStaff($date, $userId){
		$query = 'select * from sales where date = :date';
		$params = array('date' => $date);

		if(!is_null($userId)){
			$query .= ' and userId = :userId';
			$params['userId'] =  $userId;
		}
		return parent::query($query, $params, true);
	}


	public function addSale(Array $data){
		parent::insert('sales', array(
				'date' => $data['date'],
				'time' => $data['time'],
				'transId' => $data['transId'],
				'codeNo' => $data['codeNo'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'userId' => $data['userId']
		));
		return $this;
	}


	public function addTransDetails(Array $data){
		parent::insert('transactions', array(
			'date' => $data['date'],
			'transId' => $data['transId'],
			'subTotal' => $data['subTotal'],
			'discount' => $data['discount'],
			'grandTotal' => $data['grandTotal'],
			'payType' => $data['payType'],
			'customerDetails' => $data['customerDetails'],
			'shippingDetails' => $data['shippingDetails'],
			'userId' => $data['userId']
		));
	}

	public function fetchTransPaymentDetails($transId){
		$query = 'select * from transactions where transId = :transId';
		return parent::query($query, array('transId' => $transId));
	}

	public function fetchDocket($staffId, $currentInvioceNo){
		$query = 'select * from salesDocket where staffId = :staffId and transId = :transId';
		return parent::query($query, array('staffId' => $staffId, 'transId' => $currentInvioceNo), true);
	}

	public function checkIfItemInDocket(Array $data){
		$query = 'select id from salesDocket where codeNo = :codeNo and transId = :transId';
		$response = parent::bindFetch($query, array('codeNo' => $data['codeNo'], 'transId' => $data['transId']), array
		('id'));
		return $response['id'] > 0 ? true : false;
	}

	public function getItemInDocket(Array $data){
		$query = 'select * from salesDocket where codeNo = :codeNo and transId = :transId';
		return parent::query($query, array('codeNo' => $data['codeNo'], 'transId' => $data['transId']));
	}

	public function updateDocketItem(Array $data){

		parent::update('salesDocket', array( 'qty' => $data[ 'qty' ], 'discount' => $data['discount'], 'total' => $data['total'] ), array( 'codeNo' => $data[ 'codeNo' ], 'transId' => $data[ 'transId' ] ));

		return $this;
	}

	public function addToDocket (Array $data){

		return parent::insert('salesDocket', array(
				'date'     => $data[ 'date' ],
				'transId' => $data[ 'transId' ],
				'codeNo'   => $data[ 'codeNo' ],
				'price'    => $data[ 'price' ],
				'qty'      => $data[ 'qty' ],
				'discount'      => $data[ 'discount' ],
				'total' => $data['total'],
				'staffId'  => $data[ 'staffId' ]
		));
	}

	public function clearDocket($transId){
		parent::delete('salesDocket', array('transId' => $transId));
		return $this;
	}

	public function fetchDocketItem($docketId){
		$query = 'select * from salesDocket where id = :id';
		return $response = parent::query($query, array('id' => $docketId));
	}

	public function deleteDocketItem($docketItemId){
		parent::delete('salesDocket', array('id' => $docketItemId));
		return $this;
	}


#end of class
}
