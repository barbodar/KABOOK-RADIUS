#!/usr/bin/php -q
<?php

require('libs/config/defines.php');
require('libs/phpagi/phpagi.php');

//Create a new AGI Class
$agi = new AGI();

//Username Details Catcher
$uniqueid = $agi->request['agi_uniqueid'];
$userURI = $agi->get_variable("SIPURI",true);
$userURI = explode("@", $userURI);
$userURI = explode(":", $userURI[1]);
$userIPURI = $userURI[0];

$sipusercontact = $agi->get_variable("SIP_HEADER(Contact)",true);
$sipusercontact = explode("@", $sipusercontact);
$sipusercontact = explode(":", $sipusercontact[1]);
$sipusercontact = $sipusercontact[0];

$sipusername = $agi->get_variable("SIP_HEADER(From)",true);
$sipusername = explode(":",$sipusername);
$sipusername = explode("@",$sipusername[1]);
$trunkUsername = $sipusername[0];
$trunkIPAddress = explode(">",$sipusername[1]);
$trunkIPAddress = $trunkIPAddress[0];

//Get account code
$callerid = $agi->request['agi_accountcode'];
if($callerid == ''){ $callerid = $agi->request['agi_rdnis']; }
if($callerid == 'BY_CALLER_ID'){ $callerid = $agi->request['agi_callerid']; }

sendLog("############################################################################################", $agi);

sendLog("########## [$uniqueid] <$trunkUsername> URI IP <$userIPURI>", $agi);
sendLog("########## [$uniqueid] <$trunkUsername> IP FROM CONTACT HEADER <$sipusercontact>", $agi);
sendLog("########## [$uniqueid] <$trunkUsername> IP FROM FROM HEADER: $trunkIPAddress", $agi);

$agi->exec("SIPAddHeader","X-Client-Address:$userIPURI");

sendLog("############################################################################################", $agi);

exit();

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function sendLog($message, $agi) {

	if(DEBUG) syslog(LOG_INFO, "< KABOOK - AUTH > - $message");
	if(DEBUG) $agi->noop($message);

}

?>
