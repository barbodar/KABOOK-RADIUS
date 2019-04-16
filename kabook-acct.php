#!/usr/bin/php -q
<?php

require('libs/config/defines.php');
require('libs/helper/radius.php');
require('libs/helper/database.php');
require('libs/helper/logger.php');
require('libs/phpagi/phpagi.php');

$moduleName = "KABOOK RADIUS - ACCT";

//Create a new AGI Class
$agi = new AGI();

//Create Logger
$logger = new LOGGER();
$logger->agi = $agi;
$logger->moduleName = $moduleName;

//Create Database Connection
$db = new DB();
$db->assignConfig(DB_TYPE_MYSQL);
$dbConn = $db->connect();

//User Details Catcher
$data['uniqueid'] = $agi->request['agi_uniqueid'];
$data['callerid'] = $agi->request['agi_callerid'];
$data['extension'] = $agi->request['agi_extension'];
$data['carrierAddress'] = $agi->get_variable("NEXTHOP")["data"];
$data['hangupCause'] = $agi->get_variable("HANGUPCAUSE")["data"];
$data['totalDuration'] = $agi->get_variable("CDR(duration)")["data"];
$data['billedDuration'] = $agi->get_variable("CDR(billsec)")["data"];
$data['disposition'] = $agi->get_variable("CDR(disposition)")["data"];
$data['callProcessStage'] = MYSQL_PROCESSED_CALL_NOTPROCESSED;

$userURI = $agi->get_variable("SIPURI", true);
$sipusername = $agi->get_variable("SIP_HEADER(From)", true);

$dbResult = $db->updateCall($dbConn, $data);

if( $dbResult[0] === MYSQL_SUCCESS_RESULT ) {

    $logger->sendLog("Call updated successfully, UniqueId : {$data['uniqueid']}");
    
} else {

    $logger->sendLog("##### Call NOT updated please check UniqueId : {$data['uniqueid']} #####");

}

//Free up the resources
$db->close($dbConn);
$db = NULL;
$radClient = NULL;
$agi = NULL;
$logger = NULL;

exit();
