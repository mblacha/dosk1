<?php


/**
 * Enrol user into course.
 *
 *
 * @package    block
 * @subpackage int_partnercourse
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Form for search user
 */

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partnercourse/locallib.php');
require_once($CFG->dirroot . '/blocks/int_partnercourse/enrol_form.php');
require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');

require_login();

$blockid = required_param('blockid', PARAM_INT);// instance of block
$usecache = optional_param('usecache', true, PARAM_BOOL); // use cached list of courses

$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);
$blockcontext = get_context_instance(CONTEXT_BLOCK, $blockid);

require_capability('moodle/block:view', $blockcontext);

$service = mnetservice_enrol::get_instance();

if (!$service->is_available()) {
	echo $OUTPUT->box(get_string('mnetdisabled','mnet'), 'noticebox');
	echo $OUTPUT->footer();
	die();
}

// remote hosts that may publish remote enrolment service and we are subscribed to it
$hosts = $service->get_remote_publishers();

if (!$usecache) {
	// our local database will be changed
	require_sesskey();
}


$title = get_string('enroltitle', 'block_int_partnercourse');
$PAGE->set_context($blockcontext);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_partnercourse'));
//$PAGE->navbar->add(get_string('users', 'block_int_partnercourse'), new moodle_url('/blocks/int_partnercourse/users.php', array('id'=>$blockid)));
$PAGE->navbar->add($title);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partnercourse/users.php', array('id'=>$blockid));
$PAGE->set_pagelayout('course');
$PAGE->requires->js('/blocks/int_partnercourse/module.js');

$customdata = array('blockid'=>$blockid, 'usecache'=>$usecache);
//create form
$enrolform = new enrol_partnercourse_user_form(null, $customdata, 'post', '', array('id'=>'enrol_partnercourse', 'class'=>'enrol_partnercourse_user'));
//$enrolform->set_data($user);

if ($enrolnew = $enrolform->get_data()) {

	$hostcourse = explode(',', $enrolnew->course);
	$hostid = $hostcourse[0];
	$courseid = $hostcourse[1];
	
	$enrolfrom = explode('-', $enrolnew->enrolfrom);
	$timestart = date(mktime(0,0,0,$enrolfrom[1],$enrolfrom[0],$enrolfrom[2]));
	
	$enrolto = explode('-', $enrolnew->enrolto);
	$timeend = date(mktime(23,59,59,$enrolto[1],$enrolto[0],$enrolto[2]));
	
	if (empty($hosts[$hostid])) {
		print_error('wearenotsubscribedtothishost', 'mnetservice_enrol');
	}
	$host   = $hosts[$hostid];
	$course = $DB->get_record('mnetservice_enrol_courses', array('remoteid'=>$courseid, 'hostid'=>$host->id), '*', MUST_EXIST);	
	
	$error = '';	
	
	$lastfetchenrolments = get_config('mnetservice_enrol', 'lastfetchenrolments');
	if (!$usecache or empty($lastfetchenrolments) or (time()-$lastfetchenrolments > 600)) {
		// fetch fresh data from remote if we just came from the course selection screen
		// or every 10 minutes
		$usecache = false;
		$result = $service->req_course_enrolments($hostcourse[0], $hostcourse[1], $usecache);
		if ($result !== true) {
			$error .= $service->format_error_message($result);
		}
	}
	
	// user selectors
	$currentuserselector = new mnetservice_enrol_existing_users_selector('removeselect', array('hostid'=>$host->id, 'remotecourseid'=>$course->remoteid));
	$potentialuserselector = new mnetservice_enrol_potential_users_selector('addselect', array('hostid'=>$host->id, 'remotecourseid'=>$course->remoteid));
	
	// process incoming enrol request
	if (!empty($enrolnew->user)) {
		
		$user = $DB->get_record('user', array('id'=>$enrolnew->user));	
		$user->timestart = $timestart; 	
		$user->timeend = $timeend;
		
		$result = $service->req_enrol_user($user, $course);
		if ($result !== true) {
			$error .= $service->format_error_message($result);
		}
		//create order
		$partnercoursemanager = new block_int_partnercourse_manager();
		$partnercoursemanager->create_order($user, $course);			
	}
	
	if (!empty($error)) {
		echo $OUTPUT->box($error, 'generalbox error');
	}
	
	redirect(new moodle_url('/blocks/int_partnercourse/courses.php?blockid', array('blockid'=>$blockid)));
}


echo $OUTPUT->header();
//echo $OUTPUT->heading($userfullname);
$enrolform->display();

echo $OUTPUT->footer();

