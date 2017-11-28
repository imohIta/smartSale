<?php
    /**
     *
     *
     */
    defined('ACCESS') || Error::exitApp();

    class SalesModel extends BaseModel
    {

        protected $_param;
        protected $_viewParams;

        public function execute(Array $options = array( 'action' => 'render', 'tmpl' => 'viewStock', 'widget' => '', 'msg' => '' ))
        {
            $this->_viewParams = $options;
            $this->notify();
        }


        public function addToDocket(Array $data){

            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));


            # get all form fields into an array...
            $formFields = array();
            foreach ( $data as $key => $value ) {
                # code...
                $formFields[] = $key;
            }

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

            //var_dump($sanitized['transId']); die;

            # check if transId is sent from UI
            if($sanitized['transId'] == ''){
                $transId = $registry->get('db')->bindFetch('select lastInvioceNo as no from appCache where id = :id', array('id' => 1), array('no'))['no'];
                $transId = (int)transId + 1;
                $sanitized['transId'] = 'INV-' . $transId;
            }

            # check if qty of required item is up to the qty of that item in stock
            $stockItem = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($sanitized[ 'codeNo' ]));

            if($stockItem->get('qtyInStock') < $sanitized[ 'qty' ]){

                # throw error
                $msg = array();
                $msg['errorMsg'] =  $stockItem->get('name') . "'s quantity in stock is less than " . $sanitized['qty'] . ". ( Available Qty : " . $stockItem->get('qtyInStock') . " )";

                $msg[ 'docket' ] = Sales::fetchDocket($thisUser->get('id'), $sanitized['transId']);

                $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showSalesDocket', 'msg' => $msg ));
                return;
            }


            # reduce item's qty from stock
            $stockItem->reduceQty($sanitized['qty']);


            # check if item has already been added to this docket
            if ( $registry->get('salesDb')->checkIfItemInDocket(array(
                'codeNo'  => $sanitized[ 'codeNo' ],
                'transId' => $sanitized[ 'transId' ]
            ))
            ) {


                # get current qty of item in Docket
                $response = $registry->get('salesDb')->getItemInDocket(array(
                    'codeNo'  => $sanitized[ 'codeNo' ],
                    'transId' => $sanitized[ 'transId' ]
                ));

                /** recalculate total discount and total */

                $totalDiscountPercent = $sanitized['discount'] + $response->discount;

                # add current total + total from docket
                $total = ( $sanitized['price'] * $sanitized['qty']) + ( $response->price * $response->qty );

                # caluculate discount amt
                $newDiscountAmt = ( $totalDiscountPercent / 100) * $total;

                $newTotal = $total - $newDiscountAmt;

                # update item in docket
                $registry->get('salesDb')->updateDocketItem(array(
                    'codeNo'  => $sanitized[ 'codeNo' ],
                    'transId' => $sanitized[ 'transId' ],
                    'qty'     => $sanitized[ 'qty' ] + $response->qty,
                    'discount' => $sanitized['discount'],
                    'total' => $newTotal,
                    'staffId' => $thisUser->get('id')
                ));


            } else {

                # add item to docket
                $registry->get('salesDb')->addToDocket(array(
                    'date'    => date('Y-m-d'),
                    'transId' => $sanitized[ 'transId' ],
                    'codeNo'  => $sanitized[ 'codeNo' ],
                    'price'   => $sanitized[ 'price' ],
                    'qty'     => $sanitized[ 'qty' ],
                    'discount'     => $sanitized[ 'discount' ],
                    'total' => $sanitized['total'],
                    'staffId' => $thisUser->get('id')
                ));

            }

            # fetch current Docket
            $msg = array();
            $msg[ 'docket' ] = Sales::fetchDocket($thisUser->get('id'), $sanitized['transId']);

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showSalesDocket', 'msg' => $msg ));


        }

        public function clearDocket($transId){

            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            # fetch Docket by transId
            $docketItems = Sales::fetchDocket($thisUser->get('id'), $transId);
            foreach ($docketItems as $item) {
                # code...

                $stockItem = new StockItem($registry->get('stockDb')->fetchStockByCodeNo($item->codeNo));
                # increase item's qty in stock
                $stockItem->increaseQty($item->qty);

                StockItem::deleteItemFromDocket('salesDocket', $item->id);

            }


        }

        public function setSalesDate(Array $data){

            global $registry;
            $session = $registry->get('session');


            $day = filter_var($data['day'], FILTER_SANITIZE_STRING);
            $month = filter_var($data['month'], FILTER_SANITIZE_STRING);
            $year = filter_var($data['year'], FILTER_SANITIZE_STRING);

            $date = $year . '-' . $month . '-' . $day;
            $session->write('salesDate', $date);
            $session->write('salesDate-day', $day);
            $session->write('salesDate-month', $month);
            $session->write('salesDate-year', $year);


            $this->execute(array( 'action' => 'render', 'tmpl' => 'viewTransactions', 'widget' => '', 'msg' => '' ));


        }

        public function deleteDocketItem($docketId, $transId)
        {
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));
            $stockDb = $registry->get('stockDb');

            # fetch Docket Item
            $docketItem = $registry->get('salesDb')->fetchDocketItem($docketId);

            $stockItem = new StockItem($stockDb->fetchStockByCodeNo($docketItem->codeNo));

            # increase item's qty in stock
            $stockItem->increaseQty($docketItem->qty);

            StockItem::deleteItemFromDocket('salesDocket', $docketId);

            # fetch current Docket
            $msg = array();
            $msg[ 'docket' ] = Sales::fetchDocket($thisUser->get('id'), $transId);

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showSalesDocket', 'msg' => $msg ));
        }

        public function completeSale(Array $data){

            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            $payType = filter_var($data['payType'], FILTER_SANITIZE_NUMBER_INT);
            $customerName = filter_var($data['customerName'], FILTER_SANITIZE_STRING);
            $customerAddr = filter_var($data['customerAddr'], FILTER_SANITIZE_STRING);
            $shippingAddr = filter_var($data['shippingAddr'], FILTER_SANITIZE_STRING);
            $date = filter_var($data['date'], FILTER_SANITIZE_STRING);
            $invoiceNo = filter_var($data['invoiceNo'], FILTER_SANITIZE_STRING);

            # get sales docket
            $salesDocket = Sales::fetchDocket($thisUser->get('id'), 'INV-' . $invoiceNo);

            if(count($salesDocket) < 1){

                $this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'error', 'msg' => 'Sales Docket is Empty'));
                return;
            }


            $time = time();

            $msg['receiptData'] = array(
                'date' => date('Y-m-d'),
                'time' => $time,
                'customerName' => $customerName,
                'customerAddr' => $customerAddr,
                'shippingAddr' => $shippingAddr,
                'invoiceNo' => 'INV-'. $invoiceNo,
                'docket' => $salesDocket,
                'staffId' => $thisUser->get('id')
            );

            $subTotal = $grandTotal = $totalDiscount = 0;
            foreach($salesDocket as $docketItem){

                $subTotal += ( $docketItem->qty * $docketItem->price);
                $grandTotal += $docketItem->total;
                $totalDiscount += $docketItem->discount;

                # insert into sales table
                Sales::addNew(array(
                    'date' => date('Y-m-d'),
                    'time' => $time,
                    'transId' => 'INV-'. $invoiceNo,
                    'codeNo' => $docketItem->codeNo,
                    'qty' => $docketItem->qty,
                    'price' => $docketItem->price,
                    'discount' => $docketItem->discount,
                    'amount' => $docketItem->total,
                    'userId' => $thisUser->get('id')
                ));

                # delete docket Item
                Sales::deleteFromDocket($docketItem->id);


            }

            $customerDetails = json_encode(array(
                'name' => $customerName,
                'address' => $customerAddr
            ));

            # add transaction details
            Sales::addTransDetails(array(
                'date' => date('Y-m-d'),
                'transId' => 'INV-'. $invoiceNo,
                'subTotal' => $subTotal,
                'discount' => $totalDiscount,
                'grandTotal' => $grandTotal,
                'payType' => $payType,
                'customerDetails' => $customerDetails,
                'shippingDetails' => json_encode(array('address' => $shippingAddr)),
                'userId' => $thisUser->get('id')
            ));

            # @TODO send shipping alert notification
            if($shippingAddr != ''){

            }

            # update last purchase No
            $registry->get('db')->update('appCache', array('lastInvioceNo' => $invoiceNo), array('id' => 1));


            $this->execute(array('action'=>'display', 'tmpl' => '', 'widget' => 'showSaleReceipt', 'msg' => $msg));
        }

        public function setSortParams(Array $data)
        {
            # code...
            global $registry;
            $session = $registry->get('session');

            $b_day = filter_var($data['b-day'], FILTER_SANITIZE_STRING);
            $b_month = filter_var($data['b-month'], FILTER_SANITIZE_STRING);
            $b_year = filter_var($data['b-year'], FILTER_SANITIZE_STRING);

            $e_day = filter_var($data['e-day'], FILTER_SANITIZE_STRING);
            $e_month = filter_var($data['e-month'], FILTER_SANITIZE_STRING);
            $e_year = filter_var($data['e-year'], FILTER_SANITIZE_STRING);

            $user = filter_var($data['user'], FILTER_SANITIZE_STRING);

            $b_date = $b_year . '-' . $b_month . '-' . $b_day;
            $e_date = $e_year . '-' . $e_month . '-' . $e_day;

            $datetime1 = date_create($b_date);
            $datetime2 = date_create($e_date);
            $interval = date_diff($datetime1, $datetime2);

            if($interval->format('%R%') == '-' || $interval->format('%R%a') == '+0'){

                $msg = 'Sort Begin Date must be earlier than Sort End Date';
                $this->execute(array( 'action' => 'display', 'tmpl' => 'sortSales', 'widget' => 'error', 'msg' => $msg ));
                return;
            }

            $session->write('salesBeginDate', $b_date);
            $session->write('salesEndDate', $e_date);

            $session->write('salesDate-b-day', $b_day);
            $session->write('salesDate-b-month', $b_month);
            $session->write('salesDate-b-year', $b_year);

            $session->write('salesDate-e-day', $e_day);
            $session->write('salesDate-e-month', $e_month);
            $session->write('salesDate-e-year', $e_year);

            $session->write('salesDate-user', $user);

            $this->execute(array( 'action' => 'render', 'tmpl' => 'sortSales', 'widget' => '', 'msg' => '' ));

        }


        public function fetchTransactionsOnHold()
        {
            # code...
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));

            $msg['transactionsOnHold'] = $registry->get('db')->query('select * from salesDocket where staffId = :userId and onHold = :onHold group by transId', array('userId' => $thisUser->get('id'), 'onHold' => 1), true);

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'transactionsOnHold', 'msg' => $msg ));
        }


        public function fetchPrevious($transId)
        {
            # code...
            global $registry;

            $trandId = filter_var($transId, FILTER_SANITIZE_STRING);

            $msg['docket'] = $registry->get('db')->query('select * from sales where transId = :transId', array('transId' => $transId), true);
            $msg['responseType'] = 'html';

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPreviousSales', 'msg' => $msg ));
        }

        public function fetchPreviousDetails($transId)
        {
            # code...
            global $registry;

            $trandId = filter_var($transId, FILTER_SANITIZE_STRING);

            $transDetails = $registry->get('db')->query('select * from transactions where transId = :transId', array('transId' => $transId));

            $customerDetails = json_decode($transDetails->customerDetails);
            $msg['docket'] = array(
                'date' => changeDateFormat($transDetails->date),
                'customerName' => $customerDetails->name,
                'customerAddr' => $customerDetails->address,
                'shippingAddr' => ''
            );

            if($transDetails->shippingDetails != ''){
                $shippingDetails = json_decode($transDetails->shippingDetails);
                $msg['docket']['shippingAddr'] = $shippingDetails->address;
            }

            $msg['responseType'] = 'json';

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showPreviousSales', 'msg' => $msg ));
        }

        public function fetchDocket($invoiceNo)
        {
            # code...
            global $registry;
            $session = $registry->get('session');
            $thisUser = unserialize($session->read('thisUser'));
            $invoiceNo = filter_var($invoiceNo, FILTER_SANITIZE_STRING);

            $msg[ 'docket' ] = Sales::fetchDocket($thisUser->get('id'), $invoiceNo);

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showSalesDocket', 'msg' => $msg ));
        }

        public function holdTransaction($invoiceNo)
        {
            # code...
            global $registry;

            # hold all transactions with this invoice no
            $registry->get('db')->update('salesDocket', array('onHold' => 1), array('transId' => $invoiceNo));

            # split invoiceNo to get the digit
            $invoice = explode('-', $invoiceNo);


            # update last purchase No
            //$registry->get('db')->update('appCache', array('lastInvioceNo' => $invoice[1]), array('id' => 1));

            $msg['data'] = array(
                'invoiceNo' => (int)$invoice[1] + 1
            );

            # store invioce no in session
            //$session->write('currentInvoiceNo', 'INV-'. (int)$invoice[1] + 1);

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'holdTransaction', 'msg' => $msg ));

        }

        public function recallTransaction($invoiceNo)
        {
            # code...
            global $registry;

            # fetch all sales with this invoice No
            $msg[ 'docket' ] = $registry->get('db')->query('select * from salesDocket where transId = :invoiceNo',array('invoiceNo' => $invoiceNo), true);

            # split invoiceNo to get the digit
            $invoice = explode('-', $invoiceNo);

            $msg['transId'] = (int)$invoice[1];

            # recall all transactions with this invoice no
            $registry->get('db')->update('salesDocket', array('onHold' => 0), array('transId' => $invoiceNo));

            $this->execute(array( 'action' => 'display', 'tmpl' => '', 'widget' => 'showSalesDocket', 'msg' => $msg ));


        }




        # end of class
    }
