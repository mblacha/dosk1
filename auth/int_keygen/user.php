<?php

/**
 * Allows you to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once('../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/auth/int_keygen/user_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

//HTTPS is required in this page when $CFG->loginhttps enabled
$PAGE->https_required();

$userid = optional_param('id', $USER->id, PARAM_INT);    // user id
$course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
$cancelemailchange = optional_param('cancelemailchange', 0, PARAM_INT);   // course id (defaults to Site)

$PAGE->set_url('/auth/int_keygen/user.php', array('course'=>$course, 'id'=>$userid));

if (!$course = $DB->get_record('course', array('id'=>$course))) {
    print_error('invalidcourseid');
}

if ($course->id != SITEID) {
    require_login($course);
} else if (!isloggedin()) {
    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $CFG->httpswwwroot.'/user/edit.php';
    }
    $authplugin = get_auth_plugin('int_keygen');
    redirect($userauth->get_login_url());
} else {
    $PAGE->set_context(get_system_context());
    $PAGE->set_pagelayout('standard');
}

// Guest can not edit
if (isguestuser()) {
    print_error('guestnoeditprofile');
}

// The user profile we are editing
if (!$user = $DB->get_record('user', array('id'=>$userid))) {
    print_error('invaliduserid');
}

// Guest can not be edited
if (isguestuser($user)) {
    print_error('guestnoeditprofile');
}

// User interests separated by commas
if (!empty($CFG->usetags)) {
    require_once($CFG->dirroot.'/tag/lib.php');
    $user->interests = tag_get_tags_array('user', $user->id);
}

// remote users cannot be edited
if (is_mnet_remote_user($user)) {
    if (user_not_fully_set_up($user)) {
        $hostwwwroot = $DB->get_field('mnet_host', 'wwwroot', array('id'=>$user->mnethostid));
        print_error('usernotfullysetup', 'mnet', '', $hostwwwroot);
    }
    redirect($CFG->wwwroot . "/user/view.php?course={$course->id}");
}

// load the appropriate auth plugin
$userauth = get_auth_plugin($user->auth);

if (!$userauth->can_edit_profile()) {
    print_error('noprofileedit', 'auth');
}

if ($course->id == SITEID) {
    $coursecontext = context_system::instance();   // SYSTEM context
} else {
    $coursecontext = context_course::instance($course->id);   // Course context
}
$systemcontext   = context_system::instance();
$personalcontext = context_user::instance($user->id);

// check access control
if ($user->id == $USER->id) {
    //editing own profile - require_login() MUST NOT be used here, it would result in infinite loop!
    if (!has_capability('moodle/user:editownprofile', $systemcontext)) {
        print_error('cannotedityourprofile');
    }

} else {
    // teachers, parents, etc.
    require_capability('moodle/user:editprofile', $personalcontext);
    // no editing of guest user account
    if (isguestuser($user->id)) {
        print_error('guestnoeditprofileother');
    }
    // no editing of primary admin!
    if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins
        print_error('useradmineditadmin');
    }
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

// Process email change cancellation
if ($cancelemailchange) {
    cancel_email_update($user->id);
}

//load user preferences
useredit_load_preferences($user);

//Load custom profile fields data
profile_load_data($user);

//create form
$userform = new user_keygen_edit_form(null, array('userid' => $user->id));
if (empty($user->country)) {
    // MDL-16308 - we must unset the value here so $CFG->country can be used as default one
    unset($user->country);
}

if(!$user->timemodified){
	$user->firstname = '';
	$user->lastname = '';
	$user->password = '';
}
$userform->set_data($user);

if ($usernew = $userform->get_data()) {

    add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');
    $authplugin = get_auth_plugin($user->auth);

    $usernew->timemodified = time();
    $usernew->msn = $usernew->password;
    $usernew->password = hash_internal_user_password($usernew->password);
    $DB->update_record('user', $usernew);

    // pass a true $userold here
    if (! $authplugin->user_update($user, $usernew)) {
        // auth update failed, rollback for moodle
        $DB->update_record('user', $user);
        print_error('cannotupdateprofile');
    }

    //update preferences
    useredit_update_user_preference($usernew);

    // update mail bounces
    useredit_update_bounces($user, $usernew);

    /// update forum track preference
    useredit_update_trackforums($user, $usernew);

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

    if (is_siteadmin() and empty($SITE->shortname)) {
        // fresh cli install - we need to finish site settings
        redirect(new moodle_url('/admin/index.php'));
    } else {
    	redirect(new moodle_url('/auth/int_keygen/jump.php')); 
    }
}

// make sure we really are on the https page when https login required
$PAGE->verify_https_required();


/// Display page header
$streditmyprofile = get_string('editmyprofile');
$strparticipants  = get_string('participants');
$userfullname     = fullname($user, true);

$PAGE->set_title("$course->shortname: $streditmyprofile");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($userfullname);
$userform->display();
/// and proper footer
echo $OUTPUT->footer();

