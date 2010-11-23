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

class function_link extends modules {
	protected	$title = 'Lien entre les files via les fonctions';
	protected	$description = 'Établit les liens entre deux files, pour via une fonction : un lien est établit entre le file de défintion de la fonction, et son utilisation';

	function __construct($mid) {
        parent::__construct($mid);

        $this->format = modules::FORMAT_DOT;
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('functionscalls','deffunctions');
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
INSERT INTO <rapport_dot>
SELECT TR1.file, TR2.file, TR1.element, '{$this->name}'
FROM <rapport>  TR1
JOIN <rapport> TR2
    ON TR2.module = 'functionscalls' AND
       TR2.element = TR1.element
WHERE TR1.module='deffunctions';
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>