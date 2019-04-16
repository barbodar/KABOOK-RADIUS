#!/usr/bin/php -q
<?php

error_reporting(E_ALL);

for($counter=0 ; $counter <30; $counter++)  {
    
	$startResult = trim(shell_exec("ps ax | grep kabook-acct-engine-start | grep php -c"));
    
    if( $startResult === '2') {
        syslog(LOG_INFO,"< KABOOK ENGINE CHECK > - KABOOK ACCT START is running ... [{$counter}]");
        echo "KABOOK ACCT START is running ... [{$counter}]\n";
    } else {
        syslog(LOG_INFO,"< KABOOK ENGINE CHECK > - KABOOK ACCT START is stopped... trying to start it now... [{$counter}]");
        echo "KABOOK ACCT START is stopped... trying to start it now... [{$counter}]\n";
        shell_exec('/usr/bin/php /opt/kabook-radius/kabook-acct-engine-start.php > /dev/null 2>/dev/null &');
    }

    sleep(1);
    
    $stopResult = trim(shell_exec("ps ax | grep kabook-acct-engine-stop | grep php -c"));
    
    if( $stopResult === '2') {
        syslog(LOG_INFO,"< KABOOK ENGINE CHECK > - KABOOK ACCT STOP is running ... [{$counter}]");
        echo "KABOOK ACCT STOP is running ... [{$counter}]\n";
    } else { 
        syslog(LOG_INFO,"< KABOOK ENGINE CHECK > - KABOOK ACCT STOP is stopped... trying to start it now... [{$counter}]");
        echo "KABOOK ACCT STOP is stopped... trying to start it now... [{$counter}]\n";
        shell_exec('/usr/bin/php /opt/kabook-radius/kabook-acct-engine-stop.php > /dev/null 2>/dev/null &');
    }
    
    sleep(1);
}
