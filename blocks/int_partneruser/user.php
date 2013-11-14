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
$PAGE->set_url('/blocks/int_partneruser/users.php', array('blockid'=>$blockid, 'id'=>$user->id));
$PAGE->set_pagelayout('course');


//create form
$userform = new user_partneruser_edit_form(null, array('id'=>$user->id, 'blockid'=>$blockid));
$userform->set_data($user);

if ($usernew = $userform->get_data()) {

	add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');
	$authplugin = get_auth_plugin($user->auth);

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

	redirect(new moodle_url('/blocks/int_partneruser/profil.php', array('blockid'=>$blockid, 'id'=>$usernew->id)));
}

// make sure we really are on the https page when https login required
$PAGE->verify_https_required();

$userfullname = fullname($user, true);

echo $OUTPUT->header();
echo $OUTPUT->heading($userfullname);
$userform->display();
echo $OUTPUT->footer();
