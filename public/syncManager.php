<?php

    /**************
    * Sync Manager Algorithm
    ( syn shud run on the background every  X mins )

    * create table : lastPurchaseIdSynched ( id | lastID )






    ****************/


    # connect to local DB
    $host = 'localhost';
    $dbName = 'smartSale-v2';
    $dbUser = 'root';
    $dbPwd = 'root';
    $dsn = "mysql:host=" . $host . ";dbname=" . $dbName . ";charset=utf8";

    $localConnection = new PDO($dsn, $dbUser, $dbPwd);
    $localConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $localConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    //$localConnection->exec('update lastPurchaseIdSync set id = 15'); die;
    # connect to online DB
    // try {
    //
    //     $host = 'localhost';
    //     $dbName = 'smartSale-v2';
    //     $dbUser = 'Perfumes';
    //     $dbPwd = 'fx~t-7?&yp.(';
    //     $dsn = "mysql:host=" . $host . ";dbname=" . $dbName . ";charset=utf8";
    //
    //     $liveConnection = new PDO($dsn, $dbUser, $dbPwd);
    //
    //
    // } catch (PDOException $e) {
    //     //echo 'Error occured ' . $e->getMessage(); //die;
    // }

    # get last purchase Syn Id from Local Db
    $st = $localConnection->query('select * from lastPurchaseIdSync');
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

            $localConnection->exec('update currentStock set qty = qty + ' . $purchase->qty . ' where codeNo = ' . $purchase->codeNo);

            # if resultset is the last record fetched
            if($counter == count($result)){

                # update last purchase Sync Id
                $localConnection->exec('update lastPurchaseIdSync set id = ' . $purchase->id);
            }

            $counter++;
        }


    }

    echo 'Done';




 ?>
