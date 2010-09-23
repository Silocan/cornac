<?php 
include_once('Auditeur_Framework_TestCase.php');

class mssql_functions_Test extends Auditeur_Framework_TestCase
{
    public function testmssql_functions()  {
        $this->expected = array( 
'mssql_bind',
'mssql_close',
'mssql_connect',
'mssql_data_seek',
'mssql_execute',
'mssql_fetch_array',
'mssql_fetch_assoc',
'mssql_fetch_batch',
'mssql_fetch_field',
'mssql_fetch_object',
'mssql_fetch_row',
'mssql_field_length',
'mssql_field_name',
'mssql_field_seek',
'mssql_field_type',
'mssql_free_result',
'mssql_free_statement',
'mssql_get_last_message',
'mssql_guid_string',
'mssql_init',
'mssql_min_error_severity',
'mssql_min_message_severity',
'mssql_next_result',
'mssql_num_fields',
'mssql_num_rows',
'mssql_pconnect',
'mssql_query',
'mssql_result',
'mssql_rows_affected',
'mssql_select_db',);
        $this->unexpeted = array(/*'',*/);

        parent::generic_test();
    }
}
?>
