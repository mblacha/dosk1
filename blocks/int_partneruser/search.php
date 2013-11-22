<?php

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/locallib.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/forms.php');
require_once($CFG->dirroot . '/mnet/service/dosk/locallib.php');
require_once($CFG->libdir.'/tablelib.php');

require_login();

$usecache = optional_param('usecache', true, PARAM_BOOL); // use cached list of courses
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

$PAGE->set_context($blockcontext);

$service = mnetservice_dosk::get_instance();

if (!$service->is_available()) {
	echo $OUTPUT->box(get_string('mnetdisabled','mnet'), 'noticebox');
	die();
}

if ($add = optional_param('add', '0', PARAM_INT) and confirm_sesskey()) {
	if($user = $DB->get_record('user', array('id'=>$add))){			
		
		// remote hosts that may publish remote enrolment service and we are subscribed to it
		$hosts = $service->get_remote_publishers();
		
		if (!$usecache) {
			// our local database will be changed
			require_sesskey();
		}			
		
		$error = '';
		foreach ($hosts as $host){
			$result = $service->req_partner_assign($user, $host->id);
			if ($result !== true) {
				$error .= $service->format_error_message($result);
			}
		}	
		
		
		if (!empty($error)) {
			echo $OUTPUT->box($error, 'generalbox error');
			die();
		}
		
		redirect(new moodle_url('/blocks/int_partneruser/users.php'));
	}	
}

$titlesearch = get_string('search', 'block_int_partneruser');

$PAGE->set_title($titlesearch);
$PAGE->set_heading($titlesearch);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_partneruser'));
$PAGE->navbar->add($titlesearch);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partneruser/search.php');
$PAGE->set_pagelayout('course');

$partnerusermanager = new block_int_partneruser_manager();
$users = array();

$searchform = new int_partneruser_search_form();
//$searchform->set_data($searchform);
if($searchdata = $searchform->get_data()){	
	$users = $partnerusermanager->block_int_partneruser_find_user($searchdata);
}

echo $OUTPUT->header();
echo html_writer::start_tag('div', array('id' => 'page-blocks-int_partneruser-users'));

if(empty($users)){
} else {	
	echo $OUTPUT->heading(get_string('searchadduser', 'block_int_partneruser'));
}

$table = new flexible_table('mod-block-int_parentuser');
$tablecolumns = array('firstname', 'lastname', 'pesel', 'action');
$table->define_columns($tablecolumns);
$tableheaders = array(get_string('firstname'), get_string('lastname'), get_string('pesel', 'block_int_partneruser'), '');
$table->define_headers($tableheaders);
$table->set_attribute('class', 'int_dosk int_partneruser');
$table->set_attribute('width', '100%');
$table->define_baseurl($PAGE->url);
$table->sortable(false);
$table->setup();

foreach($users as $user){	
	$addurl = new moodle_url('/blocks/int_partneruser/search.php', array('add'=>$user->id, 'sesskey'=>sesskey()));
	
	$action = html_writer::start_tag('div', array('class' => 'buttons'));
	$action .= $OUTPUT->single_button($addurl, get_string('searchadduser', 'block_int_partneruser'), 'post');
	$action .= html_writer::end_tag('div');
	
	$table->add_data(array($user->firstname, $user->lastname, $user->pesel, $action));
}

if(empty($users)){	
	echo '<div class="add_user_form"><div class="naglowek">';	
	echo $OUTPUT->heading($titlesearch);
	echo '</div>';
	$searchform->display();
	echo '</div>';
}

if($searchdata){
	$table->finish_output();
}

echo html_writer::end_tag('div');
echo $OUTPUT->footer();
