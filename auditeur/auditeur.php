#!/usr/bin/php
<?php

include('../libs/getopts.php');
include('../libs/write_ini_file.php');

$args = $argv;

$help = get_arg($args, '-?') ;
if ($help) { help(); }

// default values, stored in a INI file
$ini = get_arg_value($args, '-I', null);
if (!is_null($ini)) {
    global $INI;
    if (file_exists('../ini/'.$ini)) {
        define('INI','../ini/'.$ini);
    } elseif (file_exists('../ini/'.$ini.".ini")) {
        define('INI','../ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        define('INI','../ini/'.'cornac.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array();
}
unset($ini);

$INI['dependences'] = (bool) get_arg_value($args, '-d', false);


$modules = array(
'_new',
'affectations_variables',
//'appelsfonctions', // @_
'arobases',
'classes',
'classes_hierarchie',
'constantes',
'constantes_classes',
'defconstantes',
'deffunctions',
'doubledeffunctions',
'doubledefclass',
'defmethodes',
'dieexit',
'dir_functions',
'emptyfunctions',
'ereg_functions',
'error_functions',
'evals',
'exec_functions',
'execs',
'file_functions',
'filter_functions',
'functions_frequency',
'functions_undefined',
'functions_unused',
'functionscalls',
'globals',
'gpc',
'headers',
'iffectations',
'ifsanselse',
'image_functions',
'inclusions',
'inclusions2',
'ldap_functions',
'literals',
'method_special',
'methodscall',
//'modules_used', @_ Removed, double with php_modules
'mssql_functions',
'mysql_functions',
'mysqli_functions',
'nestedif',
'nestedloops',
'nonphp_functions',
'parentheses',
'php_functions',
'php_modules',
'proprietes_publiques',
'regex',
'returns',
'secu_functions',
'secu_protection_functions',
'session_functions',
'sql_queries',
'statiques',
'tableaux',
'tableaux_gpc',
'tableaux_gpc_seuls',
'thrown',
'trim_rawtext',
'undeffunctions',
'unused_args',
'vardump',
'variables',
'variablesvariables',
'xdebug_functions',
'xml_functions',
'zfAction',
'zfController',
'zfElements',
'zfGetGPC',
'properties_defined',
'properties_used',
'classes_unused',
'classes_undefined',
'html_tags', 
//'affectations_gpc', 
'classes_nb_methods',
'unused_properties',
'undefined_properties',
);

$INI['analyzers'] = get_arg_value($args, '-a', 'all');
if ($INI['analyzers'] == 'all' ) {
 // default : all modules
} else {
    var_dump($INI['analyzers']);
    $m = explode(',', $INI['analyzers']);

    $diff = array_diff($m , $modules);
    if (count($diff) > 0) {
        print count($diff)." analyzers are unknown and omitted : ".join(', ', $diff)."\n";
    }
    
    $m = array_intersect($m, $modules);    
    if (count($m) == 0) {
        print "No analyzer provided : aborting\n";
        die();
    } else {
        $modules = $m;
    }
} 
print count($modules)." modules will be treated : ".join(', ', $modules)."\n";
write_ini_file($INI, INI);

if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
    $database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);

//    $database->query('DELETE FROM '.$INI['template.mysql'].'_rapport WHERE fichier = "'.$fichier.'"');
    $database->query('CREATE TABLE IF NOT EXISTS '.$INI['cornac']['prefix'].'_rapport (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fichier` varchar(500) NOT NULL,
  `element` varchar(500) NOT NULL,
  `token_id` int(10) unsigned NOT NULL,
  `module` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `element` (`element`),
  KEY `module` (`module`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1');

//        $database->query('DELETE FROM '.$INI['template.mysql']['table'].'_rapport_dot WHERE fichier = "'.$fichier.'"');
        $database->query('CREATE TABLE IF NOT EXISTS '.$INI['cornac']['prefix'].'_rapport_dot (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

//        $database->query('DELETE FROM '.$INI['template.mysql']['table'].'_rapport_module WHERE fichier = "'.$fichier.'"');
        $database->query('CREATE TABLE IF NOT EXISTS '.$INI['cornac']['prefix'].'_rapport_module (
  `module` varchar(255) NOT NULL,
  `fait` datetime NOT NULL,
  `format` enum("html","dot","gefx") NOT NULL,
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1');

} elseif (isset($INI['sqlite'])  && $INI['sqlite']['active'] == true) {
    $database = new pdo($INI['sqlite']['dsn']);
    
//    $database->query('DELETE FROM '.$INI['cornac']['prefix'].'_rapport WHERE fichier = "'.$fichier.'"');
    $database->query('CREATE TABLE IF NOT EXISTS '.$INI['cornac']['prefix'].'_rapport 
  (id       INTEGER PRIMARY KEY   AUTOINCREMENT  , 
  `fichier` varchar(500) NOT NULL,
  `element` varchar(500) NOT NULL,
  `token_id` int unsigned NOT NULL,
  `module` varchar(50) NOT NULL
)');
        
//    $database->query('DELETE FROM '.$INI['cornac']['prefix'].'_rapport_dot WHERE cluster = "'.$fichier.'"');
    $database->query('CREATE TABLE IF NOT EXISTS '.$INI['cornac']['prefix'].'_rapport_dot (
  `a` varchar(255) NOT NULL,
  `b` varchar(255) NOT NULL,
  `cluster` varchar(255) NOT NULL DEFAULT \'\',
  `module` varchar(255) NOT NULL
)');

    $database->query('CREATE TABLE IF NOT EXISTS '.$INI['cornac']['prefix'].'_rapport_module (
  `module` varchar(255) NOT NULL PRIMARY KEY,
  `fait` datetime NOT NULL,
  `format` varchar(255) NOT NULL
)');
} else {
    print "No database configuration provided (no mysql, no sqlite)\n";
    die();
}

// validation done


// rendu (templates) @_
include 'classes/sommaire.php';
$sommaire = new sommaire();

// @inclusions abstract classes 
include 'classes/abstract/modules.php';
include 'classes/abstract/functioncalls.php';
include 'classes/abstract/typecalls.php';
include 'classes/abstract/noms.php';

$modules_faits = array();

// @todo the init could take into account the current content of the database, avoiding reprocess

foreach($modules as $module) {
    print "+ $module\n";
    analyse_module($module);
}

function analyse_module($module) {
    require_once('classes/'.$module.'.php');
    global $modules_faits, $database,$sommaire, $INI;
    
    if (isset($modules_faits[$module])) {  
        return ;
    }

    $x = new $module($database);
    $dependances = $x->dependsOn();
    
    if (count($dependances) > 0) {
        $manque = array_diff($dependances, $modules_faits);
        if (count($manque) > 0) {
            foreach($manque as $m) {
                print "  +  $m";
                if ($INI['dependences']) {
                    analyse_module($m);
                } else {
                    print " omitted ";
                    // @todo check if dependances are there or not. 
                    // @todo if not, they should be done, of course!
                }
                print "\n";
            }
        } else {
            print "Dépendances already processed\n";
        }
    }
    
    $x->analyse();
    $x->sauve(); 
    
    $sommaire->add($x);
    $modules_faits[$module] = 1;
}

$sommaire->sauve();

function help() {
    print <<<TEXT
Usage : ./auditeur.php
prefix : tokens (default)

    -?    : this help
    -a    : comma separated list of analyzers to be used. Defaut to all. 
    -d    : refresh dependent analyzers (default : no)
    -f    : output format : html
    -o    : folder for output : /tmp
    -I    : ini config file

TEXT;
    
    die();
}

?>