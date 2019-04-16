<?php

class RADIUS {
    
    public $nasPort;
    public $eventTime;
    public $acctSessionId;
    public $h323_conf_id;
    
    //Radius Authentincation
    public function authenticate($username, $password, $destination) {
        
        $radiusAuth = radius_auth_open() or die ("Could not create Authentication handle");
        radius_add_server( $radiusAuth, RADIUS_SERVER, RADIUS_SERVER_AUTH_PORT, RADIUS_SECRET, RADIUS_TIMEOUT, RADIUS_MAX_TRIES );
        radius_create_request( $radiusAuth, RADIUS_ACCESS_REQUEST );
        radius_put_attr( $radiusAuth, RADIUS_ACCT_SESSION_ID, $this->acctSessionId );
        radius_put_int( $radiusAuth, RADIUS_NAS_PORT_TYPE, RADIUS_ASYNC );
        radius_put_attr( $radiusAuth, RADIUS_USER_NAME, $username );
        radius_put_vendor_string( $radiusAuth, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CONF_ID, $this->h323_conf_id ); //h323-conf-id
        radius_put_attr( $radiusAuth, RADIUS_CALLED_STATION_ID, $destination );
        radius_put_attr( $radiusAuth, RADIUS_CALLING_STATION_ID, $username );
        radius_put_int( $radiusAuth, RADIUS_NAS_IP_ADDRESS, ip2long(RADIUS_NAS_IP) );
        radius_put_string( $radiusAuth, RADIUS_NAS_IDENTIFIER, RADIUS_NAS_IDENT );
        radius_put_int( $radiusAuth, RADIUS_NAS_PORT, $this->nasPort);
        radius_put_int( $radiusAuth, RADIUS_EVENT_TIMESTAMP, $this->eventTime); //Event-Timestamp
        radius_put_attr( $radiusAuth, RADIUS_USER_PASSWORD, $password );
        
        $authResponse[RADIUS_ACCESS_REQUEST] = radius_send_request( $radiusAuth );
        
        while( $resa = radius_get_attr( $radiusAuth ) ) {
            
            $attr = $resa['attr'];
            $data = $resa['data'];
            
            if($attr === RADIUS_CLASS ) { $authResponse[RADIUS_CLASS] = $data; }
            
            if ($attr == RADIUS_VENDOR_SPECIFIC) {

                $resv = radius_get_vendor_attr($data);
                
                if (is_array($resv)) {
                    
                    $vendor = $resv['vendor'];
                    $attrv = $resv['attr'];
                    $datav = $resv['data'];    
                    if($attrv === RADIUS_CISCO_H323_CREDIT_AMOUNT) { $authResponse[RADIUS_CISCO_H323_CREDIT_AMOUNT] = explode('=', $datav)[1]; }
                    if($attrv === RADIUS_CISCO_H323_CREDIT_TIME) { $authResponse[RADIUS_CISCO_H323_CREDIT_TIME] = explode('=', $datav)[1]; }
                    if($attrv === RADIUS_CISCO_H323_RETURN_CODE) { $authResponse[RADIUS_CISCO_H323_RETURN_CODE] = explode('=', $datav)[1]; }
                }
                
            } else {
            
                //printf("Got Attr:%d %d Bytes %s\n", $attr, strlen($data), $data);
            }
        }
        
        radius_close( $radiusAuth );
        return $authResponse;
    }
    
    public function sendAccounting ($type, $data) {
    
        $billedDuration = $data['onlineCalls_billedDuration'];
		$totalDuration = $data['onlineCalls_totalDuration'];
        $startTime = $data['onlineCalls_startTime'];
        $setupTime = date("H:i:s.000 e D M d Y", $startTime );
        if($billedDuration > 0) {
            $connectTime = date("H:i:s.000 e D M d Y", $startTime + ($totalDuration - $billedDuration));
        } else {
            $connectTime = date("H:i:s.000 e D M d Y", $startTime);
        }
        $disconnectTime = date("H:i:s.000 e D M d Y", $startTime + $totalDuration);
        $hangupCause = dechex( DEFAULT_HANGUP_CAUSE );
        $radiusAcc = NULL;
        
        if($type === RADIUS_START ) {
        
		    $radiusAcc = radius_acct_open() or die ("Could not create START Accounting handle");
		    radius_add_server( $radiusAcc, RADIUS_SERVER, RADIUS_SERVER_ACCT_PORT, RADIUS_SECRET, RADIUS_TIMEOUT, RADIUS_MAX_TRIES );
		    radius_create_request( $radiusAcc, RADIUS_ACCOUNTING_REQUEST );
            radius_put_int( $radiusAcc, RADIUS_ACCT_STATUS_TYPE, RADIUS_START );
            radius_put_attr( $radiusAcc, RADIUS_ACCT_SESSION_ID, $this->acctSessionId );
            radius_put_attr( $radiusAcc, RADIUS_USER_NAME, $this->username );
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CONF_ID, $this->h323_conf_id ); //h323-conf-id
            radius_put_attr( $radiusAcc, RADIUS_CALLED_STATION_ID, $this->destination );
            radius_put_attr( $radiusAcc, RADIUS_CALLING_STATION_ID, $this->username );
            radius_put_int( $radiusAcc, RADIUS_NAS_IP_ADDRESS, ip2long(RADIUS_NAS_IP) );
            radius_put_string( $radiusAcc, RADIUS_NAS_IDENTIFIER, RADIUS_NAS_IDENT );
            radius_put_int( $radiusAcc, RADIUS_SERVICE_TYPE, RADIUS_LOGIN);
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_SETUP_TIME, $setupTime ); //h323-setup-time
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CALL_ORIGIN, "originate" ); //h323-call-origin
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CALL_TYPE, "VoIP" ); //h323-call-type
            radius_put_int( $radiusAcc, RADIUS_ACCT_DELAY_TIME, 0); //Acct-Delay-Time
            radius_put_int( $radiusAcc, RADIUS_NAS_PORT, $this->nasPort);
            radius_put_int( $radiusAcc, RADIUS_EVENT_TIMESTAMP, time()); //Event-Timestamp
		    
            $accountingResponse = radius_send_request( $radiusAcc );
            
            while( $resa = radius_get_attr( $radiusAcc ) ) {
            
                $attr = $resa['attr'];
                $data = $resa['data'];
                
                printf("Got Attr:%d %d Bytes %s\n", $attr, strlen($data), radius_cvt_int($data));
            }
            
            radius_close( $radiusAcc );
            return $accountingResponse;
        }
        
        if($type === RADIUS_STOP) {
        
            $remoteAddress = "{$data['onlineCalls_clientAddress']}:{$data['onlineCalls_carrierAddress']}";

            $radiusAcc = radius_acct_open() or die ("Could not create STOP Accounting handle");
            radius_add_server( $radiusAcc, RADIUS_SERVER, RADIUS_SERVER_ACCT_PORT, RADIUS_SECRET, RADIUS_TIMEOUT, RADIUS_MAX_TRIES );
            radius_create_request( $radiusAcc, RADIUS_ACCOUNTING_REQUEST );
            radius_put_int( $radiusAcc, RADIUS_ACCT_STATUS_TYPE, RADIUS_STOP );
            radius_put_attr( $radiusAcc, RADIUS_ACCT_SESSION_ID, $this->acctSessionId );
            radius_put_attr( $radiusAcc, RADIUS_USER_NAME, $this->username );
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CONF_ID, $this->h323_conf_id ); //h323-conf-id
            radius_put_attr( $radiusAcc, RADIUS_CALLED_STATION_ID, $this->destination );
            radius_put_attr( $radiusAcc, RADIUS_CALLING_STATION_ID, $this->username );
            radius_put_int( $radiusAcc, RADIUS_NAS_IP_ADDRESS, ip2long(RADIUS_NAS_IP) );
            radius_put_string( $radiusAcc, RADIUS_NAS_IDENTIFIER, RADIUS_NAS_IDENT );
            radius_put_int( $radiusAcc, RADIUS_SERVICE_TYPE, RADIUS_LOGIN);
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_REMOTE_ADDRESS, $remoteAddress ); //h323-remote-address
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_SETUP_TIME, $setupTime ); //h323-setup-time
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CALL_ORIGIN, "originate" ); //h323-call-origin
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CALL_TYPE, "VoIP" ); //h323-call-type
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_CONNECT_TIME, $connectTime ); //h323-connect-time
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_DISCONNECT_TIME, $disconnectTime ); //h323-disconnect-time
            radius_put_vendor_string( $radiusAcc, RADIUS_VENDOR_CISCO, RADIUS_CISCO_H323_DISCONNECT_CAUSE, $hangupCause ); //h323-disconnect-cause
            radius_put_int( $radiusAcc, RADIUS_ACCT_INPUT_OCTETS, 0 );
            radius_put_int( $radiusAcc, RADIUS_ACCT_OUTPUT_OCTETS, 0 );
            radius_put_int( $radiusAcc, RADIUS_ACCT_SESSION_TIME, $billedDuration );
            radius_put_int( $radiusAcc, RADIUS_ACCT_DELAY_TIME, 0); //Acct-Delay-Time
            radius_put_int( $radiusAcc, RADIUS_NAS_PORT, $this->nasPort);
            radius_put_int( $radiusAcc, RADIUS_EVENT_TIMESTAMP, time()); //Event-Timestamp
            
            $accountingResponse = radius_send_request( $radiusAcc );
            
            while( $resa = radius_get_attr( $radiusAcc ) ) {
            
                $attr = $resa['attr'];
                $data = $resa['data'];
                
                printf("Got Attr:%d %d Bytes %s\n", $attr, strlen($data), radius_cvt_int($data));
            }
            
            radius_close( $radiusAcc );
            return $accountingResponse;
        }
    }
}
