<?php


class Cornac_Log {
    private $name = 'tokenizer';
    private $file = '';
    private $path = 'log';
    static $logs = array();
    private $mode = 'w+';
    
    const REWRITE = "w+";
    const ADD = "a";
    
    
    function __construct($name = "tokenizer", $mode = "w+") {
        $this->name = $name;
        $this->setMode($mode);
        
        // @todo check for path value (__FILE__ or INI)

        if (basename($_SERVER['PWD']) == 'auditeur') {
            $this->path = '../log';
        }

        if ($this->name != 'null') {
            $this->file =  fopen($this->path.'/'.$this->name.'.log',$this->mode);
        }
        Cornac_Log::$logs[$this->name] = $this;
    }
    
    function __destruct() {
        if (is_resource($this->file)) {
            fclose($this->file);
        }
    }

    function setMode($mode) {
        if ($mode == Cornac_Log::ADD) {
            $this->mode = Cornac_Log::ADD;
        } else {
            $this->mode = Cornac_Log::REWRITE;
        }
    }

    function log($message) {
        // @todo either object exists, either this doesn't. 
        if (!LOG) { return true; }
        
        // @todo this is too harsh : may be display a warning
        // @todo make this configurable some way
        if (is_resource($this->file)) {
            fwrite($this->file, date('r')."\t".memory_get_usage()."\t$message\r");
        }

        return true; 
    }
    
    static public function getInstance($name = "null") {
        if (isset(Cornac_Log::$logs[$name])) {
            return Cornac_Log::$logs[$name];
        } else {
            if (!isset(Cornac_Log::$logs['null'])) {
                Cornac_Log::$logs['null'] = new Cornac_Log('null');
            }
            return Cornac_Log::$logs['null'];
        }
    }
}

?>