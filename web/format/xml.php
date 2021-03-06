<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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
function get_html_check($lines) {
        $xml = '<xml>
';
        foreach($lines as $line) {
            $line['element'] = htmlentities($line['element']);
            $xml .= <<<XML
    <row>
        <element>{$line['element']}</element>
        <count>{$line['nb']}</count>
    </row>

XML;
        }
        $xml .= "</xml>";

        global $prefix;
        $xml = print_entete($prefix).$xml.print_pieddepage($prefix);
        
        return $xml;
}

function get_html_level2($lines) {
        $xml = '<xml>
';
        
        foreach($lines as $file => $rows) {
            $file = htmlentities($file, ENT_COMPAT, 'UTF-8');
            $nb = count($rows);
            $xml .= <<<XML
    <row>
        <file>$file</file>
        <elements count="$nb">

XML;
            foreach($rows as $row) {
                $row['nb'] = htmlentities($row['nb'], ENT_COMPAT, 'UTF-8');
                $row['element'] = htmlentities($row['element'], ENT_COMPAT, 'UTF-8');
                $xml .= <<<XML
            <occurrence>
                <element>{$row['element']}</element>
                <count>{$row['nb']}</count>
            </occurrence>

XML;
            }
            $xml .= <<<XML
        </elements>
    </row>

XML;
        }
        $xml .= "</xml>";

        global $prefix;
        $xml = print_entete($prefix).$xml.print_pieddepage($prefix);
        
        return $xml;
}        

    
function print_entete($prefix='No Name') {
    global $entete;
    
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>

XML;

}

function print_pieddepage($prefix='No Name') {
    return <<<XML
XML;
}
?>