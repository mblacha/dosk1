<?php

/**
 * Block edit user.
 *
 *
 * @package    block
 * @subpackage int_partneruser
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Form for search user
 */

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/locallib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/user_form.php');

require_login();

$PAGE->https_required();


$userid = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);

if(class_exists('context_course')) {
	$context  = context_course::instance($course->id);
}
else {
	$context  = get_context_instance(CONTEXT_COURSE, $course->id);
}

if($block = $DB->get_record('block_instances', array('parentcontextid'=>$context->id, 'blockname'=>'int_partneruser'))){
	$blockcontext = get_context_instance(CONTEXT_BLOCK, $block->id);
}

require_capability('moodle/block:view', $blockcontext); 


if ((!$user = $DB->get_record('user', array('id' => $userid))) || ($user->deleted)) {
	$PAGE->set_context(context_system::instance());
	echo $OUTPUT->header();
	if (!$user) {
		echo $OUTPUT->notification(get_string('invaliduser', 'error'));
	} else {
		echo $OUTPUT->notification(get_string('userdeleted'));
	}
	echo $OUTPUT->footer();
	die;
}

/// Load the Custom User Fields
profile_load_data($user);

$partneruser = ($user->idnumber == $USER->id) || is_siteadmin();
$context = $usercontext = context_user::instance($userid, MUST_EXIST);

if (!$partneruser) {
	// Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
	$struser = get_string('user');
	$PAGE->set_context(context_system::instance());
	$PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name
	$PAGE->set_heading("$SITE->shortname: $struser");
	$PAGE->set_url('/blocks/int_partneruser/users.php', array('userid'=>$user->id));
	$PAGE->navbar->add($struser);
	echo $OUTPUT->header();
	echo $OUTPUT->notification(get_string('usernotavailable', 'error'));
	echo $OUTPUT->footer();
	exit;
}


$title = get_string('profiltitle', 'block_int_partneruser');
$PAGE->set_context($blockcontext);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_partneruser'));
$PAGE->navbar->add(get_string('users', 'block_int_partneruser'), new moodle_url('/blocks/int_partneruser/users.php'));
$PAGE->navbar->add($title);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partneruser/users.php', array('id'=>$user->id));
$PAGE->set_pagelayout('course');
$PAGE->requires->js('/blocks/int_partneruser/module.js');

/*
var_dump($user);
["profile_field_pesel"]=> string(13) "78101016536h0" 
["profile_field_miasto"]=> string(11) "41-700/ruda" 
["profile_field_kod"]=> string(0) "" 
["profile_field_ulica"]=> string(4) "mowa" 
["profile_field_nrdomu"]=> string(1) "1" 
["profile_field_nrlokalu"]=> string(1) "2" 
["profile_field_telefon"]=> string(4) "3456" 
["profile_field_dataur"]=> string(10) "30-01-2014" 
		["profile_field_miejsceur"]=> string(5) "Bytom" 
		["profile_field_nrprawajazdy"]=> string(3) "qeq" 
		["profile_field_A1"]=> string(1) "1" 
		["profile_field_A"]=> string(1) "1" 
["profile_field_B1"]=> string(0) "" 
["profile_field_B"]=> string(0) "" 
["profile_field_BE"]=> string(0) "" 
["profile_field_C1"]=> string(0) "" 
["profile_field_C"]=> string(1) "1" 
["profile_field_CE"]=> string(1) "1" 
["profile_field_D1"]=> string(0) "" 
["profile_field_D"]=> string(0) "" 
["profile_field_DE"]=> string(0) "" 

["profile_field_cert_C"]=> string(1) "1" 
["profile_field_cert_C1"]=> string(0) "" 
["profile_field_cert_CE"]=> string(0) "" 
["profile_field_cert_C1E"]=> string(0) "" 
["profile_field_cert_D"]=> string(1) "1" 
["profile_field_cert_D1"]=> string(0) "" 
["profile_field_cert_DE"]=> string(0) "" 
["profile_field_cert_D1E"]=> string(0) "" 

["profile_field_katswiadectwabrak"]=> string(0) "" 
["profile_field_peselbrak"]=> string(0) "" ["profile_field_nrswiadectwa"]=> string(8) "adsadsad" ["profile_field_nrswiadectwabrak"]=> string(0) ""
		 ["profile_field_T"]=> string(0) "" }
		 */

//create form
$userform = new user_partneruser_edit_form(null, array('id'=>$user->id), 'post', '', array('id'=>'edit_user', 'class'=>'edit_user'));
$userform->set_data($user);

if ($usernew = $userform->get_data()) {

	add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');
	$authplugin = get_auth_plugin($user->auth);
	
	if(!isset($usernew->profile_field_peselbrak)){
		$usernew->profile_field_peselbrak = 0;
	}
	
	if(!isset($usernew->profile_field_nrswiadectwabrak)){
		$usernew->profile_field_nrswiadectwabrak = 0;
	}
	
	if(!isset($usernew->profile_field_katswiadectwabrak)){
		$usernew->profile_field_katswiadectwabrak = 0;
	}
	
	if(!isset($usernew->profile_field_cert_C1)){
		$usernew->profile_field_cert_C1 = 0;
	}
	
	if(!isset($usernew->profile_field_cert_C)){
		$usernew->profile_field_cert_C = 0;
	}
	
	if(!isset($usernew->profile_field_cert_CE)){
		$usernew->profile_field_cert_CE = 0;
	}
	
	if(!isset($usernew->profile_field_cert_C1E)){
		$usernew->profile_field_cert_C1E = 0;
	}
	
	if(!isset($usernew->profile_field_cert_D)){
		$usernew->profile_field_cert_D = 0;
	}
	
	if(!isset($usernew->profile_field_cert_D1)){
		$usernew->profile_field_cert_D1 = 0;
	}
	
	if(!isset($usernew->profile_field_cert_DE)){
		$usernew->profile_field_cert_DE = 0;
	}
	
	if(!isset($usernew->profile_field_cert_D1E)){
		$usernew->profile_field_cert_D1E = 0;
	}
	
	if(!isset($usernew->profile_field_A1)){
		$usernew->profile_field_A1 = 0;
	}
	
	if(!isset($usernew->profile_field_A)){
		$usernew->profile_field_A = 0;
	}
	
	if(!isset($usernew->profile_field_B1)){
		$usernew->profile_field_B1 = 0;
	}
	
	if(!isset($usernew->profile_field_B)){
		$usernew->profile_field_B = 0;
	}
	
	if(!isset($usernew->profile_field_C1)){
		$usernew->profile_field_C1 = 0;
	}
	
	if(!isset($usernew->profile_field_C)){
		$usernew->profile_field_C = 0;
	}
	
	if(!isset($usernew->profile_field_D1)){
		$usernew->profile_field_D1 = 0;
	}
	
	if(!isset($usernew->profile_field_D)){
		$usernew->profile_field_D = 0;
	}
	
	if(!isset($usernew->profile_field_BE)){
		$usernew->profile_field_BE = 0;
	}
	
	if(!isset($usernew->profile_field_CE)){
		$usernew->profile_field_CE = 0;
	}
	
	if(!isset($usernew->profile_field_DE)){
		$usernew->profile_field_DE = 0;
	}
	
	if(!isset($usernew->profile_field_T)){
		$usernew->profile_field_T = 0;
	}

	$usernew->timemodified = time();
	$DB->update_record('user', $usernew);

	// pass a true $userold here
	if (! $authplugin->user_update($user, $usernew)) {
		// auth update failed, rollback for moodle
		$DB->update_record('user', $user);
		print_error('cannotupdateprofile');
	}

	// save custom profile fields data
	profile_save_data($usernew);

	// reload from db
	$usernew = $DB->get_record('user', array('id'=>$user->id));
	events_trigger('user_updated', $usernew);

	if ($USER->id == $user->id) {
		// Override old $USER session variable if needed
		foreach ((array)$usernew as $variable => $value) {
			$USER->$variable = $value;
		}
		// preload custom fields
		profile_load_custom_fields($USER);
	}

	redirect(new moodle_url('/blocks/int_partneruser/profil.php', array('id'=>$usernew->id)));
}

// make sure we really are on the https page when https login required
$PAGE->verify_https_required();

$userfullname = fullname($user, true);

echo $OUTPUT->header();
echo $OUTPUT->heading($userfullname . ' '. $user->username);
echo html_writer::start_tag('div', array('id' => 'page-blocks-int_partneruser-user'));
$userform->display();
echo html_writer::end_tag('div');
echo $OUTPUT->footer();
