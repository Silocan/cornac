<?php
include_once('Auditeur_Framework_TestCase.php');

class ldap_functions_Test extends Auditeur_Framework_TestCase
{
    public function testldap_functions()  { 
        $this->expected = array( 
'ldap_8859_to_t61',
'ldap_add',
'ldap_bind',
'ldap_close',
'ldap_compare',
'ldap_connect',
'ldap_count_entries',
'ldap_delete',
'ldap_dn2ufn',
'ldap_err2str',
'ldap_errno',
'ldap_error',
'ldap_explode_dn',
'ldap_first_attribute',
'ldap_first_entry',
'ldap_first_reference',
'ldap_free_result',
'ldap_get_attributes',
'ldap_get_dn',
'ldap_get_entries',
'ldap_get_option',
'ldap_get_values_len',
'ldap_get_values',
'ldap_list',
'ldap_mod_add',
'ldap_mod_del',
'ldap_mod_replace',
'ldap_modify',
'ldap_next_attribute',
'ldap_next_entry',
'ldap_next_reference',
'ldap_parse_reference',
'ldap_parse_result',
'ldap_read',
'ldap_rename',
'ldap_sasl_bind',
'ldap_search',
'ldap_set_option',
'ldap_set_rebind_proc',
'ldap_sort',
'ldap_start_tls',
'ldap_t61_to_8859',
'ldap_unbind',

        );
        $this->inexpected = array(/*'',*/);
        
        parent::generic_test();
//        parent::generic_counted_test();
    }
}
?>