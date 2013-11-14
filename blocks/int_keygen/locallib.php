<?php
/*
 * @package    blocks
 * @subpackage int_keygen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  2013 Intersiec (http://intersiec.com)
 *
 * Keygen library
*/

class block_int_keygen_manager {
	/**
	 * Generator klucza
	 * 
	 * @param index iteracja
	 * @param string course shortname
	 * @return key 
	 */
	function block_int_keygen_create_key($index, $shortname){
		$key = substr( $shortname, 0, 5);
		$key .= '-' . substr( md5('keygen1'.time().$index),0, 5 );
		$key .= '-' . substr( md5('keygen2'.time().$index),0, 5 );
		$key .= '-' . substr( md5('keygen3'.time().$index),0, 5 );
		$key .= '-' . substr( md5('keygen4'.time().$index),0, 5 );
		
		return strtoupper($key);
		
	}
}
