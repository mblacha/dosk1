<?php

require('../../config.php');
require_once($CFG->dirroot . '/blocks/int_partneruser/locallib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');

require_login();

$blockid = required_param('blockid', PARAM_INT);// instance of block
$userid = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);
$blockcontext = get_context_instance(CONTEXT_BLOCK, $blockid);

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

$partneruser = ($user->idnumber == $USER->id);
$context = $usercontext = context_user::instance($userid, MUST_EXIST);

if (!$partneruser) {
	// Course managers can be browsed at site level. If not forceloginforprofiles, allow access (bug #4366)
	$struser = get_string('user');
	$PAGE->set_context(context_system::instance());
	$PAGE->set_title("$SITE->shortname: $struser");  // Do not leak the name
	$PAGE->set_heading("$SITE->shortname: $struser");
	$PAGE->set_url('/blocks/int_partneruser/users.php', array('id'=>$blockid, 'userid'=>$user->id));
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
$PAGE->navbar->add(get_string('users', 'block_int_partneruser'), new moodle_url('/blocks/int_partneruser/users.php', array('id'=>$blockid)));
$PAGE->navbar->add($title);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_partneruser/users.php', array('id'=>$blockid, 'userid'=>$user->id));
$PAGE->set_pagelayout('course');

echo $OUTPUT->header();

echo '<div class="userprofile">';

echo $OUTPUT->heading($title);

echo '<div class="userprofilebox clearfix">';
echo '<div class="descriptionbox">';

echo html_writer::start_tag('dl', array('class'=>'list'));
echo html_writer::tag('dt', get_string('firstname'));
echo html_writer::tag('dd', $user->firstname);

echo html_writer::tag('dt', get_string('lastname'));
echo html_writer::tag('dd', $user->lastname);

echo html_writer::tag('dt', get_string('pesel', 'block_int_partneruser'));
echo html_writer::tag('dd', $user->profile_field_pesel);
echo html_writer::end_tag('dl');

$editurl = new moodle_url('/blocks/int_partneruser/user.php', array('blockid'=>$blockid, 'id'=>$user->id));
echo html_writer::start_tag('div', array('class' => 'buttons'));
echo $OUTPUT->single_button($editurl, get_string('edit'), 'get');
echo  html_writer::end_tag('div');

echo '</div></div></div>';


echo $OUTPUT->footer();
