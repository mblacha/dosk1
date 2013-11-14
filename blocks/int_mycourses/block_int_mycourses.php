<?php

/**
 * Block displaying information about user.
 *
 *
 * @package    block
 * @subpackage int_mycourses
 * @copyright  2013 Intersiec (http://intersiec.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the current user's profile information.
 *
 */
class block_int_mycourses extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title   = get_string('pluginname', 'block_int_mycourses');
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
 
        if (!has_capability('block/int_mycourses:addinstance', context_system::instance())) {
            $this->title = '';
            $header = '<div class="header"><img src="'.$CFG->wwwroot.'/blocks/int_mycourses/pix/header.png" alt="header"></div>';            
        } else {
			$header = '';
		}         
        
        $this->content = new stdClass;
        $this->content->text = $header;
        $this->content->footer = '';        
        
        if($mycourses=$this->get_remote_courses()){
        	$this->content->text .= "<ul>\n";
        	
        	//var_dump($mycourses);
        	
        	foreach ($mycourses as $mycourse){
        		//$coursecontext = context_course::instance($mycourse->id);
        		
        		$this->content->text .= '<li>';
        		$str =  'co to';//format_string($mycourse->shortname, true, array('context' => $coursecontext));
        		$this->content->text .= '<a href="'.$CFG->wwwroot.'/auth/mnet/jump.php?hostid='.$mycourse->hostid.
        		        '&amp;wantsurl=/course/view.php?id='.$mycourse->remoteid.'" title="'. $str .'">'.format_string($mycourse->fullname).'</a>';
        		$this->content->text .= "</li>\n";
        	}
        	
        	$this->content->text .= '</ul><div class="clearer"><!-- --></div>';
        	        	
        }
        
        $course = $this->page->course;

        return $this->content;
    }
    
    
    function get_remote_courses() {
    	global $CFG, $USER, $OUTPUT;
    
    	if (!is_enabled_auth('mnet')) {
    		// no need to query anything remote related
    		return;
    	}
    
    	//$icon = '<img src="'.$OUTPUT->pix_url('i/mnethost') . '" class="icon" alt="" />';
    
    	// shortcut - the rest is only for logged in users!
    	if (!isloggedin() || isguestuser()) {
    		return false;
    	}
    
    	if ($courses = get_my_remotecourses()) {
    		/*
    		$this->content->items[] = get_string('remotecourses','mnet');
    		$this->content->icons[] = '';
    		foreach ($courses as $course) {
    			$coursecontext = context_course::instance($course->id);
    			$this->content->items[]="<a title=\"" . format_string($course->shortname, true, array('context' => $coursecontext)) . "\" ".
    					"href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
    					.$icon. format_string($course->fullname) . "</a>";
    		}
    		*/
    		// if we listed courses, we are done
    		return $courses;
    	}
    
    	/*
    	if ($hosts = get_my_remotehosts()) {
    		$this->content->items[] = get_string('remotehosts', 'mnet');
    		$this->content->icons[] = '';
    		foreach($USER->mnet_foreign_host_array as $somehost) {
    			$this->content->items[] = $somehost['count'].get_string('courseson','mnet').'<a title="'.$somehost['name'].'" href="'.$somehost['url'].'">'.$icon.$somehost['name'].'</a>';
    		}
    		// if we listed hosts, done
    		return true;
    	}
    	*/
    
    	return false;
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
