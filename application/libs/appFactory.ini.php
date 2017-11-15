<?php

use application\libs\database\Database;
use  application\libs\database\StockDatabase;
use  application\libs\database\SalesDatabase;
use  application\libs\database\AppUserDatabase;
use application\libs\database\LoggerDatabase;
use application\libs\Logger;
use application\libs\Authenticator;

defined('ACCESS') || AppError::exitApp();

global $registry;

/************************************************************
	INSTANTIATE AND REGISTER APPLICATION-WIDE CLASESS
************************************************************/

# create Logger
$logger = new logger(new LoggerDatabase(array(), true));


$registry->set('authenticator', new Authenticator($registry->get('db')))
        ->set('appDb', new Database(array(), true))
        ->set('logger', $logger, true)
        ->set('stockDb', new StockDatabase(array(), true))
        ->set('salesDb', new SalesDatabase(array(), true))
        ->set('appUserDb', new AppUserDatabase(array(), true));
