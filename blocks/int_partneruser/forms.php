<?php

/**
 * Block displaying information about user.
 *
 *
 * @package    block
 * @subpackage int_partneruser
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Form for search user
 */

require_once($CFG->libdir . '/formslib.php');

class int_partneruser_search_form extends moodleform {

    public function definition() {
        global $CFG, $USER, $OUTPUT;
        $strrequired = get_string('required');
        $mform = & $this->_form;
        
        //$mform->addElement('header', 'site', get_string('search', 'block_int_partneruser'));

        //add the course id (of the context)
        //$mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        //$mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_INT);

      
		$mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30" 
		        placeholder="'.get_string('searchfirstname', 'block_int_partneruser').'"');
		$mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30" 
		        placeholder="'.get_string('searchlastname', 'block_int_partneruser').'"');       
		$mform->addElement('text', 'pesel',  get_string('pesel', 'block_int_partneruser'),  'maxlength="100" size="30" 
		        placeholder="'.get_string('searchpesel', 'block_int_partneruser').'"');		
        
        $mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);
        
        $mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);
        
        $mform->addRule('pesel', $strrequired, 'required', null, 'client');
        $mform->setType('pesel', PARAM_NOTAGS);
        
        $mform->addElement('submit', 'submitbutton', get_string('search', 'block_int_partneruser'));        
        
    }
    
    function validation($usernew, $files) {
    	global $CFG, $DB;
    	    
    	$errors = parent::validation($usernew, $files);    
    	$usernew = (object)$usernew;    	
    	 
    	if(!$this->check_pesel($usernew->pesel)) {
    		$errors['pesel'] = get_string('errorpesel', 'block_int_partneruser');
    	}
    	 
    	return $errors;
    }
    
    function check_pesel($str) {
    	if(strlen($str)==0){
    		return true;
    	}
    	if (!preg_match('/^[0-9]{11}$/',$str)) {
    		return false;
    	}
    
    	$arrSteps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
    	$intSum = 0;
    	for ($i = 0; $i < 10; $i++)	{
    		$intSum += $arrSteps[$i] * $str[$i];
    	}
    	$int = 10 - $intSum % 10;
    	$intControlNr = ($int == 10)?0:$int;
    	if ($intControlNr == $str[10]) {
    		return true;
    	}
    	return false;
    }

}
