<?php

class xdebug_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de ereg et associées';
	protected	$description_en = 'usage of ereg and co. functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('xdebug_get_stack_depth', 'xdebug_get_function_stack', 'xdebug_print_function_stack', 'xdebug_get_declared_vars', 'xdebug_call_class', 'xdebug_call_function', 'xdebug_call_file', 'xdebug_call_line', 'xdebug_var_dump', 'xdebug_debug_zval', 'xdebug_debug_zval_stdout', 'xdebug_enable', 'xdebug_disable', 'xdebug_is_enabled', 'xdebug_break', 'xdebug_start_trace', 'xdebug_stop_trace', 'xdebug_get_tracefile_name', 'xdebug_get_profiler_filename', 'xdebug_dump_aggr_profiling_data', 'xdebug_clear_aggr_profiling_data', 'xdebug_memory_usage', 'xdebug_peak_memory_usage', 'xdebug_time_index', 'xdebug_start_code_coverage', 'xdebug_stop_code_coverage', 'xdebug_get_code_coverage', 'xdebug_get_function_count', 'xdebug_dump_superglobals');
	    parent::analyse();
	}
}

?>
