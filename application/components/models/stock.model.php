<?php
    /**
     *
     *
     */
    defined('ACCESS') || Error::exitApp();

    class StockModel extends BaseModel
    {

        protected $_param;
        protected $_viewParams;

        public function execute(Array $options = array( 'action' => 'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => '' ))
        {
            $this->_viewParams = $options;
            $this->notify();
        }


        public function setItemByCodeNo(Array $data)
        {

            global $registry;
            $session = $registry->get('session');

            $codeNo = filter_var($data[ 'itemQuery' ], FILTER_SANITIZE_STRING);

            $item = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($codeNo));

            if ( !is_null($item->get('id')) ) {
                $session->write('foundItem', serialize($item));
                $session->write('searchQuery', $codeNo);
                $session->write('searchType', 1);
            }

            # redirect
            $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/stock/addNew');
        }

        public function suggestByName($searchQuery, $requestLocation, $fetchEmpty)
        {
            global $registry;
            $searchQuery = filter_var($searchQuery, FILTER_SANITIZE_STRING);

            $msg[ 'searchQuery' ] = $searchQuery;
            $msg[ 'suggestions' ] = $registry->get('stockDb')->suggestByName($searchQuery);
            $msg['requestLocation'] = $requestLocation;

            # this param is to flag weather a message should be returned wen an item is not found
            $msg['fetchEmpty'] = $fetchEmpty;

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'suggestedItems', 'msg' => $msg ));


        }


        public function setItemById($itemId, $itemName)
        {
            global $registry;
            $session = $registry->get('session');

            $itemId = filter_var($itemId, FILTER_SANITIZE_NUMBER_INT);
            $itemName = filter_var($itemName, FILTER_SANITIZE_STRING);

            $item = new StockItem($itemId);

            if ( !is_null($item->get('id')) ) {
                $session->write('foundItem', serialize($item));
                $session->write('searchQuery', $itemName);
                $session->write('searchType', 2);
            }

            # redirect
            $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/stock/addNew');


        }




        public function fetchDocketItemById($docketId)
        {

            global $registry;

            $docketId = filter_var($docketId, FILTER_SANITIZE_NUMBER_INT);

            # fetch stock item
            $msg['Item'] = $registry->get('stockDb')->fetchDocketItem('purchaseDocket', $docketId);

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'getDocketItem', 'msg' => $msg ));
        }

        public function fetchStockItemByCodeNo($codeNo, $requestLocation = 'sales'){

            global $registry;

            $codeNo = trim(filter_var($codeNo, FILTER_SANITIZE_STRING));

            # fetch stock item
            $msg['Item'] = $registry->get('stockDb')->fetchStockByCodeNo($codeNo);
            $msg['requestLocation'] = $requestLocation;

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'getDocketItem', 'msg' => $msg ));
        }


        public function removeBadItem($data)
        {
            # code...
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            $required = array( 'codeNo', 'qty', 'reason' );

            $checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

            #if some required fields where not filled
            if ( $checkReq->status == 'error' ) {

                $this->execute(array( 'action' => 'display', 'tmpl' => 'removeBadItem', 'widget' => 'error', 'msg' =>
                    $checkReq->msg ));
            }

            $codeNo = filter_var($data[ 'codeNo' ], FILTER_SANITIZE_STRING);
            $qty = filter_var($data[ 'qty' ], FILTER_SANITIZE_NUMBER_INT);
            $reason = filter_var($data[ 'reason' ], FILTER_SANITIZE_STRING);

            $stockItem = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($codeNo));

            if ( $stockItem->get('qtyInStock') < $qty ) {

                $this->execute(array( 'action' => 'display', 'tmpl' => 'removeBadItem', 'widget' => 'error', 'msg' => 'Quantity in Stock of ' . $stockItem->get('name') . ' is less than ' . $qty ));
            }

            # Remove Item from Stock
            $stockItem->reduceQty($qty);

            # save in bad items table
            $registry->get('db')->insert('badItems', array(
                'itemId' => $stockItem->get('id'),
                'date' => date('Y-m-d'),
                'time' => time(),
                'qty' => $qty,
                'reason' => $reason,
                'userId' => $thisUser->get('id')
            ));


            # log Stock Removal
            $msg = 'Removed ' . $qty . ' ' . $stockItem->get('name') . ' from Stock ( Reason for removal : ' .
                   $reason . ' )';

            $registry->get('logger')->logBadItemRemoval(array(
                'date' => date('Y-m-d'),
                'time' => time(),
                'staffId' => $thisUser->get('id'),
                'itemId' => $stockItem->get('id'),
                'qty'    => $qty,
                'reason' => $reason,
                'privilege' => 2,
                'msg' => $msg

            ));

            $this->execute(array( 'action' => 'display', 'tmpl' => 'removeBadItem', 'widget' => 'success', 'msg' => $qty . ' ' . $stockItem->get('name') . ' was successfully removed from Stock' ));

        }

    

        public function createCategory(Array $data)
        {
            # code...
            global $registry;

            $required = array( 'name');

            $checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

            #if some required fields where not filled
            if ( $checkReq->status == 'error' ) {

                $this->execute(array( 'action' => 'display', 'tmpl' => 'createStockCategory', 'widget' => 'error', 'msg' =>
                    $checkReq->msg ));
            }

            $name = ucwords(filter_var($data[ 'name' ], FILTER_SANITIZE_STRING));

            $registry->get('db')->insert('stockCategories', array(
                'name' => $name
            ));

            $this->execute(array( 'action' => 'display', 'tmpl' => 'createStockCategory', 'widget' => 'success', 'msg' => 'Stock Category ( ' . $name . ' ) was successfully created' ));

        }


        public function deleteCategory($categoryId)
        {
            # code...
            global $registry;

            $categoryId = filter_var($categoryId, FILTER_SANITIZE_STRING);

            $registry->get('db')->delete('stockCategories', array(
                'id' => $categoryId
            ));

            $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/stock/viewCategories');

            // $this->execute(array( 'action' => 'display', 'tmpl' => 'viewStockCategories', 'widget' => 'success', 'msg' => 'Stock Category was successfully deleted' ));

        }

        public function proccessStockCard(Array $data)
        {
            global $registry;

            $required = array( 'itemName', 'codeNo', 'wholesalePrice', 'retailPrice', 'costPrice', 'groupId');



            $checkReq = json_decode($registry->get('form')->checkRequiredFields($required));

            #if some required fields where not filled
            if ( $checkReq->status == 'error' ) {

                $this->execute(array( 'action' => 'display', 'tmpl' => 'stockCard', 'widget' => 'error', 'msg' =>
                    $checkReq->msg ));
            }

            # get all form fields into an array...
    		$formFields = array();
    		foreach ($data as $key => $value) {
    			# code...
    			$formFields[] = $key;
    		}

            # sanitize each of the fields & append to sanitized array
    		$sanitized = array();
    		foreach ($formFields as $key) {
    			# code...

    			$$key = $registry->get('form')->sanitize($_POST[$key], 'string');

    			$sanitized[$key] = $$key;

    		}

            # check if item with this codeNo already exist
            $id = StockItem::fetchIdByCodeNo($sanitized['codeNo']);

            //var_dump($id); die;

            if(!is_null($id)){ # stock card already exist

                # create new Stock Item
                $stockItem = new StockItem($id);

                # update stock card
                $stockItem->updateCard($sanitized);

                $msg = 'Stock Card successfully edited';

            }else{
                # add new stock card

                StockItem::createCard($sanitized);

                $msg = 'Stock Card successfully created';

            }

            $this->execute(array( 'action' => 'display', 'tmpl' => 'stockCard', 'widget' => 'success', 'msg' => $msg ));

        }




        # end of class
    }
