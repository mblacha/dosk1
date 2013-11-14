<?php

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_keygen/locallib.php');
require_once($CFG->libdir.'/tablelib.php');

$perpage = optional_param('perpage', 20, PARAM_INT);	
$download = optional_param('download', '', PARAM_ALPHA);

//require_once($CFG->libdir.'/adminlib.php');
require_login();

$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);
$context = context_system::instance();

//$adminroot = admin_get_root(false, false);  // settings not required - only pages

require_capability('block/int_keygen:generatecode', $context); 

if ($countnewuser = optional_param('count', '0', PARAM_INT) and confirm_sesskey()) {
	$instancekeygen = new block_int_keygen_manager();
	$prefix = optional_param('prefix', '', PARAM_TEXT);
	
	if($countnewuser){
		$i=0;
		$now=time();
		$role = $DB->get_record('role', array('shortname'=>'kursant'));
		$context = context_system::instance();
		while($i<$countnewuser){
			$key = $instancekeygen->block_int_keygen_create_key($i, $prefix);
			if(!$DB->get_record('user', array('username'=>$key))){
				$i++;
				$usernew = new StdClass;			
				$usernew->username = $key;
				$usernew->auth = 'int_keygen';
				$usernew->password = hash_internal_user_password($key);
				$usernew->mnethostid = $CFG->mnet_localhost_id; // always local user
				$usernew->confirmed  = 1;
				$usernew->timecreated = $now;
				$usernew->idnumber  = '0';
				$usernew->firstname = '';
				$usernew->lastname = '';
				$usernew->email = $key.'@intersiec.pl';		
				$usernew->city = 'Warszawa';
				$usernew->lang = 'pl'; 
				$usernew->country = 'PL'; 

				$usernewid = $DB->insert_record('user', $usernew);

				if( $usernewid && !empty($role)){
				  //przypisz role kursant
				  role_assign($role->id, $usernewid, $context->id);					 
				}
			}			
		}
		redirect(new moodle_url('/blocks/int_keygen/list.php'));				
	}    
}

$strlistcode = get_string('listcode', 'block_int_keygen'); 

$PAGE->set_context($context);
$PAGE->set_title($strlistcode );
$PAGE->set_heading($strlistcode );
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_keygen'));
$PAGE->navbar->add($strlistcode );
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_keygen/list.php', array('perpage'=>$perpage));
$PAGE->set_pagelayout('course');

$codes = $DB->get_records('user', array('auth'=>'int_keygen', 'firstaccess'=>0), '', 'id, username, timecreated');
$totalcount = count($codes);

$table = new flexible_table('mod-block-int_keygen');
$tablecolumns = array('username', 'timecreated');
$table->define_columns($tablecolumns);
$tableheaders = array(get_string('code', 'block_int_keygen'), get_string('timecreated', 'block_int_keygen'));	   					
$table->define_headers($tableheaders);
//$table->width = "100%";
$table->is_downloading($download, 'codes', 'codes');
$table->set_attribute('class', 'generalbox');
$table->column_style_all('padding', '5px 10px');
$table->column_style_all('text-align', 'left');
$table->column_style_all('vertical-align', 'middle');
//$table->no_sorting('coursename');
$table->define_baseurl($PAGE->url);
$table->sortable(true, 'username');
$table->defaultdownloadformat = 'excel';
$table->setup();

$sort = $table->get_sql_sort();
//$sort = (!empty($sort)) ? $sort : '';

if (!$table->is_downloading()) {    
	echo $OUTPUT->header();
	echo $OUTPUT->heading($strlistcode);

	$table->initialbars($totalcount > $perpage);
	$table->pagesize($perpage, $totalcount);
 
	$codes = $DB->get_records('user', array('auth'=>'int_keygen', 'firstaccess'=>0), $sort, 'id, username, timecreated', $table->get_page_start(), $table->get_page_size());
} 

foreach($codes as $code){
	$table->add_data(array($code->username, date('Y-m-d H:i:s', $code->timecreated)));
}

$table->finish_output();

if (!$table->is_downloading()) {    
	echo $OUTPUT->footer();
}   



