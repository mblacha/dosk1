<?php

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/locallib.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/forms.php');
require_once($CFG->libdir.'/tablelib.php');

require_login();

$blockid = required_param('id', PARAM_INT);// instance of block

$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);
$context = context_system::instance();

$blockcontext = get_context_instance(CONTEXT_BLOCK, $blockid);

require_capability('moodle/block:view', $blockcontext); 

if ($add = optional_param('add', '0', PARAM_INT) and confirm_sesskey()) {
	if($user = $DB->get_record('user', array('id'=>$add))){					
		
		/*
		$data = new stdClass();			
		$data->userid  = $add;
		$data->fieldid = $userfieldpartner->id;
		$data->data    = $USER->id;
		
		if ($dataid = $DB->get_field('user_info_data', 'id', array('userid'=>$data->userid, 'fieldid'=>$data->fieldid))) {
			$data->id = $dataid;
			$DB->update_record('user_info_data', $data);
		} else {
			$DB->insert_record('user_info_data', $data);
		}
		*/
		$user->idnumber = $USER->id;
		$DB->update_record('user', $user);					
		
		redirect(new moodle_url('/blocks/int_partneruser/users.php', array('id'=>$blockid)));
	}	
}

$titlesearch = get_string('search', 'block_int_partneruser');
$PAGE->set_context($blockcontext);
$PAGE->set_title($titlesearch);
$PAGE->set_heading($titlesearch);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_partneruser'));
$PAGE->navbar->add($titlesearch);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partneruser/search.php', array('id'=>$blockid));
$PAGE->set_pagelayout('course');

$table = new flexible_table('mod-block-int_parentuser');
$tablecolumns = array('firstname', 'lastname', 'pesel', 'action');
$table->define_columns($tablecolumns);
$tableheaders = array(get_string('firstname'), get_string('lastname'), get_string('pesel', 'block_int_partneruser'), '');
$table->define_headers($tableheaders);
$table->width = "100%";
//$table->is_downloading($download, 'codes', 'codes');
$table->set_attribute('class', 'generalbox');
$table->column_style_all('padding', '5px 10px');
$table->column_style_all('text-align', 'left');
$table->column_style_all('vertical-align', 'middle');
//$table->no_sorting('coursename');
$table->define_baseurl($PAGE->url);
$table->sortable(false);
$table->setup();

$partnerusermanager = new block_int_partneruser_manager();
$users = array();

$searchform = new int_partneruser_search_form('', array('id'=>$blockid));
//$searchform->set_data($searchform);
if($searchdata = $searchform->get_data()){	
	$users = $partnerusermanager->block_int_partneruser_find_user($searchdata);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($titlesearch);

foreach($users as $user){	
	$addurl = new moodle_url('/blocks/int_partneruser/search.php', array('id'=>$blockid, 'add'=>$user->id, 'sesskey'=>sesskey()));
	
	$action = html_writer::start_tag('div', array('class' => 'buttons'));
	$action .= $OUTPUT->single_button($addurl, get_string('searchadduser', 'block_int_partneruser'), 'post');
	$action .= html_writer::end_tag('div');
	
	$table->add_data(array($user->firstname, $user->lastname, $user->pesel, $action));
}

if(empty($users)){
	$searchform->display();
}
if($searchdata){
	$table->finish_output();
}


echo $OUTPUT->footer();
