<?php
include_once('Auditeur_Framework_TestCase.php');

class xml_functions_Test extends Auditeur_Framework_TestCase
{
    public function testVariables()  { 
        $this->name = 'xml_functions';
        $this->attendus = array('xml_parser_create','xml_parser_create_ns','xml_set_object','xml_set_element_handler','xml_set_character_data_handler','xml_set_processing_instruction_handler','xml_set_default_handler','xml_set_unparsed_entity_decl_handler','xml_set_notation_decl_handler','xml_set_external_entity_ref_handler','xml_set_start_namespace_decl_handler','xml_set_end_namespace_decl_handler','xml_parse','xml_parse_into_struct','xml_get_error_code','xml_error_string','xml_get_current_line_number','xml_get_current_column_number','xml_get_current_byte_index','xml_parser_free','xml_parser_set_option','xml_parser_get_option','utf8_encode','utf8_decode');
        $this->inattendus = array();
        
        parent::generic_test();
    }
}

?>

