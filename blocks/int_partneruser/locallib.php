<?php
/*
 * @package    blocks
 * @subpackage int_partneruser
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  2013 Intersiec (http://intersiec.com)
 *
 * Keygen library
*/

class block_int_partneruser_manager {
	
	/**
	 * Search user
	 * @param array $search
	 * @return array users
	 */
	public function block_int_partneruser_find_user($search) {
		global $DB;
		
		if(empty($search)){
			return false;
		}
		
		$users = array();
		$userfieldpesel = $DB->get_record('user_info_field', array('shortname'=>'pesel'));		
		if($userfieldpesel){		
			$params = array('fieldpesel'=>$userfieldpesel->id);			
			$sql = "SELECT u.id, u.firstname, u.lastname, uidpesel.data as pesel
					FROM {user} u 
					LEFT JOIN {user_info_data} uidpesel ON u.id=uidpesel.userid AND uidpesel.fieldid = :fieldpesel";
			$where = "\nWHERE u.auth = 'int_keygen' AND u.idnumber ='0'";

			$wheres = array();
			if(!empty($search->firstname)){
				$wheres[] = 'u.firstname LIKE :firstname';
				$params['firstname'] =  $search->firstname;
			}
			if(!empty($search->lastname)){
				$wheres[] = 'u.lastname LIKE :lastname';
				$params['lastname'] =  $search->lastname;
			}
			if(!empty($search->pesel)){
				$wheres[] = 'uidpesel.data LIKE :pesel';
				$params['pesel'] =  $search->pesel;
			}
			
			if(!empty($wheres)){			
				$where .= "\nAND " .implode("\nAND ", $wheres);
				$users = $DB->get_records_sql($sql.$where, $params);
			}			
						
		} 	
		
		return $users;		
	}
	
	/**
	 * Users of partner
	 * @param array $search
	 * @return array users
	 */
	public function block_int_partneruser_users($partnerid) {
		global $DB;
	
		if(empty($partnerid)){
			return false;
		}
	
		$users = array();
		$userfieldpesel = $DB->get_record('user_info_field', array('shortname'=>'pesel'));
	
		if($userfieldpesel){	
			$params = array('fieldpesel'=>$userfieldpesel->id, 'partnerid'=>$partnerid);				
			$sql = "SELECT u.id, u.firstname, u.lastname, uidpesel.data as pesel
					FROM {user} u
					LEFT JOIN {user_info_data} uidpesel ON u.id=uidpesel.userid AND uidpesel.fieldid = :fieldpesel";
			$where = "\nWHERE u.auth = 'int_keygen' AND  u.idnumber = :partnerid";	
			$users = $DB->get_records_sql($sql.$where, $params);	
		}
	
		return $users;
	}
}
