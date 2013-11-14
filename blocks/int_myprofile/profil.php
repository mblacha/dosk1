<?php

require('../../config.php');
require_once($CFG->dirroot . '/user/profile/lib.php');

require_login();

$blockid = required_param('blockid', PARAM_INT);// instance of block
$userid = $USER->id;

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

$title = get_string('profiltitle', 'block_int_myprofile');
$PAGE->set_context($blockcontext);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_int_myprofile'));
$PAGE->navbar->add($title);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/int_myprofile/profil.php', array('id'=>$blockid));
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

echo html_writer::tag('dt', get_string('pesel', 'block_int_myprofile'));
echo html_writer::tag('dd', $user->profile_field_pesel);
echo html_writer::end_tag('dl');

echo '</div></div></div>';


echo $OUTPUT->footer();
