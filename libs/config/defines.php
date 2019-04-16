<?php

//PHP Errors
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('error_reporting', "none");

//Date Define
    date_default_timezone_set ( 'Asia/Tehran' );
    
//Main Engine defaults
    define('DEBUG', false);
    define('DB_TYPE_MYSQL', 'mysql');
    define('DB_TYPE_POSTGRE', 'postgre');
    define('SLEEP_STEP_1', 1000000); //microsecond
    define('SLEEP_STEP_2', 1000000); //microsecond
    define('LOOP_TIME', 28); //dual seconds
    
//Database Connection Setting
    //MYSQL
    define('MYSQL_HOST', 'localhost');
    define('MYSQL_USERNAME', 'kabook');
    define('MYSQL_PASSWORD', 'kabookpass');
    define('MYSQL_DATABASE', 'kabook-radius');
    define('MYSQL_SUCCESS_RESULT', '00000');
    define('MYSQL_PROCESSED_CALL_NOTPROCESSED', 1);
    define('MYSQL_PROCESSED_CALL_START', 2);
    define('MYSQL_PROCESSED_CALL_STOP', 3);
    define('MYSQL_PROCESSED_CALL_ARCHIVED', 4);
    
    //POSTGRE
    define('POSTGRE_HOST', 'localhost');
    define('POSTGRE_USERNAME', 'kabook');
    define('POSTGRE_PASSWORD', 'kabookpass');
    define('POSTGRE_DATABASE', 'kabook-radius');

//RADIUS SETTING
    define('RADIUS_NAS_IP','1.1.1.1');
    define('RADIUS_SERVER','1.1.1.2');
    define('RADIUS_SERVER_AUTH_PORT','1812');
    define('RADIUS_SERVER_ACCT_PORT','1813');
    define('RADIUS_TIMEOUT', 3);
    define('RADIUS_MAX_TRIES', 1);
    define('RADIUS_SECRET', 'kabooksecret');
    define('RADIUS_NAS_IDENT', 'kabook');
    
//RADIUS ATTRIBUTE
    define('RADIUS_EVENT_TIMESTAMP', 55);
    define('RADIUS_ACCT_DELAY_TIME', 41);
    define('RADIUS_VENDOR_CISCO', 9);
    define('RADIUS_CISCO_H323_REMOTE_ADDRESS', 23);
    define('RADIUS_CISCO_H323_CONF_ID', 24);
    define('RADIUS_CISCO_H323_SETUP_TIME', 25);
    define('RADIUS_CISCO_H323_CALL_ORIGIN', 26);
    define('RADIUS_CISCO_H323_CALL_TYPE', 27);
    define('RADIUS_CISCO_H323_CONNECT_TIME', 28);
    define('RADIUS_CISCO_H323_DISCONNECT_TIME', 29);
    define('RADIUS_CISCO_H323_DISCONNECT_CAUSE', 30);
    define('RADIUS_CISCO_H323_CREDIT_AMOUNT', 101);
    define('RADIUS_CISCO_H323_CREDIT_TIME', 102);
    define('RADIUS_CISCO_H323_RETURN_CODE', 103);
    define('DEFAULT_HANGUP_CAUSE', 16); //Normal Call clearing
    
//H323-CODES
    define('H323_RETURN_CODE_SUCCESS', 0);
