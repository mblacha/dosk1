<?php

/**
 * Block displaying information about user.
 *
 *
 * @package    block
 * @subpackage int_myprofile
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the current user's profile information.
 *
 */
class block_int_myprofile extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_int_myprofile');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin() or isguestuser()) {
            return '';      // Never useful unless you are logged in as real users
        }
        
    	/*if (!has_capability('block/int_myprofile:addinstance', context_system::instance())) {
            $this->title = '';
            $header = '<div class="header"><img src="'.$CFG->wwwroot.'/blocks/int_myprofile/pix/obsluga-os-szkolonej.png" alt="header"></div>';
    	} else {
			$header = '';
		}       */ 

        $this->content = new stdClass;
      //  $this->content->text = $header;
        $this->content->footer = '';
        //KŁ	
		  $this->content->text .= '<img class="headericon" src="'.$CFG->wwwroot.'/blocks/int_myprofile/pix/obsluga-os-szkolonej.png" alt="header">';
        $this->content->text .= "<ul>\n";
        
        $this->content->text .= '<li>';
        $str = get_string('showprofile', 'block_int_myprofile');      
        $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/int_myprofile/profil.php?blockid='.$this->instance->id.'" title="'. $str .'">'.$str.'</a>';      
        $this->content->text .= "</li>\n";      
        
        $this->content->text .= '</ul><div class="clearer"><!-- --></div>';

        $course = $this->page->course;

        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return false;
    }

    /**
     * allow instances to have their own configuration
     *
     * @return boolean
     */
    function instance_allow_config() {
        //allow instances to have their own configuration
        return false;
    }

    /**
     * instance specialisations (must have instance allow config true)
     *
     */
    public function specialization() {
    }

    /**
     * displays instance configuration form
     *
     * @return boolean
     */
    function instance_config_print() {
        return false;

        /*
        global $CFG;

        $form = new block_int_myprofile.phpConfigForm(null, array($this->config));
        $form->display();

        return true;
        */
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all'=>true);
    }

    /**
     * post install configurations
     *
     */
    public function after_install() {
    }

    /**
     * post delete configurations
     *
     */
    public function before_delete() {
    }

}
