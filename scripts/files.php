#!/usr/bin/env php
<?php


include('../libs/getopts.php');
include('../libs/write_ini_file.php');

$args = $argv;
if (get_arg($args, '-f')) { define('SHOW_FILES','true'); }
if (get_arg($args, '-d')) { define('SHOW_DIRS','true'); }
if (get_arg($args, '-e')) { define('SHOW_DIRS','true'); }

if ($format = get_arg_value($args, '-F', 'print_r')) {
    if (!in_array($format, array('print_r','csv','xml'))) { $format = 'print_r'; }
    define('FORMAT', $format);
}
if ($dir = get_arg_value($args, '-D', '.')) {
    if (!file_exists($dir)) { print "'$dir' doesn't exist\n"; die(); }
    define('DIR', $dir);
}

chdir($dir);

$liste = liste_directories_recursive('.');


$dirs = array_map('dirname', $liste);
$dirs = array_count_values($dirs);
if (SHOW_DIRS) { display($dirs); }

$files = array_map('basename', $liste);
$files = array_count_values($files);
if (SHOW_FILES) { display($files); }

$exts = array_map('cb_exts', $liste);
$exts = array_count_values($exts);
if (SHOW_EXTS) { display($exts); }

//print count($liste)." fichiers distincts\n";

function cb_exts($filename) {
    $filename = basename($filename);
    $pos = strrpos($filename, '.');
    return substr($filename, $pos);
}

function display($list) {
    if (FORMAT == 'print_r') {
        print_r($list);
    } elseif (FORMAT == 'xml') {
        print "<list>\n    <file>".join("</file>\n    <file>", $list)."</file>\n</list>\n";
    } elseif (FORMAT == 'csv') {
        print '"'.join("\"\n\"", $list).'"';
    }

    return true;
}
?>