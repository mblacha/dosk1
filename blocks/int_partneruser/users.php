<?php

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/locallib.php');
require_once($CFG->libdir.'/tablelib.php');

require_login();

$blockid = required_param('id', PARAM_INT);// instance of block


$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);

//$context = context_system::instance();

$blockcontext = get_context_instance(CONTEXT_BLOCK, $blockid);

require_capability('moodle/block:view', $blockcontext); 

$titlesearch = get_string('users', 'block_int_partneruser');
$PAGE->set_context($blockcontext);
$PAGE->set_title($titlesearch);
$PAGE->set_heading($titlesearch);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_partneruser'));
$PAGE->navbar->add($titlesearch);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partneruser/users.php', array('id'=>$blockid));
$PAGE->set_pagelayout('course');

$table = new flexible_table('mod-block-int_parentuser');
$tablecolumns = array('lp', 'person', 'pesel', 'action');
$table->define_columns($tablecolumns);
$tableheaders = array(get_string('lp', 'block_int_partneruser'), get_string('person', 'block_int_partneruser'), get_string('pesel', 'block_int_partneruser'), '');
$table->define_headers($tableheaders);
$table->set_attribute('class', 'int_dosk int_partneruser');
$table->set_attribute('width', '100%');
//$table->no_sorting('coursename');
$table->define_baseurl($PAGE->url);
$table->sortable(false);
$table->setup();

$partnerusermanager = new block_int_partneruser_manager();
$users = $partnerusermanager->block_int_partneruser_users($USER->id);


echo $OUTPUT->header();
echo $OUTPUT->heading($titlesearch);

$i=0;
foreach($users as $user){
	$i++;
	
	$addurl = new moodle_url('/blocks/int_partneruser/profil.php', array('blockid'=>$blockid, 'id'=>$user->id));
	
	//$action = html_writer::start_tag('div', array('class' => 'buttons'));
	$action = $OUTPUT->single_button($addurl, get_string('details', 'block_int_partneruser'), 'get');
	//$action .= html_writer::end_tag('div');
	
	$table->add_data(array($i, $user->firstname.' '.$user->lastname, $user->pesel, $action));
}

$table->finish_output();

echo $OUTPUT->footer();
