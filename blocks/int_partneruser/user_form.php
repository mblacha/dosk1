<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class user_partneruser_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE, $USER, $DB;

        $mform =& $this->_form;
        $userid = $USER->id;
        $blockid = 0;

        if (is_array($this->_customdata)) {           
            if (array_key_exists('id', $this->_customdata)) {
                $userid = $this->_customdata['id'];
            }
            if (array_key_exists('blockid', $this->_customdata)) {
            	$blockid = $this->_customdata['blockid'];            
            }
        }
        //Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');
                
        $user = $DB->get_record('user', array('id' => $userid));
        $authplugin = get_auth_plugin($user->auth);
        //useredit_load_preferences($user, false);
        
        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'blockid', $blockid);
        $mform->setType('blockid', PARAM_INT);
       
        /// Print the required moodle fields first
        //$mform->addElement('header', 'moodle', '');

        /// shared fields
        //useredit_shared_definition($mform, $editoroptions, $filemanageroptions);
        $nameordercheck = new stdClass();
        $nameordercheck->firstname = 'a';
        $nameordercheck->lastname  = 'b';
        if (fullname($nameordercheck) == 'b a' ) {  // See MDL-4325
        	$mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
        	$mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        } else {
        	$mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        	$mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
        }
        
        $mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);
        
        $mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);

        /// Next the customisable profile fields
        $authplugin->profile_definition($mform, $userid);

        $this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');

        if ($user = $DB->get_record('user', array('id'=>$userid))) {
            /// Next the customisable profile fields
            profile_definition_after_data($mform, $user->id);
        } else {
            profile_definition_after_data($mform, 0);
        }
    }

    function validation($usernew, $files) {
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;
        $user    = $DB->get_record('user', array('id'=>$usernew->id));
               
        if(!$this->check_pesel($usernew->profile_field_pesel)) {
        	$errors['profile_field_pesel'] = get_string('errorpesel', 'auth_int_keygen');
        }
        
        /// Next the customisable profile fields
        $errors += profile_validation($usernew, $files);
   
        return $errors;
    }
    
    function check_pesel($str) {
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


