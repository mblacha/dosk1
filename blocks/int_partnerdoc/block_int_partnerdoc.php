<?php

/**
 * Block displaying information about user.
 *
 *
 * @package    block
 * @subpackage int_partnerdoc
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the current user's profile information.
 *
 */
class block_int_partnerdoc extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_int_partnerdoc');
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
    

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
		
		$this->content->text .= "<ul>\n";
        
		//docs
        $this->content->text .= '<li>';
        $str = get_string('cardclasses', 'block_int_partnerdoc');
        $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/int_partnerdoc/docs.php" title="'. $str .'">'.$str.'</a>';
        $this->content->text .= "</li>\n";
        
        //osk
        $this->content->text .= '<li>';
        $str = get_string('oskprofil', 'block_int_partnerdoc');
        $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/int_partnerdoc/osk.php" title="'. $str .'">'.$str.'</a>';
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

        $form = new block_int_partnerdoc.phpConfigForm(null, array($this->config));
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
