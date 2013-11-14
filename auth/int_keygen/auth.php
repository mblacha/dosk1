<?php

/**
 * Authentication Plugin: Keygen Authentication
 * Just does a simple check against the moodle database.
 *
 * @package    auth_int_keygen
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * int_keygen authentication plugin.
 *
 * @package    auth
 * @subpackage int_keygen
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_int_keygen extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_int_keygen() {
        $this->authtype = 'int_keygen';
        $this->config = get_config('auth/int_keygen');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist. (Non-mnet accounts only!)
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        global $CFG, $DB, $USER;
		
        if (!$user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            return false;
        }
      		
        if (!validate_internal_user_password($user, $password)) {
            return false;
        }
        
       
        if ($password === 'changeme') {
            // force the change - this is deprecated and it makes sense only for int_keygen auth,
            // because most other plugins can not change password easily or
            // passwords are always specified by users
            set_user_preference('auth_forcepasswordchange', true, $user->id);
        }
         
        return true;
    }

    /**
     * Updates the user's password.
     *
     * Called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     */
    function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }

    function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return false;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return false;
    }
    /**
     * Returns true if this authentication plugin can edit the users'
     * profile.
     *
     * @return bool
     */
    function can_edit_profile() {
    	return true;
    }
    
    /**
     * Returns the URL for editing the users' profile, or empty if the default
     * URL can be used.
     *
     * This method is used if can_edit_profile() returns true.
     * This method is called only when user is logged in, it may use global $USER.
     *
     * @return moodle_url url of the profile page or null if standard used
     */
    function edit_profile_url() {
    	global $CFG;
    	return new moodle_url( $CFG->wwwroot.'/auth/int_keygen/user.php');
    }
    

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $config An object containing all the data for this page.
     * @param string $error
     * @param array $user_fields
     * @return void
     */
    function config_form($config, $err, $user_fields) {
        include 'config.html';
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param array $config
     * @return void
     */
    function process_config($config) {
        return true;
    }

   /**
    * Confirm the new user as registered. This should normally not be used,
    * but it may be necessary if the user auth_method is changed to int_keygen
    * before the user is confirmed.
    *
    * @param string $username
    * @param string $confirmsecret
    */
    function user_confirm($username, $confirmsecret = null) {
        global $DB;

        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;
            } else {
                $DB->set_field("user", "confirmed", 1, array("id"=>$user->id));
                if ($user->firstaccess == 0) {
                    $DB->set_field("user", "firstaccess", time(), array("id"=>$user->id));
                }
                return AUTH_CONFIRM_OK;
            }
        } else  {
            return AUTH_CONFIRM_ERROR;
        }
    }
    
    function get_login_url() {
    	global $CFG;
    
    	$url = "$CFG->wwwroot/auth/int_keygen/index.php";
    
    	if (!empty($CFG->loginhttps)) {
    		$url = str_replace('http:', 'https:', $url);
    	}
    
    	return $url;
    }
    
    
    /**
     * Determines if a user has completed setting up their account.
     *
     * @param user $user A {@link $USER} object to test for the existence of a valid name and email
     * @return bool
     */
    function user_not_fully_set_up($user) {
    	if (isguestuser($user)) {
    		return false;
    	}
    	return (empty($user->timemodified) or empty($user->firstname) or empty($user->lastname) 
    	        or empty($user->email) or over_bounce_threshold($user));
    }
    
    /**
     * Print out the customisable categories and fields for a users profile
     * @param  object   instance of the moodleform class
     * @param int $userid id of user whose profile is being edited.
     */
    function profile_definition($mform, $userid = 0) {
    	global $CFG, $DB;
    
    	// if user is "admin" fields are displayed regardless
    	$update = has_capability('moodle/user:update', context_system::instance());
    
    	if ($categories = $DB->get_records('user_info_category', null, 'sortorder ASC')) {
    		foreach ($categories as $category) {
    			if ($fields = $DB->get_records('user_info_field', array('categoryid'=>$category->id), 'sortorder ASC')) {
    
    				// check first if *any* fields will be displayed
    				$display = false;
    				foreach ($fields as $field) {
    					if ($field->visible != PROFILE_VISIBLE_NONE) {
    						$display = true;
    					}
    				}
    
    				// display the header and the fields
    				if ($display or $update) {
    					//$mform->addElement('header', 'category_'.$category->id, format_string($category->name));
    					foreach ($fields as $field) {
    						require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
    						$newfield = 'profile_field_'.$field->datatype;
    						$formfield = new $newfield($field->id, $userid);
    						$formfield->edit_field($mform);
    					}
    				}
    			}
    		}
    	}
    }
 

}


