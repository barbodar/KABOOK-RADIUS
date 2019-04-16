#!/usr/bin/php -q
<?php

require('libs/config/defines.php');
require('libs/helper/database.php');
require('libs/helper/logger.php');

$moduleName = "KABOOK RADIUS - CALL ARCHIVE - ENGINE";

//Create Logger
$logger = new LOGGER();
$logger->agi = NULL;
$logger->moduleName = $moduleName;

//Create Database Connection
$db = new DB();
$db->assignConfig(DB_TYPE_MYSQL);
$dbConn = $db->connect();

$counter = 0;
$mainLoop = true;

while($mainLoop) {

	$counter++;

    $logger->sendLog("archive all call processed in STOP state");
    $data['callProcessStage'] = MYSQL_PROCESSED_CALL_STOP;
    $dbResults = $db->getCalls($dbConn, $data);

    foreach($dbResults as $dbResult) {    
        $data['callProcessStage'] = MYSQL_PROCESSED_CALL_STOP;
        $data['uniqueid'] = $dbResult["onlineCalls_uniqueid"];
        $archiveResult = $db->archiveCall($dbConn, $data);
        $logger->sendLog("archive call with uniqueid {$data['uniqueid']} MYSQL Result : {$archiveResult[0]}");
        if($archiveResult[0] == MYSQL_SUCCESS_RESULT) {
            $data['callProcessStage'] = MYSQL_PROCESSED_CALL_ARCHIVED;
            $updateResult = $db->updateCallStatus($dbConn, $data);
            $logger->sendLog("update call status with uniqueid {$data['uniqueid']} to archived MYSQL Result : {$updateResult[0]}");
        } else { 
			$data['callProcessStage'] = MYSQL_PROCESSED_CALL_ARCHIVED;
            $updateResult = $db->updateCallStatus($dbConn, $data);
            $logger->sendLog("update call status with uniqueid {$data['uniqueid']} to archived MYSQL Result : {$updateResult[0]}");
		}
    }

    usleep(SLEEP_STEP_1);
    
    $logger->sendLog("archive all call processed in STOP state");
    $data['callProcessStage'] = MYSQL_PROCESSED_CALL_ARCHIVED;
    $dbResults = $db->getCalls($dbConn, $data);
    
    foreach($dbResults as $dbResult) {    
        $data['uniqueid'] = $dbResult["onlineCalls_uniqueid"];
        $removeResult = $db->removeOnlineCall($dbConn, $data);
        $logger->sendLog("remove online call with uniqueid {$data['uniqueid']} MYSQL Result : {$removeResult[0]}");
    }
    
    usleep(SLEEP_STEP_2);
	
	if($counter == LOOP_TIME ) { $mainLoop = false; }
}

//Free up the resources
$db->close($dbConn);
$db = NULL;
$radClient = NULL;
$agi = NULL;
$logger = NULL;

exit();
