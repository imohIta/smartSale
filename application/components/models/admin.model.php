<?php
/**
* pass-iAmLegend-Key
*
*/
defined('ACCESS') || Error::exitApp();

class AdminModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	private $resetHash = '$2y$10$rQ5iENJOXSQhXTFu5BpjoucFEKm9We8wlECaP0H.bn4syITWNElZG';
	private $resetPwd = 'SYz@A+min0_0';

	public function execute(Array $options){
		$this->_viewParams = $options;
		$this->notify();

		# SYz@A+min0_0
	}

	public function resetApp($passKey, $resetType = 1)
	{
		# code...
		global $registry;

		$session = $registry->get('session');

		$passKey = filter_var($passKey, FILTER_SANITIZE_STRING);
		if(Authenticator::simpleCrypt($passKey) != 'c2hIT21neWRiektCZTRCaEFjd1ZJZz09'){
			echo '<p style="font-size:16px; color:red; text-align:center; margin-top:20px">You do not have permission to carry out ths operation</p>';
		}else{

			echo '<p style="color:green; font-size:16px; text-align:center; margin-top:20px">App Reset in Progress, Please wait...</p>';

			$truncateTbls1 = array('baditems', 'currentstock', 'customerinfo', 'expenses', 'expensescategories', 'incomingcash',  'purchasedocket', 'purchases', 'sales', 'salesdocket', 'staffinfo',  'subcharges', 'transactions');

			$truncateTbls2 = array();

			if($resetType == 2){
				$truncateTbls2('stockcard', 'stockcategories', 'perfumebrands', 'suppliers');
			}

			$truncateTbls = array_merge($truncateTbls1, $truncateTbls2);

			# trucate tables
			foreach ($truncateTbls as $key => $value) {
				# code...
				$registry->get('db')->truncateTbl($value);
			}

			# reset app cache
			$registry->get('db')->update('appCache', array('lastPurchaseNo' => '0', 'lastInvioceNo' => 0), array('id' => 1));

			# reset lastPurchaseSynId
			$registry->get('db')->update('lastpurchaseidsync', array('id' => '0'));

			# reset user table
			$registry->get('db')->query('delete from users where id > :id', array('id' => 1));


			echo '<p style="color:green; font-size:16px; margin-top:40px; text-align:center">App Reset in Progress, Please wait...</p>';


		}


	}


	public function runSynManager()
	{

		$host = 'localhost';
	    $dbName = 'smartSale-v2';
	    $dbUser = 'root';
	    $dbPwd = 'root';
	    $dsn = "mysql:host=" . $host . ";dbname=" . $dbName . ";charset=utf8";

	    $localConnection = new PDO($dsn, $dbUser, $dbPwd);
	    $localConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $localConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

		# get last purchase Syn Id from Local Db
	    $st = $localConnection->query('select * from lastpurchaseidsync');
	    $st->execute();
	    $response = $st->fetch();
	    $lastPurchaseSynId =  $response->id;



	    # fetch all purchases starting from last purchase Syn ID from Local DB
	    $st = $localConnection->query('select * from purchases limit 100 offset ' . $lastPurchaseSynId);
	    $result = $st->execute() ? $st->fetchAll() : array();


	    if(count($result) > 0){
	        $counter = 1;
	        foreach ($result as $purchase) {

	            # do current stock reconciliation

	            $localConnection->exec('update currentstock set qty = qty + ' . $purchase->qty . ' where codeNo = ' . $purchase->codeNo);

	            # if resultset is the last record fetched
	            if($counter == count($result)){

	                # update last purchase Sync Id
	                $localConnection->exec('update lastpurchaseidsync set id = ' . $purchase->id);
	            }

	            $counter++;
	        }


	    }

	    echo json_encode(array('status' => true));
	}






	#end of class
}
