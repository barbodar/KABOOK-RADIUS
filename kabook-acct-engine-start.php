#!/usr/bin/php -q
<?php

require('libs/config/defines.php');
require('libs/helper/radius.php');
require('libs/helper/database.php');
require('libs/helper/logger.php');

$moduleName = "KABOOK RADIUS - ACCT - ENGINE";

//Create Logger
$logger = new LOGGER();
$logger->agi = NULL;
$logger->moduleName = $moduleName;

//Create Database Connection
$db = new DB();
$db->assignConfig(DB_TYPE_MYSQL);
$dbConn = $db->connect();

//Create RADIUS Handler
$radius = new RADIUS();

$logger->sendLog("Get list of all online calls NOT PROCESSED");
$data['callProcessStage'] = MYSQL_PROCESSED_CALL_NOTPROCESSED;
$dbResults = $db->getCalls($dbConn, $data);
$logger->sendLog("Count of all online calls NOT PROCESSED : ".count($dbResults));

foreach($dbResults as $dbResult) {
    $radius->acctSessionId = $dbResult["onlineCalls_uniqueid"];
    $radius->username = $dbResult["onlineCalls_callerid"];
    $radius->destination = $dbResult["onlineCalls_extension"];
    $radius->h323_conf_id = $dbResult["onlineCalls_confId"];
    $radius->nasPort = $dbResult["onlineCalls_nasPort"];
    $accountingResponse = $radius->sendAccounting(RADIUS_START, $dbResult);
    if($accountingResponse == RADIUS_ACCOUNTING_RESPONSE) {
        $data['callProcessStage'] = MYSQL_PROCESSED_CALL_START;
        $data['uniqueid'] = $dbResult["onlineCalls_uniqueid"];
        $updateResult = $db->updateCallStatus($dbConn, $data);
        $logger->sendLog("update call status with uniqueid {$data['uniqueid']} to START, RAD RESP: {$accountingResponse}");
    }
}

//Free up the resources
$db->close($dbConn);
$db = NULL;
$radClient = NULL;
$agi = NULL;
$logger = NULL;

exit();
