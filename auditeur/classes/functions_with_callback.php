<?php

class functions_with_callback extends modules {
	protected	$title = 'Fonctions avec callback';
	protected	$description = 'Liste des fonctions PHP utilisant une fontion de callback';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('functionscalls');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $functions = array();
        // callback is in second position
        $functions[1] = array('array_map',
                              'call_user_func',
                              'call_user_func_array');
        // callback is in second position
        $functions[2] = array('usort', 
                              'preg_replace_callback',
                              'uasort',
                              'uksort',
                              'array_reduce',
                              'array_walk',
                              'array_walk_recursive',
                              'mysqli_set_local_infile_handler',
                              );
        // callback is in last position
        $functions[-1] = array('array_diff_uassoc',
                               'array_diff_ukey',
                               'array_intersect_uassoc',
                               'array_intersect_ukey',
                               'array_udiff_assoc',
                               'array_udiff_uassoc',
                               'array_udiff',
                               'array_uintersect_assoc',
                               'array_uintersect_uassoc',
                               'array_uintersect',
                               'array_filter',
                               'array_reduce',
                            );

        $functions = array_merge($functions[1], $functions[2], $functions[-1]);
        
        $functions = "'".join("', '", $functions)."'";

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element, TR1.id, '{$this->name}', 0
    FROM <rapport> TR1
    WHERE TR1.module="php_functions" AND 
          TR1.element IN ($functions)
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>