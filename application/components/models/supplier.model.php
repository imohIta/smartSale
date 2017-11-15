<?php
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class SupplierModel extends BaseModel{

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

		$requiredFields = array('name');
		# get all form fields into an array...
		$formFields = array();
		foreach ($data as $key => $value) {
			# code...
			$formFields[] = $key;
		}

		$checkReq = json_decode($registry->get('form')->checkRequiredFields($requiredFields));

		#if some required fields where not filled
		if($checkReq->status == 'error'){
			$this->execute(array('action'=>'display', 'tmpl' => 'addNewSupplier', 'widget' => 'error', 'msg' => $checkReq->msg));
		}

		#sanitize each of the fields & append to sanitized array
		$sanitized = array();
		foreach ($formFields as $key) {
			# code...

			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

			$sanitized[$key] = $$key;

		}

		$registry->get('db')->insert('suppliers', array(
			'name' => ucwords($sanitized['name']),
			'phone' => $sanitized['phone'],
			'email' => $sanitized['email'],
			'address' => $sanitized['address']
		));


		$msg = 'Supplier ' . ucwords($sanitized['name']) . ' successfully added';
		$this->execute(array('action'=>'display', 'tmpl' => 'addNewSupplier', 'widget' => 'success', 'msg' => $msg));

	}

	public function deleteSupplierInfo(Array $data)
	{
		# code...
		global $registry;

		$id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
		$name = filter_var($data['name'], FILTER_SANITIZE_STRING);

		$registry->get('db')->delete('suppliers', array('id' => $id));

		$msg = 'Supplier ( ' . $name . ' ) was successfully Deleted';

		$this->execute(array('action'=>'display', 'tmpl' => 'editSupplierInfo', 'widget' => 'success', 'msg' => $msg));
	}

	public function fetchSupplierInfo($id)
	{
		# code...
		global $registry;

		$id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

		$msg['supplierInfo'] = $registry->get('db')->query('select * from suppliers where id = :id', array('id' => $id));

		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'supplierInfo', 'msg' => $msg));


	}

	public function editSupplierInfo(Array $data)
	{
		# code...
		global $registry;

		$id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
		$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
		$address = filter_var($data['address'], FILTER_SANITIZE_STRING);
		$email = filter_var($data['email'], FILTER_SANITIZE_STRING);
		$phone = filter_var($data['phone'], FILTER_SANITIZE_STRING);


		$registry->get('db')->update('suppliers',array(
			'name' => $name,
			'address' => $address,
			'email' => $email,
			'phone' => $phone
		), array('id' => $id));

		$msg = 'Supplier ( ' . $name . ' ) info was successfully updated';

		$this->execute(array('action'=>'display', 'tmpl' => 'editSupplierInfo', 'widget' => 'success', 'msg' => $msg));
	}

	public function fetchPurchaseHistory($supplierId)
	{
		# code...
		global $registry;
		$msg['purchases'] = $registry->get('db')->query('select * from purchases where supplierId = :supplierId group by transId', array('supplierId' => $supplierId), true);

		$this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'supplierPurchases', 'msg' => $msg));
	}

	#end of class
}
