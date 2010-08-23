<?php
//ini_set('include_path', ".:".dirname(__FILE__));

require_once 'PHPUnit/Framework.php'; 

$tests = array( 
'class.variables.test.php',
'class.constantes.test.php',
'class._new.test.php',
'class.affectations_variables.test.php',
'class.headers.test.php',
'class.method_special.test.php',
'class.globals.test.php',
'class.iffectations.test.php',
'class.functions_unused.test.php',
'class.classes.test.php',
'class.arobases.test.php',
'class.properties_defined.test.php',
'class.properties_used.test.php',
'class.classes_undefined.test.php',
'class.classes_unused.test.php',
'class.php_modules.test.php',
'class.php_functions.test.php',
'class.xml_functions.test.php',
'class.functions_frequency.test.php',
'class.emptyfunctions.test.php',
'class.doubledeffunctions.test.php',
'class.doubledefclass.test.php',
'class.inclusions.test.php',
'class.inclusions_test.test.php',
'class.statiques.test.php',
'class.html_tags.test.php',
'class.undeffunctions.test.php',
'class.classes_nb_methods.test.php',
'class.variablesvariables.test.php',
'class.unused_properties.test.php',
'class.undefined_properties.test.php',
'class.block_of_call.test.php',
'class.arglist_call.test.php',
'class.arglist_def.test.php',
'class.arglist_disc.test.php',
'class.multi_def_files.test.php',
'class.php_classes.test.php',
'class.affectations_direct_gpc.test.php',
'class.affectations_literals.test.php',
'class.concatenation_gpc.test.php',
'class.upload_functions.test.php',
'class.variables_unaffected.test.php',
'class.dangerous_combinaisons.test.php',
'class.gpc_affectations.test.php',
// Prochain tests
);

foreach($tests as $i => $test ) {
    $fichier = $test;
    if (!file_exists($fichier)) {
        unset($tests[$i]); 
        print "Tests $test introuvable (pas de fichier $fichier) : omis\n";
        continue;
    }
    require (dirname(__FILE__)."/".$fichier);
    
    $code = file_get_contents(dirname(__FILE__)."/".$fichier);
    if (!preg_match('$class (.*?_Test) $', $code, $r)) {
        print "Impossible de trouver la classe de test dans '$fichier'\n";
        die();
    }
}
 
class Framework_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit Framework');
 
         global $tests;
         
         foreach($tests as $test) {
             $test = substr($test, 6); // @doc remove class.
             $test = substr($test, 0, -4); // @doc remove .php
             $test = str_replace('.','_', $test); // @doc remove .
             $test = ucwords($test);
             $test = str_replace('_test','_Test', $test);
             
            $suite->addTestSuite($test);
         }
 
        return $suite;
    }
}
?>