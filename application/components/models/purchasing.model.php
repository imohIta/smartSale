<?php
    /**
     *
     *
     */
    defined('ACCESS') || Error::exitApp();

    class PurchasingModel extends BaseModel
    {

        protected $_param;
        protected $_viewParams;

        public function execute(Array $options = array( 'action' => 'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => '' ))
        {
            $this->_viewParams = $options;
            $this->notify();
        }


        public function setSummaryDuration(Array $data)
        {
            global $registry;
            $session = $registry->get('session');


            $month = filter_var($data['month'], FILTER_SANITIZE_STRING);
            $year = filter_var($data['year'], FILTER_SANITIZE_STRING);

            $session->write('PL-month', $month);
            $session->write('PL-year', $year);


            $this->execute(array( 'action' => 'render', 'tmpl' => 'profitNLoss', 'widget' => '', 'msg' => '' ));

        }


        public function addTemp(Array $data)
        {

            global $registry;
            $thisUser = unserialize($registry->get('session')->read('thisUser'));


            # get all form fields into an array...
            $formFields = array();
            foreach ( $data as $key => $value ) {
                # code...
                $formFields[] = $key;
            }

            //var_dump($formFields, $data); die;

            #sanitize each of the fields & append to sanitized array
            $sanitized = array();
            foreach ( $formFields as $key ) {
                # code...

                if ( ctype_digit($data[ $key ]) ) {
                    $$key = $registry->get('form')->sanitize($data[ $key ], 'float');
                }
                else {
                    $$key = $registry->get('form')->sanitize($data[ $key ], 'string');
                }
                $sanitized[ $key ] = $$key;

            }

            # check if item has already been added to this docket
            if ( $registry->get('stockDb')->checkIfItemInDocket(array(
                'docket' => 'purchasedocket',
                'date'    => date('Y-m-d'),
                'codeNo'  => $sanitized[ 'codeNo' ],
                'staffId' => $thisUser->get('id')
            ))
            ) {

                    # get current qty of item in Docket
                    $response = $registry->get('stockDb')->getItemQtyInDocket(array(
                        'docket' => 'purchasedocket',
                        'codeNo'  => $sanitized[ 'codeNo' ],
                        'staffId' => $thisUser->get('id')
                    ));

                    # update item in docket
                    $registry->get('stockDb')->updateDocketItem(array(
                        'docket' => 'purchasedocket',
                        'codeNo'  => $sanitized[ 'codeNo' ],
                        'date'    => date('Y-m-d'),
                        'name' => ucwords($sanitized['itemName']),
                        'price' => $sanitized['price'],
                        'qty'     => $sanitized[ 'qty' ] + $response['qty'],
                        'staffId' => $thisUser->get('id')
                    ));

                    /****
                     * @TODO
                     * check if price of item is not the same with the items old price
                     * If So....log price change
                     *
                     */

                    $msg = $sanitized[ 'itemName' ] . "'s quantity successfully updated in Docket";

            } else {

                # add item to docket
                $registry->get('stockDb')->addToDocket(array(
                    'docket' => 'purchasedocket',
                    'date'    => date('Y-m-d'),
                    'name'    => ucwords($sanitized[ 'itemName' ]),
                    'codeNo'  => $sanitized[ 'codeNo' ],
                    'price'   => $sanitized[ 'price' ],
                    'qty'     => $sanitized[ 'qty' ],
                    'staffId' => $thisUser->get('id')
                ));

                $msg = $sanitized[ 'itemName' ] . " successfully added to Docket";
            }

            # fetch current Docket
            $msg = array();
            $msg[ 'docket' ] = StockItem::fetchPurchaseDocket(date('Y-m-d'), $thisUser->get('id'));

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPurchaseDocket', 'msg' => $msg ));
        }


        public function deleteDocketItem($docketId)
        {
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            StockItem::deleteItemFromDocket('purchasedocket', $docketId);

            # fetch current Docket
            $msg = array();
            $msg[ 'docket' ] = StockItem::fetchPurchaseDocket(date('Y-m-d'), $thisUser->get('id'));

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPurchaseDocket', 'msg' => $msg ));
        }



        public function clearDocket(){

            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            StockItem::clearDocket('purchasedocket', $thisUser->get('id'));


        }

        public function addItemsToStock($data){

            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            $date = filter_var($data['date'], FILTER_SANITIZE_STRING);

            $supplier = filter_var($data['supplier'], FILTER_SANITIZE_NUMBER_INT);

            $purchaseNo = filter_var($data['purchaseNo'], FILTER_SANITIZE_STRING);

            # fetch all docket items
            $docketItems = StockItem::fetchPurchasedocket(date('Y-m-d'), $thisUser->get('id'));

            if(count($docketItems) > 0){

                # loop tru each docket item
                foreach($docketItems as $docketItem){


                    # create new instance of stock Item
                    $stockItem = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($docketItem->codeNo));

                    if($docketItem->price != $stockItem->get('price')){

                        # update stock card
                       $stockItem->updateCostPrice($docketItem->price);
                    }

                    # update qty of StockItem
                    $stockItem->increaseQty($docketItem->qty);


                    # insert item into stock purchases table
                    StockItem::insertIntoPurchases(array(
                       'date' => changeDateFormat($date, 'd-m-Y', 'Y-m-d'),
                       'transId' => $purchaseNo,
                       'supplierId' => $supplier,
                       'itemName' => $docketItem->itemName,
                       'qty' => $docketItem->qty,
                       'rate' => $docketItem->price,
                       'codeNo' => $docketItem->codeNo,
                       'staffId' => $thisUser->get('id')
                    ));


                    # delete item from docket
                    StockItem::deleteItemFromDocket('purchasedocket', $docketItem->id);

                }


                # get last purchase No
                $lastPurchaseNo = $registry->get('db')->bindFetch('select lastPurchaseNo as no from appcache where id= :id',array('id' => 1), array('no'));


                # update last purchase No
                $registry->get('db')->update('appcache', array('lastPurchaseNo' => $lastPurchaseNo['no'] + 1), array('id' => 1));
                $msg = array('docket' => array());

            }

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPurchaseDocket', 'msg' => $msg ));



        }


        public function fetchPrevious($transId)
        {
            # code...
            global $registry;

            $trandId = filter_var($transId, FILTER_SANITIZE_STRING);

            $msg['docket'] = $registry->get('db')->query('select * from purchases where transId = :transId', array('transId' => $transId), true);
            $msg['responseType'] = 'html';

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPreviousPurchase', 'msg' => $msg ));
        }

        public function fetchPreviousDetails($transId)
        {
            # code...
            global $registry;

            $trandId = filter_var($transId, FILTER_SANITIZE_STRING);

            $docket = $registry->get('db')->query('select * from purchases where transId = :transId', array('transId' => $transId));


            $supplier = $registry->get('db')->bindFetch('select name from suppliers where id = :id', array('id' => $docket->supplierId), array('name'));

            $msg['docket'] = array(
                'supplier' => $supplier['name'],
                'date' => changeDateFormat($docket->date)
            );

            $msg['responseType'] = 'json';

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPreviousPurchase', 'msg' => $msg ));
        }

        public function fetchDocket()
        {
            # code...
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            $msg[ 'docket' ] = StockItem::fetchPurchaseDocket(date('Y-m-d'), $thisUser->get('id'));

            $msg['responseType'] = 'html';

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPurchaseDocket', 'msg' => $msg ));
        }


        # end of class
    }
