<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class enrol_partnercourse_user_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE, $USER, $DB;

        $mform =& $this->_form;        
        $renderer =& $mform->defaultRenderer();
  
        $blockid = 0;

        if (is_array($this->_customdata)) {            
            if (array_key_exists('blockid', $this->_customdata)) {
            	$blockid = $this->_customdata['blockid'];            
            }
            if (array_key_exists('usecache', $this->_customdata)) {
            	$usecache = $this->_customdata['usecache'];
            }
        }
        $partnercoursemanager = new block_int_partnercourse_manager(); 
        
        //Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required'); 
        
        /// Add some extra hidden fields 
        $mform->addElement('hidden', 'blockid', $blockid);
        $mform->setType('blockid', PARAM_INT);
        
        $mform->addElement('hidden', 'usecache', $usecache);
        $mform->setType('usecache', PARAM_INT);
       
        
		//first
		$mform->addElement('header', 'first_enrol_partnercourse_user');
		$mform->addElement('html', '<div class="title">'.get_string('formtitle1', 'block_int_partnercourse').'</div>');		
		
		
		
		$users[0] = get_string('choosedots');
        $users =  $partnercoursemanager->block_int_partnercourse_users_array($users, $USER->id);        
        $mform->addElement('select', 'user', get_string('formselectuser', 'block_int_partnercourse'), $users, array('class'=>'myselect'));
        $mform->setDefault('user', '0');
        
        $courses[0] = get_string('choosedots');
        $courses =  $partnercoursemanager->block_int_partnercourse_courses_array($courses, $usecache);
        $element = $mform->addElement('select', 'course', get_string('formselectcourse', 'block_int_partnercourse'), $courses);
        $mform->setDefault('course', '0');
        
        //second
		$mform->addElement('header', 'second_enrol_partnercourse_user');
		$mform->addElement('html', '<div class="title">'.get_string('formtitle2', 'block_int_partnercourse').'</div>');
		
		//datatime
		$mform->addElement('html', '<div id="errordata"></div>');		
		$mform->addElement('html', '<div class="enroldata">');	
				
		$mform->addElement('text', 'enrolfrom', get_string('formdatafrom', 'block_int_partnercourse'), array('class'=>'calendar', 'size'=>9));
		$mform->setType('enrolfrom', PARAM_TEXT);
		
		$mform->addElement('text', 'enrolto', get_string('formdatato', 'block_int_partnercourse'), array('class'=>'calendar', 'size'=>9));
		$mform->setType('enrolto', PARAM_TEXT);

		$mform->addElement('html', '</div>');		
	       
        $renderer->setHeaderTemplate('<div>');
        $this->add_action_buttons(false, get_string('formsave', 'block_int_partnercourse'));              
    }
    
    function validation($enrolnew, $files) {
    	global $CFG, $DB;
    	
    	$errors['errordata'] = get_string('formerrordata', 'block_int_partnercourse');
    	$errors['enrolfrom'] = get_string('formerrordata', 'block_int_partnercourse');
    
    	$errors = parent::validation($enrolnew, $files);    
    	$enrolnew = (object)$enrolnew;
    	
    	$from = explode('-', $enrolnew->enrolfrom);    	
    	$from = date(mktime(0,0,0,$from[1],$from[0],$from[2]));
    	
    	$to = explode('-', $enrolnew->enrolto);
    	$to = date(mktime(0,0,0,$to[1],$to[0],$to[2]));

    	if($from>$to){
    		//$errors['errordata'] = get_string('formerrordata', 'block_int_partnercourse');
    	}
    
    	return $errors;
    }
    
}


