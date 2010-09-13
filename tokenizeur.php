#!/usr/bin/php
<?php

// @synopsis : read options
$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => false),
                 'templates' => array('help' => 'output templates',
                                 'get_arg_value' => 'tree',
                                 'option' => 'g',
                                 'compulsory' => false),
                 'recursive' => array('help' => 'recursive mode',
                                      'option' => 'r',
                                      'compulsory' => false),
                 'file' => array('help' => 'file to work on',
                                 'get_arg_value' => null,
                                 'option' => 'f',
                                 'compulsory' => false),
                 'log' => array('help' => 'log activity',
                                          'option' => 'l',
                                          'compulsory' => false),
                 'tokens' => array('help' => 'only show tokens',
                                          'option' => 't',
                                          'compulsory' => false),
                 'directory' => array('help' => 'directory to work in',
                                      'get_arg_value' => null,
                                      'option' => 'd',
                                      'compulsory' => false),
                 );
include('libs/getopts.php');

$shell = './tokinit.php -I '.$INI['ini'].' -g '.$INI['templates'].' -K -r -d '.$INI['directory'];
if (isset($INI['directory'])) {
    shell_exec($shell.' -d '.$INI['directory']);
} elseif (isset($INI['file'])) {
    shell_exec($shell.' -f '.$INI['file']);
}

$ini = ' ';
if ($INI['log']) {
    $ini .= " -l ";
}

if ($INI['tokens']) {
    $ini .= " -t ";
}


print shell_exec('./tokclient.php -I '.$INI['ini'].$ini);

?>