<?php

defined('ACCESS') || Error::exitApp();

/**
* Had to edit this class froma normal class to a decorator object that collects a staff class
*/
class Expenses extends FuniObject
{
	protected $_id = null;
	protected $_date = null;
	protected $_categoryId = null;
	protected $_category = null;
	protected $_description = null;
	protected $_amount = null;


	function __construct($obj)
	{

		# code...
		global $registry;
		$db = $registry->get('db');

		# if appUserId only is passed
		if(!is_object($obj)){

			# fetch expenses details
			$obj = $db->query('select * from expenses where id = :id', array('id' => $obj));

		}

		if ( !is_null($obj) && false !== $obj) {

			$this->_id = $obj->id;
			$this->_date = $obj->date;
			$this->_categoryId = $obj->categoryId;
			$this->_amount = $obj->amount;
			$this->_description = $obj->description;
            $this->_category = $this->_getCategory();
		}

	}

	private function _getCategory()
	{
		# code...
		global $registry;
        $result = $registry->get('db')->bindFetch('select name from expensescategories where id = :id', array('id' => $this->_categoryId), array('name'));
		return $result['name'];
	}


    public static function fetchCategories()
    {
        # code...
        global $registry;
        return $registry->get('db')->query('select * from expensescategories', array(), true);
    }

    public static function addNew(Array $data)
    {
        # code...
        global $registry;
        $registry->get('db')->insert('expenses', array(
            'date' => $data['date'],
            'categoryId' => $data['category'],
            'description' => $data['description'],
            'amount' => $data['amount']
        ));

        //return $this;
    }

	public static function addNewCategory(Array $data)
    {
        # code...
        global $registry;
        $registry->get('db')->insert('expensescategories', array(
            'name' => $data['category']
        ));

        //return $this;
    }

    public static function fetchAll(Array $data)
    {
        # code...
        global $registry;

        switch($data['sortBy']){

            case 'date':

                return $registry->get('db')->query('select * from expenses where date = :date', array('date' => $data['data']), true);

            break;

            case 'category':

                return $registry->get('db')->query('select * from expenses where categoryId = :categoryId', array('categoryId' => $data['data']), true);

            break;

        }
    }


	public static function fetchAllForMonth($month, $year)
	{
		# code...
		global $registry;
		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . $month . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		$result = $registry->get('db')->bindFetch('select sum(amount) as total from expenses where date between :beginDate and :endDate', array('beginDate' => $beginDate, 'endDate' => $endDate), array('total'));

		return $result['total'];
	}

	public static function fetchSummaryForMonth($month, $year, $limit)
	{
		# code...
		global $registry;

		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . date('m') . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		return $registry->get('db')->query('select sum(amount) as total, categoryId, id from expenses where date between :beginDate and :endDate group by categoryId order by total desc limit ' . $limit, array('beginDate' => $beginDate, 'endDate' => $endDate), true);
	}

	public static function sumTotalExpensesForMonth($month, $year)
	{
		# code...
		global $registry;
		$beginDate = $year . '-' . $month . '-01';
		$endDate = $year . '-' . date('m') . '-';
		$endDate .= ($month == 2) ? '28' : '31';

		return $registry->get('db')->bindFetch('select sum(amount) as total from expenses where date between :beginDate and :endDate', array('beginDate' => $beginDate, 'endDate' => $endDate), array('total'))['total'];

	}


#end of class
}
