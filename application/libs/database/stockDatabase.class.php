<?php
namespace application\libs\database;
use \PDO;
use core\libs\Database as Db;

defined('ACCESS') || AppError::exitApp();

class StockDatabase extends Db{


	public function suggestByName($queryString)
	{
		# code...
		$query = 'select sc.*, cs.qty from stockCard as sc inner join currentStock as cs on sc.codeNo = cs.CodeNo where sc.name like :name or sc.codeNo like :codeNo limit 6';
		$st = $this->_driver->prepare($query);
		$st->bindValue('name', "%$queryString%");
		$st->bindValue('codeNo', "%$queryString%");
		return ($st->execute()) ? $st->fetchAll(PDO::FETCH_OBJ) : array();

	}

	public function createCard(Array $data){

		# insert into stock card
		parent::insert('stockCard',
		array(
			'codeNo' => $data['codeNo'],
			'name' => $data['itemName'],
			'wholesalePrice'    => $data['wholesalePrice'],
			'costPrice'      => $data['costPrice'],
			'retailPrice' => $data['retailPrice'],
			'tax' => (int)$data['tax'],
			'groupId' => $data['groupId']
		));

		# insert into current stock
		parent::insert('currentStock', array('codeNo' => $data['codeNo'], 'qty' => 0));

		return $this;
	}

	public function updateStockCard(Array $data){

		parent::update('stockCard',
		array(
            'name' => $data['itemName'],
            'wholesalePrice'    => $data['wholesalePrice'],
            'costPrice'      => $data['costPrice'],
            'retailPrice' => $data['retailPrice'],
            'tax' => $data['tax'],
            'groupId' => $data['groupId']
			), array('codeNo' => $data['codeNo']));
	}


	public function fetchStockItem($id){
		# code...
		$query = 'select sc.*, cs.qty from stockCard as sc inner join currentStock as cs on sc.codeNo = cs.CodeNo where sc.id = :id';
		return parent::query($query, array('id' => $id));
	}


	public function fetchStockByCodeNo($codeNo){
		$query = 'select sc.*, cs.qty from stockCard as sc inner join currentStock as cs on sc.codeNo = cs.CodeNo where sc.codeNo = :codeNo';
		return parent::query($query, array('codeNo' => $codeNo));
	}

	public function addToDocket(Array $data){

		return parent::insert('purchaseDocket', array(
				'date'     => $data[ 'date' ],
				'itemName' => $data[ 'name' ],
				'codeNo'   => $data[ 'codeNo' ],
				'price'    => $data[ 'price' ],
				'qty'      => $data[ 'qty' ],
				'staffId'  => $data[ 'staffId' ]
		));
	}

	public function checkIfItemInDocket(Array $data){
		$query = 'select id from purchaseDocket where codeNo = :codeNo and staffId = :staffId';
		$response = parent::bindFetch($query, array('codeNo' => $data['codeNo'], 'staffId' => $data['staffId']), array
		('id'));
		return $response['id'] > 0 ? true : false;
	}

	public function fetchDocketItem($docket, $docketId){
		$query = 'select * from purchaseDocket where id = :id';
		return $response = parent::query($query, array('id' => $docketId));
	}

	public function getItemQtyInDocket(Array $data){
		$query = 'select qty from purchaseDocket where codeNo = :codeNo and staffId = :staffId';
		return parent::bindFetch($query, array('codeNo' => $data['codeNo'], 'staffId' => $data['staffId']), array
		('qty'));
	}

	public function updateDocketItem(Array $data){

		parent::update('purchaseDocket', array( 'qty' => $data[ 'qty' ], 'itemName' => $data[ 'name' ], 'price' => $data[ 'price' ] ), array( 'codeNo' => $data[ 'codeNo' ], 'staffId' => $data[ 'staffId' ] ));

		return $this;
	}

	public function fetchPurchaseDocket($date, $staffId){
		$query = 'select * from purchaseDocket where staffId = :staffId';
		return parent::query($query, array('staffId' => $staffId), true);
	}

	public function deleteDocketItem($docket, $docketItemId){
		parent::delete($docket, array('id' => $docketItemId));
		return $this;
	}

	public function clearDocket($docket, $userId){
		parent::delete($docket, array('staffId' => $userId));
		return $this;
	}

	public function checkIfExist($codeNo){
		$query = 'select id from stockCard where codeNo = :codeNo';
		$response  = parent::bindFetch($query, array('codeNo' => $codeNo), array('id'));
		return $response['id'] > 0 ? true : false;
	}


	public function insertIntoPurchases(Array $data){
		return parent::insert('purchases', array(
				'date' => $data['date'],
				'supplierId' => $data['supplierId'],
				'transId' => $data['transId'],
				'codeNo' => $data['codeNo'],
				'rate' => $data['rate'],
				'qty' => $data['qty'],
				'staffId' => $data['staffId']
		));
	}


	public function fetchAll(){
		$query = 'select sc.*, cs.qty from stockCard as sc inner join currentStock as cs on sc.codeNo = cs.CodeNo';
		return parent::query($query, array(), true);
	}

	public function updateQty($qty, $id){
		return parent::update('currentStock', array('qty' => $qty), array('id' => $id));
	}





#end of class
}
