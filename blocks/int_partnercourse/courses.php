<?php

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partnercourse/locallib.php');
require_once($CFG->libdir.'/tablelib.php');

require_login();

$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);
if(class_exists('context_course')) {
	$context  = context_course::instance($course->id);
}
else {
	$context  = get_context_instance(CONTEXT_COURSE, $course->id);
}

if($block = $DB->get_record('block_instances', array('parentcontextid'=>$context->id, 'blockname'=>'int_partnercourse'))){
	$blockcontext = get_context_instance(CONTEXT_BLOCK, $block->id);
}

require_capability('moodle/block:view', $blockcontext); 

$titlesearch = get_string('listcourses', 'block_int_partnercourse');
$PAGE->set_context($blockcontext);
$PAGE->set_title($titlesearch);
$PAGE->set_heading($titlesearch);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_partnercourse'));
$PAGE->navbar->add($titlesearch);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partnercourse/courses.php');
$PAGE->set_pagelayout('course');

$table = new flexible_table('block-int_partnercourse');
$tablecolumns = array('lp', 'person', 'pesel', 'course', 'action');
$table->define_columns($tablecolumns);
$tableheaders = array(get_string('lp', 'block_int_partnercourse'), get_string('person', 'block_int_partnercourse'), 
		get_string('pesel', 'block_int_partnercourse'), get_string('course', 'block_int_partnercourse'), '');
$table->define_headers($tableheaders);
//$table->is_downloading($download, 'codes', 'codes');
$table->set_attribute('class', 'int_dosk int_partnercourse');
$table->set_attribute('width', '100%');
//$table->column_style_all('padding', '5px 10px');
//$table->column_style_all('text-align', 'left');
//$table->column_style_all('vertical-align', 'middle');
//$table->no_sorting('coursename');
$table->column_class(1, 'test');
$table->define_baseurl($PAGE->url);
$table->sortable(false);
$table->setup();

$partnercoursemanager = new block_int_partnercourse_manager();
$courses = $partnercoursemanager->block_int_partnercourse_courses($USER->id);

$fieldid = $DB->sql_concat('hostid', "'-'" , 'remotecourseid');
$partnerenrols = $DB->get_records('mnetservice_enrol_enrolments', 
		array('userid'=>$USER->id), '', $fieldid.' as id, timestart');


echo $OUTPUT->header();
echo $OUTPUT->heading($titlesearch);

$i=0;
foreach($courses as $course){
	$i++;
	
	if(!isset($partnerenrols[$course->hostid.'-'.$course->remoteid])){
		$partnercoursemanager->enrol_partner($course);
	}	
	
	$detailsurl = new moodle_url('/auth/mnet/jump.php', 
			array('hostid'=>$course->hostid, 'wantsurl'=>'/blocks/int_partnercoursenet/course.php?username='.$course->username.
					'&amp;user='.$USER->id.'&amp;course='.$course->remoteid));
	
	//$action = html_writer::start_tag('div', array('class' => 'buttons'));
	$action = $OUTPUT->single_button($detailsurl, get_string('details', 'block_int_partnercourse'), 'get');
	//$action .= html_writer::end_tag('div');
	
	$table->add_data(array($i, $course->firstname.' '.$course->lastname, $course->pesel, $course->fullname, $action));
}

$table->finish_output();

echo $OUTPUT->footer();
