<?php

class LOGGER {

    public $agi, $moduleName;

    public function sendLog($message) {

	    if(DEBUG) syslog(LOG_INFO, "< {$this->moduleName} > - {$message}");
	    if(DEBUG && $this->agi != NULL) $this->agi->noop("< {$this->moduleName} > - {$message}");
        if(DEBUG && $this->agi == NULL) { printf("< {$this->moduleName} > - {$message}\r\n"); }
    }

}
