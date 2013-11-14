<?php
/*
 * @package    blocks
 * @subpackage int_partnercourse
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  2013 Intersiec (http://intersiec.com)
 *
 * Keygen library
*/

class block_int_partnercourse_manager {
	
	/**
	 * Users of partner
	 * @param array $search
	 * @return array users
	 */
	public function block_int_partnercourse_courses($partnerid) {
		global $DB;
	
		if(empty($partnerid)){
			return false;
		}
	
		$courses = array();		
		$userfieldpesel = $DB->get_record('user_info_field', array('shortname'=>'pesel'));
	
		if($userfieldpesel){	
			$params = array('fieldpesel'=>$userfieldpesel->id, 'partnerid'=>$partnerid);				
			$sql = "SELECT ue.id, ec.hostid, ec.remoteid, ec.fullname, ec.shortname, ec.categoryname,
					u.username, u.firstname, u.lastname, uidpesel.data as pesel
					FROM {mnetservice_enrol_enrolments} ue 
					JOIN {mnetservice_enrol_courses} ec ON ue.hostid=ec.hostid AND ue.remotecourseid = ec.remoteid
					JOIN {user} u ON ue.userid=u.id
					JOIN {user_info_data} uidpesel ON u.id=uidpesel.userid AND uidpesel.fieldid = :fieldpesel";
			
			$where = "\nWHERE u.auth = 'int_keygen' AND  u.idnumber = :partnerid ORDER BY u.lastname, u.firstname";				
			$courses = $DB->get_records_sql($sql.$where, $params);				
		}	
		return $courses;
	}
	
	/**
	 * Users list of partner
	 * @param array $search
	 * @return array users
	 */
	public function block_int_partnercourse_users_array($users, $partnerid) {
		global $DB;
	
		if(empty($partnerid)){
			return false;
		}

		$userfieldpesel = $DB->get_record('user_info_field', array('shortname'=>'pesel'));	
		if($userfieldpesel){	
			$params = array('fieldpesel'=>$userfieldpesel->id, 'partnerid'=>$partnerid);	
			$sql = "SELECT u.id, u.firstname, u.lastname, uidpesel.data as pesel
					FROM {user} u
					LEFT JOIN {user_info_data} uidpesel ON u.id=uidpesel.userid AND uidpesel.fieldid = :fieldpesel";
			$where = "\nWHERE u.auth = 'int_keygen' AND  u.idnumber = :partnerid ORDER BY u.lastname, u.firstname";
	
			if($partnerusers = $DB->get_records_sql($sql.$where, $params)){
				foreach($partnerusers as $partneruser){
					$users[$partneruser->id] = $partneruser->lastname . ' ' .$partneruser->firstname;
				}
			}
		}
	
		return $users;
	}
	
	public function block_int_partnercourse_courses_array($arraycourses, $usecache){
		$service = mnetservice_enrol::get_instance();
		
		// remote hosts that may publish remote enrolment service and we are subscribed to it
		$hosts = $service->get_remote_publishers();
		
		if (!$usecache) {
			// our local database will be changed
			require_sesskey();
		}
		
		foreach($hosts as $host){
			$hostcourses = $service->get_remote_courses($host->id, $usecache);
			if (is_string($hostcourses)) {
				//print_error('fetchingcourses', 'mnetservice_enrol', '', null, $service->format_error_message($courses));
				continue;
			}
						
			foreach($hostcourses as $hostcourse){				
				$arraycourses[implode(',', array($host->id, $hostcourse->remoteid))] = $hostcourse->shortname;
			}			
		}
		return $arraycourses;
	}
	
	public function enrol_partner($course){
		global $CFG, $USER, $DB;
		
		require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');
		
		$service = mnetservice_enrol::get_instance();
		
		if (!$service->is_available()) {
			return false;
		}
		
		$user = clone $USER;
		$user->timestart = time();
		$user->timeend = 0;
		$user->role = 'studentpartner';
			
		$service->req_enrol_user($user, $course);
	
		return 	$service->req_enrol_user($user, $course);		
	}
	
	public function create_order($user, $course){
		global $USER, $DB;
	
		$order = new stdClass();
		$order->parentid = $USER->id;
		$order->userid = $user->id;
		$order->hostid = $course->hostid;
		$order->remotecourseid = $course->remoteid;
		$order->deleted = 0;
		$order->suspended = 0;
		$order->timestart = $user->timestart;
		$order->timeend = $user->timeend;
		$order->timecreated = time();
		$order->timeupdated = 0;
		
		$order->id = $DB->insert_record('block_int_partnercourse', $order, true);
	
		return 	$order;
	}
}
