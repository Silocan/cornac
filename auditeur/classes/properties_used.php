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

class properties_used extends modules {
	protected	$title = 'Propriétés utilisées';
	protected	$description = 'Propriétés utilisées par une classe';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

        $concat = $this->concat("T2.class","'->'","T3.code");
// @note this are the properties used within the class : we rely on $this
        $query = <<<SQL
SELECT NULL, T1.fichier, $concat AS code, T2.id, '{$this->name}', 0 
FROM <tokens> T1
  JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND TT.type='object' 
  JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id AND T2.code='\$this'
  JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND TT2.type='property'
  JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND TT2.token_sub_id = T3.id
  WHERE T1.type='property';
SQL;
        $this->exec_query_insert('rapport',$query);


// @note this are the other properties used within the class : we don't know what to do now 
        $concat = $this->concat("T2.code","'->'","T3.code"); 

        $query = <<<SQL
SELECT NULL, T1.fichier, $concat AS code, T2.id, '{$this->name}' , 0
FROM <tokens> T1
  JOIN <tokens_tags> TT 
    ON TT.token_id = T1.id AND TT.type='object' 
  JOIN <tokens> T2
    ON T1.fichier = T2.fichier AND TT.token_sub_id = T2.id AND T2.code!='\$this'
  JOIN <tokens_tags> TT2
    ON TT2.token_id = T1.id AND TT2.type='property'
  JOIN <tokens> T3
    ON T1.fichier = T3.fichier AND TT2.token_sub_id = T3.id
  WHERE T1.type='property';
SQL;
        $this->exec_query_insert('rapport',$query);
    
    }
}

?>