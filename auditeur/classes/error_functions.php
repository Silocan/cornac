<?php

class error_functions extends functioncalls {
	protected	$title = 'Fonctions d\'erreur';
	protected	$description = 'Liste des fonctions de l\'extension errorfunc de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = array('debug_backtrace', 
	                             'debug_print_backtrace', 
	                             'error_get_last', 
	                             'error_log', 
	                             'error_reporting', 
	                             'restore_error_handler', 
	                             'restore_exception_handler', 
	                             'set_error_handler', 
	                             'set_exception_handler', 
	                             'trigger_error', 
	                             'user_error');
	    parent::analyse();
	    
	    return true;
	}
}

?>