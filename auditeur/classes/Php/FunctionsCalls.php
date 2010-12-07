<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class Php_FunctionsCalls extends modules {
    protected $description = "Function calls"; 
    protected $title = "List all PHP function calls"; 

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_report();

	    $total = modules::getPHPFunctions();
	    $in = join("', '", $total);

        $query = <<<SQL
SELECT NULL, T1.file, T2.code AS code, T1.id, '{$this->name}', 0
  FROM <tokens> T1
  JOIN <tokens> T2
       ON T1.file = T2.file AND
          T1.left = T2.left - 1
  LEFT JOIN <tokens_tags> TT
       ON T1.id = TT.token_sub_id
where 
 T1.type='functioncall' AND
( TT.token_id IS NULL OR TT.type != 'method') AND
T2.code NOT IN ('$in')
SQL;

        $this->exec_query_insert('report', $query);
        return true;
	}
}

?>