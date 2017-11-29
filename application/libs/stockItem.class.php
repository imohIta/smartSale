<?php

defined('ACCESS') || Error::exitApp();

/**
* Had to edit this class froma normal class to a decorator object that collects a staff class
*/
class StockItem extends FuniObject
{
	protected $_id = null;
	protected $_name = null;
	protected $_codeNo = null;
	protected $_costPrice = null;
	protected $_wholesalePrice = null;
	protected $_retailPrice = null;
	protected $_qtyInStock = null;
	protected $_tax = null;
	protected $_groupId = null;
	protected $_brandId = null;


	function __construct($obj)
	{

		# code...
		global $registry;
		$db = $registry->get('stockDb');

		# if passed parameter is itemId and not object of itemDetail
		if(!is_object($obj)) {

			$obj = $db->fetchStockItem($obj);

		}

		if ( !is_null($obj) && false !== $obj) {

			$this->_id = $obj->id;
			$this->_name = $obj->name;
			$this->_codeNo = $obj->codeNo;
			$this->_wholesalePrice = $obj->wholesalePrice;
			$this->_retailPrice = $obj->retailPrice;
			$this->_costPrice = $obj->costPrice;
			$this->_qtyInStock = $obj->qty;
			$this->_groupId = $obj->groupId;
			$this->_brandId = $obj->brandId;
			$this->_tax = $obj->tax;
		}

		return $this;

	}

	public function getGroup()
	{
		# code...
		global $registry;
		return $registry->get('db')->bindFetch('select name from stockcategories where id = :id', array('id' => $this->_groupId), array('name'))['name'];
	}

	public function getBrand()
	{
		# code...
		global $registry;
		return $registry->get('db')->bindFetch('select name from perfumebrands where id = :id', array('id' => $this->_brandId), array('name'))['name'];
	}

	public function updateCard(Array $data){
		global $registry;

		$registry->get('stockDb')->updateStockCard($data);
		return $this;

	}

	public function changePrice(Array $data)
	{
		# code...
		global $registry;
		$this->_price = $data['price'];
		$registry->get('stockDb')->updatePrice($data);
		return $this;
	}

	public function increaseQty($qty){
		global $registry;

		$newQty = $this->_qtyInStock + $qty;
		$registry->get('stockDb')->updateQty($newQty, $this->_codeNo);
		$this->_qtyInStock = $newQty;

	}


	public function reduceQty($qty){
		global $registry;

		$newQty = $this->_qtyInStock - $qty;
		$this->_qtyInStock = $newQty;
		$registry->get('stockDb')->updateQty($newQty, $this->_codeNo);
	}

	public function updateCostPrice($price)
	{
		# code...

		global $registry;
		$registry->get('db')->update('stockcard', array('costPrice' => $price), array('id' => $this->get('id')));

	}




	/***********************************
	 * Static Functions
	 *
	 */

	public static function fetchPurchaseDocket($date, $userId){
		global $registry;
		return $registry->get('stockDb')->fetchPurchaseDocket($date, $userId);
	}

	public static function deleteItemFromDocket($docket, $docketId){
		global $registry;
		return $registry->get('stockDb')->deleteDocketItem($docket, $docketId);
	}

	public static function updateDocketItem(Array $data){
		global $registry;
		return $registry->get('stockDb')->updateDocketItem($data);
	}

	public static function clearDocket($docket, $userId){
		global $registry;
		return $registry->get('stockDb')->clearDocket($docket, $userId);
	}

	public static function checkIfExist($codeNo){
		global $registry;
		return $registry->get('stockDb')->checkIfExist($codeNo);
	}

	public static function createCard(Array $data){
		global $registry;
		return $registry->get('stockDb')->createCard($data);
	}

	public static function insertIntoPurchases(Array $data){
		global $registry;
		$registry->get('stockDb')->insertIntoPurchases($data);
	}

	public static function fetchAll(){
		global $registry;

		$response = array();
		foreach($registry->get('stockDb')->fetchAll() as $data){
			$response[] = new StockItem($data);
		}
		return $response;
	}

	public static function fetchBadItems()
	{
		# code...
		global $registry;

		return $registry->get('db')->query('select * from baditems order by id desc', array(), true);

	}

	public static function fetchTotalCostOfGoods($month, $year)
	{
		# code...
		global $registry;
		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		$result = $registry->get('db')->bindFetch('select sum(qty * rate) as total from purchases where date between :beginDate and :endDate', array('beginDate' => $beginDate, 'endDate' => $endDate), array('total'));

		return $result['total'];

	}


	public static function fetchIdByCodeNo($codeNo)
	{

		# code...
		global $registry;
		$result =  $registry->get('db')->bindFetch('select id from stockcard where codeNo = :codeNo', array('codeNo' => $codeNo), array('id'));

		return $result['id'];

	}

	public static function fetchCategories()
	{
		# code...
		global $registry;
		return $registry->get('db')->query('select * from stockcategories', array(), true);

	}

	public static function fetchBrands($limit = '')
	{
		# code...
		global $registry;
		$query = 'select * from perfumebrands';
		if($limit != ''){
			$query .= ' limit ' . $limit;
		}
		return $registry->get('db')->query($query, array(), true);

	}

	public static function fetchLastPurchaseDetails($codeNo)
	{
		# code...
		global $registry;
		return $registry->get('db')->query('select * from purchases where codeNo = :codeNo order by id desc limit 1 offset 1', array('codeNo' => $codeNo));
	}


	public static function fetchLastSoldDetails($codeNo)
	{
		# code...
		global $registry;
		return $registry->get('db')->query('select * from sales where codeNo = :codeNo order by id desc limit 1', array('codeNo' => $codeNo));
	}

	public static function getTotalPurchaseAmountForMonth($month, $year)
	{
		# code...
		global $registry;
		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		return $registry->get('db')->bindFetch('select sum(qty * rate) as total from purchases where date between :beginDate and :endDate', array('beginDate' => $beginDate, 'endDate' => $endDate), array('total'))['total'];

	}

	public static function fetchTopPurchases($year, $limit)
	{
		# code...
		global $registry;

		$beginDate = $year . '-01-01';
		$endDate = $year . '-12-31';

		return $registry->get('db')->query('select sum(qty) as total, codeNo from purchases where date between :beginDate and :endDate group by codeNo order by total desc limit ' . $limit, array('beginDate' => $beginDate, 'endDate' => $endDate), true);
	}

	public static function fetchReduced($reductionBenchmark)
	{
		# code...
		global $registry;
		return $registry->get('db')->query('select * from currentstock where qty <= ' . $reductionBenchmark, array(), true);
	}

	public static function getBrandName($brandId)
	{
		# code...
		global $registry;
		return $registry->get('db')->bindFetch('select name from perfumebrands where id = :id', array('id' => $brandId), array('name'))['name'];
	}



#end of class
}
