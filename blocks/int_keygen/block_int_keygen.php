<?php

/**
 * Keygen Block page.
 *
 * @package    block
 * @subpackage int_keygen
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * The keygen block class
 */
class block_int_keygen extends block_base {

    /** @var string */
    public $blockname = null;

    /** @var bool */
    protected $contentgenerated = false;

    /** @var bool|null */
    protected $docked = null;

    /**
     * Set the initial properties for the block
     */
    function init() {
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', $this->blockname);
    }
	
	public function has_config() {
        return true;
    }

    /**
     * All multiple instances of this block
     * @return bool Returns false
     */
    function instance_allow_multiple() {
        return true;
    }	
	
	/*
	public function specialization() {

        // Load userdefined title and make sure it's never empty.
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_tags');
        } else {
            $this->title = $this->config->title;
        }
    }
 
	*/

    /**
     * Set the applicable formats for this block to all
     * @return array
     */
    function applicable_formats() {
        if (has_capability('moodle/site:config', context_system::instance())) {
            return array('all' => true);
        } else {
            return array('site' => true);
        }
    }

    /**
     * Gets the content for this block
     */
    function get_content() {

        global $CFG;

        // First check if we have already generated, don't waste cycles
        if ($this->contentgenerated === true) {
            return $this->content;
        }
        $this->content = new stdClass();
		
		$strlist = get_string('listcode', 'block_int_keygen'); 
		$strcont = get_string('countcode', 'block_int_keygen'); 
		$strprefix = get_string('prefixcode', 'block_int_keygen'); 
        $strgenerate = get_string('generatecode', 'block_int_keygen'); 
		
		$countcode = (!empty($CFG->block_int_keygen_countcode)) ? $CFG->block_int_keygen_countcode : '0';
		$prefixcode = (!empty($CFG->block_int_keygen_prefixcode)) ? $CFG->block_int_keygen_prefixcode : '0';

		//$this->content->text = '<div align="center"><img src="'.$CFG->wwwroot.'/blocks/int_keygen/pix/icon.png" alt="icon"></div>';
		
		$this->content->text = '<div class="addkeys">';
        $this->content->text .= '<form method="POST" action="'.$CFG->wwwroot.'/blocks/int_keygen/list.php" style="display:inline"><fieldset class="invisiblefieldset">';
        //$this->content->text .= '<legend class="accesshide_">'.$strlist.'</legend>';
        $this->content->text .= '<input name="sesskey" type="hidden" value="'.sesskey().'" />';  
        $this->content->text .= '<label class="" for="addkeys_count">'.$strcont.'</label>&nbsp;'.
                                '<input id="addkeys_count" name="count" type="text" size="2" value="'.$countcode.'" />';
		$this->content->text .= '<br/><label class="" for="addkeys_prefix">'.$strprefix.'</label>&nbsp;'.
                                '<input id="addkeys_prefix" name="prefix" type="text" size="3" value="'.$prefixcode.'" />';
        $this->content->text .= '&nbsp;<button id="addkeys_button" type="submit" title="'.$strgenerate.'">'.$strgenerate.'</button><br />';
        $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/int_keygen/list.php">'.$strlist.'</a>';
        $this->content->text .= '</fieldset></form></div>';
	
        $this->content->footer = ''; 
        return $this->content;
    }

    /**
     * Returns the role that best describes the keygen block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'navigation';
    }
}


