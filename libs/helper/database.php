<?php

// DB Functionality

class DB {

	private $host, $userName, $password, $dataBase;
	
	public function assignConfig($dbType) {

        switch($dbType) {
        
            case DB_TYPE_MYSQL:
                $this->host = MYSQL_HOST;
                $this->userName = MYSQL_USERNAME;
                $this->password = MYSQL_PASSWORD;
                $this->dataBase = MYSQL_DATABASE;
                break;
            
            case DB_TYPE_POSTGRE:
                $this->host = POSTGRE_HOST;
                $this->userName = POSTGRE_USERNAME;
                $this->password = POSTGRE_PASSWORD;
                $this->dataBase = POSTGRE_DATABASE;
                break;
        }
	}
	
	public function connect() {
		$dsn = "mysql:host=".$this->host.";dbname=".$this->dataBase;
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
		try {
			$dbConn = new PDO($dsn, $this->userName, $this->password, $options);
		} catch (PDOException $e) {
    		print "Error!: " . $e->getMessage();
    		die();
		}
        return $dbConn;
	}
	
	public function close($dbConn) {
		$dbConn = NULL;
	}
    
    public function insertNewCall($dbConn, $data) {
    
        $queryStr = "INSERT INTO `tblOnlineCalls` (`onlineCalls_direction`, `onlineCalls_startDate`, `onlineCalls_startTime`, `onlineCalls_channel`, `onlineCalls_uniqueid`, `onlineCalls_callerid`, `onlineCalls_calleridname`, `onlineCalls_dnid`, 
        `onlineCalls_rdnis`, `onlineCalls_context`, `onlineCalls_extension`, `onlineCalls_accountcode`, `onlineCalls_threadid`, `onlineCalls_confId`, `onlineCalls_nasPort`, `onlineCalls_clientAddress`)
        VALUE (:onlineCalls_direction, now(), :onlineCalls_startTime, :onlineCalls_channel, :onlineCalls_uniqueid, :onlineCalls_callerid, :onlineCalls_calleridname, :onlineCalls_dnid, :onlineCalls_rdnis, :onlineCalls_context, :onlineCalls_extension, 
        :onlineCalls_accountcode, :onlineCalls_threadid, :onlineCalls_confId, :onlineCalls_nasPort, :onlineCalls_clientAddress)";
        
        $query = $dbConn->prepare($queryStr);
        $query->bindParam(':onlineCalls_direction', $data['direction'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_startTime', $data['time'], PDO::PARAM_INT);
        $query->bindParam(':onlineCalls_channel', $data['channel'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_uniqueid', $data['uniqueid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_callerid', $data['callerid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_calleridname', $data['calleridname'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_dnid', $data['dnid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_rdnis', $data['rdnis'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_context', $data['context'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_extension', $data['extension'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_accountcode', $data['accountcode'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_threadid', $data['threadid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_confId', $data['confId'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_nasPort', $data['nasPort'], PDO::PARAM_INT);
        $query->bindParam(':onlineCalls_clientAddress', $data['clientAddress'], PDO::PARAM_STR);
        $query->execute();
        return $query->errorInfo();
    }
    
    public function updateCall($dbConn, $data) {
    
        $queryStr = "UPDATE `tblOnlineCalls` SET `onlineCalls_stopDate` = FROM_UNIXTIME(`onlineCalls_startTime` + :onlineCalls_totalDuration), `onlineCalls_stopTime` = (`onlineCalls_startTime` + :onlineCalls_totalDuration), 
        `onlineCalls_carrierAddress` = :onlineCalls_carrierAddress, `onlineCalls_hangupCause` = :onlineCalls_hangupCause, `onlineCalls_totalDuration` = :onlineCalls_totalDuration, `onlineCalls_billedDuration` = :onlineCalls_billedDuration, 
        `onlineCalls_disposition` = :onlineCalls_disposition, `onlineCalls_isProcessed` = :onlineCalls_isProcessed WHERE `onlineCalls_uniqueid` = :onlineCalls_uniqueid";
    
        $query = $dbConn->prepare($queryStr);
        $query->bindParam(':onlineCalls_totalDuration', $data['totalDuration'], PDO::PARAM_INT);
        $query->bindParam(':onlineCalls_billedDuration', $data['billedDuration'], PDO::PARAM_INT);
        $query->bindParam(':onlineCalls_carrierAddress', $data['carrierAddress'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_hangupCause', $data['hangupCause'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_hangupCause', $data['hangupCause'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_disposition', $data['disposition'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_uniqueid', $data['uniqueid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_isProcessed', $data['callProcessStage'], PDO::PARAM_INT);
        $query->execute();
        return $query->errorInfo();
    }
    
    public function getCalls($dbConn, $data) {
        
        $queryStr = "SELECT * FROM `tblOnlineCalls` WHERE `onlineCalls_isProcessed` = :onlineCalls_isProcessed ORDER BY `onlineCalls_startDate` ASC";
        
        $query = $dbConn->prepare($queryStr);
        $query->bindParam(':onlineCalls_isProcessed', $data['callProcessStage'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateCallStatus($dbConn, $data) {
    
        $queryStr = "UPDATE `tblOnlineCalls` SET `onlineCalls_isProcessed` = :onlineCalls_isProcessed WHERE `onlineCalls_uniqueid` = :onlineCalls_uniqueid";
        
        $query = $dbConn->prepare($queryStr);
        $query->bindParam(':onlineCalls_isProcessed', $data['callProcessStage'], PDO::PARAM_INT);
        $query->bindParam(':onlineCalls_uniqueid', $data['uniqueid'], PDO::PARAM_STR);
        $query->execute();
        return $query->errorInfo();
    }
    
    public function archiveCall($dbConn, $data) {
    
        $queryStr = "INSERT INTO `tblOnlineCallsArchive` (`onlineCalls_direction`, `onlineCalls_startDate`, `onlineCalls_startTime`, `onlineCalls_stopDate`, `onlineCalls_stopTime`, `onlineCalls_channel`, `onlineCalls_uniqueid`, `onlineCalls_callerid`, `onlineCalls_calleridname`, `onlineCalls_dnid`, `onlineCalls_rdnis`, `onlineCalls_context`, `onlineCalls_extension`, `onlineCalls_accountcode`, `onlineCalls_threadid`, `onlineCalls_confId`, `onlineCalls_nasPort`, `onlineCalls_clientAddress`, `onlineCalls_carrierAddress`, `onlineCalls_hangupCause`, `onlineCalls_totalDuration`, `onlineCalls_billedDuration`, `onlineCalls_disposition`) SELECT `onlineCalls_direction`, `onlineCalls_startDate`, `onlineCalls_startTime`, `onlineCalls_stopDate`, `onlineCalls_stopTime`, `onlineCalls_channel`, `onlineCalls_uniqueid`, `onlineCalls_callerid`, `onlineCalls_calleridname`, `onlineCalls_dnid`, `onlineCalls_rdnis`, `onlineCalls_context`, `onlineCalls_extension`, `onlineCalls_accountcode`, `onlineCalls_threadid`, `onlineCalls_confId`, `onlineCalls_nasPort`, `onlineCalls_clientAddress`, `onlineCalls_carrierAddress`, `onlineCalls_hangupCause`, `onlineCalls_totalDuration`, `onlineCalls_billedDuration`, `onlineCalls_disposition` FROM `tblOnlineCalls` WHERE `onlineCalls_isProcessed` = :onlineCalls_isProcessed AND `onlineCalls_uniqueid` = :onlineCalls_uniqueid";
    
        $query = $dbConn->prepare($queryStr);
        $query->bindParam(':onlineCalls_uniqueid', $data['uniqueid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_isProcessed', $data['callProcessStage'], PDO::PARAM_INT);
        $query->execute();
        return $query->errorInfo();
    }
    
    public function removeOnlineCall($dbConn, $data) {
    
        $queryStr = "DELETE FROM `tblOnlineCalls` WHERE `onlineCalls_isProcessed` = :onlineCalls_isProcessed AND `onlineCalls_uniqueid` = :onlineCalls_uniqueid";
        $query = $dbConn->prepare($queryStr);
        $query->bindParam(':onlineCalls_uniqueid', $data['uniqueid'], PDO::PARAM_STR);
        $query->bindParam(':onlineCalls_isProcessed', $data['callProcessStage'], PDO::PARAM_INT);
        $query->execute();
        return $query->errorInfo();
    }
}
