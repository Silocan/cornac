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

class classes_unused extends modules {
	protected	$title = 'Unused classes';
	protected	$description = 'Unused classes';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('classes','_new','classes_hierarchie');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}', 0
FROM <rapport>  TR1
LEFT JOIN <rapport>  TR2 
    ON TR1.element = TR2.element AND TR2.module='_new' 
WHERE TR1.module = 'classes' AND TR2.module IS NULL
SQL;
        $this->exec_query_insert('rapport', $query);

// @note we need to check extensions : we have them in the dot rapport, from classes_hierarchie
        $query = <<<SQL
SELECT TRD.a
FROM <rapport>  TR1
JOIN <rapport_dot> TRD
      ON TRD.b = TR1.element
WHERE TR1.module = '_new' AND 
      TRD.module = 'classes_hierarchie'
SQL;
        $res = $this->exec_query($query);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $query = <<<SQL
DELETE FROM <rapport> 
WHERE module='{$this->name}' AND element IN ('$in')
SQL;
        $res = $this->exec_query($query);

// @note same as above, but with 2 levels for extensions
        $query = <<<SQL
SELECT TRD2.a
FROM <rapport>  TR1
JOIN <rapport_dot> TRD1
    ON TRD1.b = TR1.element
JOIN <rapport_dot> TRD2
    ON TRD2.b = TRD1.a
WHERE TR1.module = '_new' AND 
      TRD1.module = 'classes_hierarchie' AND
      TRD2.module = 'classes_hierarchie'
SQL;

        $res = $this->exec_query($query);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $query = <<<SQL
DELETE FROM <rapport> 
WHERE module='{$this->name}' AND element IN ('$in')
SQL;
        $res = $this->exec_query($query);

// @note same as above, but with 3 levels for extensions
        $query = <<<SQL
SELECT TRD3.a
FROM <rapport>  TR1
JOIN <rapport_dot> TRD1
    ON TRD1.b = TR1.element
JOIN <rapport_dot> TRD2
    ON TRD2.b = TRD1.a
JOIN <rapport_dot> TRD3
    ON TRD3.b = TRD2.a
WHERE TR1.module = '_new' AND 
      TRD1.module = 'classes_hierarchie' AND
      TRD2.module = 'classes_hierarchie' AND 
      TRD3.module = 'classes_hierarchie'          
SQL;

        $res = $this->exec_query($query);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $query = <<<SQL
DELETE FROM <rapport> 
WHERE module='{$this->name}' AND
      element IN ('$in')
SQL;
        $res = $this->exec_query($query);

// @note same as above, but with 4 levels for extensions
        $query = <<<SQL
SELECT TRD4.a
FROM <rapport>  TR1
JOIN <rapport_dot> TRD1
    ON TRD1.b = TR1.element
JOIN <rapport_dot> TRD2
    ON TRD2.b = TRD1.a
JOIN <rapport_dot> TRD3
    ON TRD3.b = TRD2.a
JOIN <rapport_dot> TRD4
    ON TRD4.b = TRD3.a
WHERE TR1.module = '_new' AND 
      TRD1.module = 'classes_hierarchie' AND
      TRD2.module = 'classes_hierarchie' AND 
      TRD3.module = 'classes_hierarchie' AND 
      TRD4.module = 'classes_hierarchie'          
SQL;
        $res = $this->exec_query($query);
        $extensions = $res->fetchAll(PDO::FETCH_COLUMN,0);
        $in = join("', '", $extensions);


        $query = <<<SQL
DELETE FROM <rapport> 
WHERE module='{$this->name}' AND 
      element IN ('$in')
SQL;
        $res = $this->exec_query($query);

// @attention may we need some more queries, with more joins, or a clever while loop. Up to now, it's sufficient

        return true;
	}
}

?>