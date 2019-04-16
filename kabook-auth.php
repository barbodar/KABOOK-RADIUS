#!/usr/bin/php -q
<?php

require('libs/config/defines.php');
require('libs/helper/radius.php');
require('libs/helper/database.php');
require('libs/helper/randomizer.php');
require('libs/helper/logger.php');
require('libs/phpagi/phpagi.php');

$moduleName = "KABOOK RADIUS - AUTH";

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

//CREAT Randomizer
$randomizer = new RANDOMIZER();

//User Details Catcher
$data['direction'] = 'outbound';
$data['time'] = time();
$data['channel'] = $agi->request['agi_channel'];
$data['uniqueid'] = $agi->request['agi_uniqueid'];
$data['callerid'] = $agi->request['agi_callerid'];
$data['calleridname'] = $agi->request['agi_calleridname'];
$data['dnid'] = $agi->request['agi_dnid'];
$data['rdnis'] = $agi->request['agi_rdnis'];
$data['context'] = $agi->request['agi_context'];
$data['extension'] = $agi->request['agi_extension'];
$data['accountcode'] = $agi->request['agi_accountcode'];
$data['threadid'] = $agi->request['agi_threadid'];
$data['nasPort'] = substr(rand(1000,$data['threadid']), 0, 5);
$confId[0] = $randomizer->randomHash(8);
$confId[1] = $randomizer->randomHash(8);
$confId[2] = $randomizer->randomHash(8);
$confId[3] = $randomizer->randomHash(8);
$data['confId'] = "{$confId[0]} {$confId[1]} {$confId[2]} {$confId[3]}";
$data['clientAddress'] = $agi->get_variable("USERIP")["data"];

$userURI = $agi->get_variable("SIPURI", true);
$sipusername = $agi->get_variable("SIP_HEADER(From)", true);

$dbResult = $db->insertNewCall($dbConn, $data);

$logger->sendLog("Insert new call result : {$dbResult[0]}");

if( $dbResult[0] === MYSQL_SUCCESS_RESULT ) {

    $logger->sendLog("Call inserted successfully, going to send RADIUS AUTH packet");
    
    $radClient = new RADIUS();
    $radClient->h323_conf_id = "h323-conf-id={$data['confId']}";
    $radClient->eventTime = $data['time'];
    $radClient->acctSessionId = $data['uniqueid'];
    $radClient->nasPort = $data['nasPort'];
    $radAccessResponse = $radClient->authenticate($data['callerid'], $data['callerid'], $data['extension']);
    
    if( $radAccessResponse[RADIUS_ACCESS_REQUEST] == RADIUS_ACCESS_ACCEPT && $radAccessResponse[RADIUS_CISCO_H323_RETURN_CODE] == H323_RETURN_CODE_SUCCESS ) {
    
        $logger->sendLog("AUTH Response: {$radAccessResponse[RADIUS_CLASS]}");
        $agi->set_variable("AUTHRESULT", "######## RADIUSAUTH Call From {$data['callerid']} to {$data['extension']} ACCEPTED ########");
        $agi->exec("Goto", "MAIN-OUT,{$data['extension']},1");
    
    } else {
        
        $logger->sendLog("AUTH Response: Radius Access Rejected [Code: {$radAccessResponse[RADIUS_ACCESS_REQUEST]}] [h323-return-code : {$radAccessResponse[RADIUS_CISCO_H323_RETURN_CODE]}]");
        $agi->set_variable("AUTHRESULT", "######## RADIUSATUH Call From {$data['callerid']} to {$data['extension']} REJECTED ########");
        //$agi->exec("Goto", "MAIN-OUT,{$data['extension']},1");
        $agi->hangup();
    }

} else {
    
    $agi->hangup();
}

//Free up the resources
$db->close($dbConn);
$db = NULL;
$radClient = NULL;
$agi = NULL;
$logger = NULL;
$randomizer = NULL;

exit();
