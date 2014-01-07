<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class PartnerUserForm_Renderer extends MoodleQuickForm_Renderer {

	/** @var array Element template array */
	var $_elementTemplates;

	/**
	 * Template used when opening a hidden fieldset
	 * (i.e. a fieldset that is opened when there is no header element)
	 * @var string
	 */
	var $_openHiddenFieldsetTemplate = "";//"\n\t<fieldset class=\"hidden\"><div>";

	/** @var string Header Template string */
	var $_headerTemplate = "";//"\n\t\t<legend class=\"ftoggler\">{header}</legend>\n\t\t";

	/** @var string Template used when opening a fieldset */
	var $_openFieldsetTemplate = "";//"\n\t<fieldset class=\"{classes}\" {id} {aria-live}>";

	/** @var string Template used when closing a fieldset */
	var $_closeFieldsetTemplate = "";//"\n\t\t</div></fieldset>";

	/** @var string Required Note template string */
	var $_requiredNoteTemplate =
	"\n\t\t<div class=\"fdescription required\">{requiredNote}</div>";

	/**
	 * Collapsible buttons string template.
	 *
	 * Note that the <span> will be converted as a link. This is done so that the link is not yet clickable
	 * until the Javascript has been fully loaded.
	 *
	 * @var string
	 */
	var $_collapseButtonsTemplate =
	"\n\t<div class=\"collapsible-actions\"><span class=\"collapseexpand\">{strexpandall}</span></div>";



	/**
	 * Constructor
	 */
	function MoodleQuickForm_Renderer(){
		// switch next two lines for ol li containers for form items.
		//        $this->_elementTemplates=array('default'=>"\n\t\t".'<li class="fitem"><label>{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><div class="qfelement<!-- BEGIN error --> error<!-- END error --> {type}"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}</div></li>');
		$this->_elementTemplates = array(
				'default'=>"\n\t\t".'<div id="{id}" class="fitem {advanced}<!-- BEGIN required --> required<!-- END required --> fitem_{type}" {aria-live}><div class="fitemtitle"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg}{help} </label></div><div class="felement {type}<!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}</div></div>',

				'actionbuttons'=>"\n\t\t".'<div id="{id}" class="fitem fitem_actionbuttons fitem_{type}"><div class="felement {type}">{element}</div></div>',

				'fieldset'=>"\n\t\t".'<div id="{id}" class="fitem {advanced}<!-- BEGIN required --> required<!-- END required --> fitem_{type}"><div class="fitemtitle"><div class="fgrouplabel"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg}{help} </label></div></div><fieldset class="felement {type}<!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}</fieldset></div>',

				'static'=>"\n\t\t".'<div class="fitem {advanced}"><div class="fitemtitle"><div class="fstaticlabel"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg}{help} </label></div></div><div class="felement fstatic <!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}&nbsp;</div></div>',

				'warning'=>"\n\t\t".'<div class="fitem {advanced}">{element}</div>',

				'nodisplay'=>'');

		parent::HTML_QuickForm_Renderer_Tableless();
	}



	/**
	 * What to do when starting the form
	 *
	 * @param MoodleQuickForm $form reference of the form
	 */
	function startForm(&$form){
		global $PAGE;
		$this->_reqHTML = $form->getReqHTML();
		$this->_elementTemplates = str_replace('{req}', $this->_reqHTML, $this->_elementTemplates);
		$this->_advancedHTML = $form->getAdvancedHTML();
		$this->_collapseButtons = '';
		$formid = $form->getAttribute('id');
		parent::startForm($form);
		if ($form->isFrozen()){
			$this->_formTemplate = "\n<div class=\"mform frozen\">\n{content}\n</div>";
		} else {
			$this->_formTemplate = "\n<form{attributes}>\n\t<div style=\"display: none;\">{hidden}</div>\n{collapsebtns}\n{content}\n</form>";
			$this->_hiddenHtml .= $form->_pageparams;
		}

		if ($form->is_form_change_checker_enabled()) {
			$PAGE->requires->yui_module('moodle-core-formchangechecker',
					'M.core_formchangechecker.init',
					array(array(
							'formid' => $formid
					))
			);
			$PAGE->requires->string_for_js('changesmadereallygoaway', 'moodle');
		}
		if (!empty($this->_collapsibleElements)) {
			if (count($this->_collapsibleElements) > 1) {
				$this->_collapseButtons = $this->_collapseButtonsTemplate;
				$this->_collapseButtons = str_replace('{strexpandall}', get_string('expandall'), $this->_collapseButtons);
				$PAGE->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
			}
			$PAGE->requires->yui_module('moodle-form-shortforms', 'M.form.shortforms', array(array('formid' => $formid)));
		}
		if (!empty($this->_advancedElements)){
			$PAGE->requires->strings_for_js(array('showmore', 'showless'), 'form');
			$PAGE->requires->yui_module('moodle-form-showadvanced', 'M.form.showadvanced', array(array('formid' => $formid)));
		}
	}

	/**
	 * Renders element
	 *
	 * @param HTML_QuickForm_element $element element
	 * @param bool $required if input is required field
	 * @param string $error error message to display
	 */
	function renderElement(&$element, $required, $error){
		// Make sure the element has an id.
		$element->_generateId();

		//adding stuff to place holders in template
		//check if this is a group element first
		if (($this->_inGroup) and !empty($this->_groupElementTemplate)) {
			// so it gets substitutions for *each* element
			$html = $this->_groupElementTemplate;
		}
		elseif (method_exists($element, 'getElementTemplateType')){
			$html = $this->_elementTemplates[$element->getElementTemplateType()];
		}else{
			$html = $this->_elementTemplates['default'];
		}
		if (isset($this->_advancedElements[$element->getName()])){
			$html = str_replace(' {advanced}', ' advanced', $html);
			$html = str_replace(' {aria-live}', ' aria-live="polite"', $html);
		} else {
			$html = str_replace(' {advanced}', '', $html);
			$html = str_replace(' {aria-live}', '', $html);
		}
		if (isset($this->_advancedElements[$element->getName()])||$element->getName() == 'mform_showadvanced'){
			$html =str_replace('{advancedimg}', $this->_advancedHTML, $html);
		} else {
			$html =str_replace('{advancedimg}', '', $html);
		}
		$html =str_replace('{id}', 'fitem_' . $element->getAttribute('id'), $html);
		$html =str_replace('{type}', 'f'.$element->getType(), $html);
		$html =str_replace('{name}', $element->getName(), $html);
		if (method_exists($element, 'getHelpButton')){
			$html = str_replace('{help}', $element->getHelpButton(), $html);
		}else{
			$html = str_replace('{help}', '', $html);

		}
		if (($this->_inGroup) and !empty($this->_groupElementTemplate)) {
			$this->_groupElementTemplate = $html;
		}
		elseif (!isset($this->_templates[$element->getName()])) {
			$this->_templates[$element->getName()] = $html;
		}

		parent::renderElement($element, $required, $error);
	}

	/**
	 * Called when visiting a form, after processing all form elements
	 * Adds required note, form attributes, validation javascript and form content.
	 *
	 * @global moodle_page $PAGE
	 * @param moodleform $form Passed by reference
	 */
	function finishForm(&$form){
		global $PAGE;
		if ($form->isFrozen()){
			$this->_hiddenHtml = '';
		}
		parent::finishForm($form);
		$this->_html = str_replace('{collapsebtns}', $this->_collapseButtons, $this->_html);
		if (!$form->isFrozen()) {
			$args = $form->getLockOptionObject();
			if (count($args[1]) > 0) {
				$PAGE->requires->js_init_call('M.form.initFormDependencies', $args, true, moodleform::get_js_module());
			}
		}
	}
	/**
	 * Called when visiting a header element
	 *
	 * @param HTML_QuickForm_header $header An HTML_QuickForm_header element being visited
	 * @global moodle_page $PAGE
	 */
	function renderHeader(&$header) {
		global $PAGE;

		$header->_generateId();
		$name = $header->getName();

		$id = empty($name) ? '' : ' id="' . $header->getAttribute('id') . '"';
		if (is_null($header->_text)) {
			$header_html = '';
		} elseif (!empty($name) && isset($this->_templates[$name])) {
			$header_html = str_replace('{header}', $header->toHtml(), $this->_templates[$name]);
		} else {
			$header_html = str_replace('{header}', $header->toHtml(), $this->_headerTemplate);
		}

		if ($this->_fieldsetsOpen > 0) {
			$this->_html .= $this->_closeFieldsetTemplate;
			$this->_fieldsetsOpen--;
		}

		// Define collapsible classes for fieldsets.
		$arialive = '';
		$fieldsetclasses = array('clearfix');
		if (isset($this->_collapsibleElements[$header->getName()])) {
			$fieldsetclasses[] = 'collapsible';
			$arialive = 'aria-live="polite"';
			if ($this->_collapsibleElements[$header->getName()]) {
				$fieldsetclasses[] = 'collapsed';
			}
		}

		if (isset($this->_advancedElements[$name])){
			$fieldsetclasses[] = 'containsadvancedelements';
		}

		$openFieldsetTemplate = str_replace('{id}', $id, $this->_openFieldsetTemplate);
		$openFieldsetTemplate = str_replace('{classes}', join(' ', $fieldsetclasses), $openFieldsetTemplate);
		$openFieldsetTemplate = str_replace('{aria-live}', $arialive, $openFieldsetTemplate);

		$this->_html .= $openFieldsetTemplate . $header_html;
		$this->_fieldsetsOpen++;
	}

	/**
	 * Return Array of element names that indicate the end of a fieldset
	 *
	 * @return array
	 */
	function getStopFieldsetElements(){
		return $this->_stopFieldsetElements;
	}
}

$GLOBALS['_HTML_QuickForm_default_renderer'] = new PartnerUserForm_Renderer();

class user_partneruser_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE, $USER, $DB;

        $mform =& $this->_form;

        $userid = $USER->id;
        $blockid = 0;

        if (is_array($this->_customdata)) {           
            if (array_key_exists('id', $this->_customdata)) {
                $userid = $this->_customdata['id'];
            }
            if (array_key_exists('blockid', $this->_customdata)) {
            	$blockid = $this->_customdata['blockid'];            
            }
        }
        //Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');
                
        $user = $DB->get_record('user', array('id' => $userid));
        $authplugin = get_auth_plugin($user->auth);
        //useredit_load_preferences($user, false);
        
        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'blockid', $blockid);
        $mform->setType('blockid', PARAM_INT);
        
        
        $mform->addElement('header', 'moodle', '');
        
        $mform->addElement('html', '<div class="first_line">');
        
        /* dane osobowe */
        $mform->addElement('html', '<div class="person">');
        $mform->addElement('html', '<div class="title">'.get_string('formusertitleperson', 'block_int_partneruser').'</div>');
        $mform->addElement('text', 'lastname',  get_string('formuserlastname', 'block_int_partneruser'),  'maxlength="100" size="34"');
        $mform->addElement('text', 'firstname', get_string('formuserfirstname', 'block_int_partneruser'), 'maxlength="100" size="34"');
        //$mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);
        
        //$mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);
        
        if ($category = $DB->get_record('user_info_category', array('name'=>'kursant'))) {
        	if ($fields = $DB->get_records('user_info_field', array('categoryid'=>$category->id), 'sortorder ASC')) {        		
        		foreach ($fields as $field) {
        			
        			if($field->shortname == 'pesel'){
        				
        				$mform->addElement('html', '<div class="fitem fitem_ftext">');
        			
        				$mform->addElement('html', '<div class="fitemtitle"><label>'.
        						get_string('formuserpesel', 'block_int_partneruser').'</label>');   
        						     				
        				
        				$mform->addElement('html', '<div class="peselno" >');          				
        				$mform->addElement('checkbox', 'profile_field_peselbrak');        				
        				$mform->addElement('html', ''.get_string('formuserpeselno', 'block_int_partneruser').'');
        				$mform->addElement('html', '</div>');       
        								
        				
        				
        				$mform->addElement('html', '</div>');        				
        				
        				$mform->addElement('text', 'profile_field_'.$field->shortname, '', 'maxlength="100" size="34" ');
        				$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
        				
        				$mform->addElement('html', '</div>');
        				continue;
        			}
        			
        			if($field->shortname == 'dataur'){
        				$mform->addElement('text', 'profile_field_'.
        						$field->shortname, get_string('formuserdateofbirth', 'block_int_partneruser'), 'maxlength="100" size="34" class="calendar" ');
        				$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
        				continue;
        			}
        			
        			if($field->shortname == 'miejsceur'){
        				$mform->addElement('text', 'profile_field_'.
        						$field->shortname, get_string('formuserplaceofbirth', 'block_int_partneruser'), 'maxlength="100" size="34" ');
        				$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
        				continue;
        			}
        			
        		}
        	}
        }
        $mform->addElement('html', '</div>');
        
        $mform->addElement('html', '<div class="address">');
        $mform->addElement('html', '<div class="title">'.get_string('formusertitleaddress', 'block_int_partneruser').'</div>');       
        if ($category = $DB->get_record('user_info_category', array('name'=>'adres'))) {       	
        	if ($fields = $DB->get_records('user_info_field', array('categoryid'=>$category->id), 'sortorder ASC')) {    
	        	
        		foreach ($fields as $field) {					

					if($field->shortname == 'miasto'){
						$mform->addElement('text', 'profile_field_'.$field->shortname, 
								get_string('formuserpostcode', 'block_int_partneruser'), 'maxlength="100" size="34" ');
						$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
						continue;
					}
					
					if($field->shortname == 'ulica'){
						$mform->addElement('text', 'profile_field_'.$field->shortname, 
								get_string('formuserstreet', 'block_int_partneruser'), 'maxlength="100" size="34" ');
						$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
						continue;
					}
					
					if($field->shortname == 'nrdomu'){
						$mform->addElement('text', 'profile_field_'.$field->shortname, 
								get_string('formuserno1', 'block_int_partneruser'), 'maxlength="100" size="34" ');
						$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
						continue;
					}
					
					if($field->shortname == 'nrlokalu'){
						$mform->addElement('text', 'profile_field_'.$field->shortname, 
								get_string('formuserno2', 'block_int_partneruser'), 'maxlength="100" size="34" ');
						$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
						continue;
					}
					
					if($field->shortname == 'telefon'){
						$mform->addElement('text', 'profile_field_'.$field->shortname, 
								get_string('formuserphone', 'block_int_partneruser'), 'maxlength="100" size="34" ');
						$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
						continue;
					}					
	        	}
        	}        	
       	}       
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '</div>');//first_line
 
        $mform->addElement('html', '<div class="second_line">');
        
        $mform->addElement('html', '<div class="auth">');
        $mform->addElement('html', '<div class="title">'.get_string('formusertitleauth', 'block_int_partneruser').'</div>');
        if ($category = $DB->get_record('user_info_category', array('name'=>'prawo_jazdy'))) {        
        	if ($fields = $DB->get_records('user_info_field', array('categoryid'=>$category->id), 'sortorder ASC', 'shortname, name')) {        		
        		
        		if(isset($fields['nrprawajazdy'])){
        			$field = $fields['nrprawajazdy'];
        			$mform->addElement('text', 'profile_field_'.
        					$field->shortname, get_string('formusernumberdriver', 'block_int_partneruser'), 'maxlength="100" size="43" ');
        			$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT);
        		}
        		
        		$mform->addElement('html', '<div class="fitem fitem_ftext">');
        		$mform->addElement('html', '<div class="fitemtitle" style="height: 43px;"><label>'.
        				get_string('formusercategorydriver', 'block_int_partneruser').'</label></div>');
        		$mform->addElement('html', '<div class="category">');
        		
        		foreach ($fields as $field) {
        		
        			if($field->shortname == 'nrprawajazdy'){        				
        				continue;	 
        			}        			
        			
        			$mform->addElement('checkbox', 'profile_field_'.$field->shortname, '', format_string($field->name));          			
        		}
        		$mform->addElement('html', '</div></div>');
        	}
        }
        $mform->addElement('html', '</div>');
        
        /* swiadectwo */
        $mform->addElement('html', '<div class="certificate">');
        $mform->addElement('html', '<div class="title">'.get_string('formusertitlecert', 'block_int_partneruser').'</div>');
  
        if ($category = $DB->get_record('user_info_category', array('name'=>'swiadectwo'))) {
        	if ($fields = $DB->get_records('user_info_field', array('categoryid'=>$category->id), 'sortorder ASC', 'shortname, name')) {            		
        		
        		
        		if(isset($fields['nrswiadectwa'])){
        			
        			$mform->addElement('html', '<div style="margin-top: 5px;">');
        			
        			$field = $fields['nrswiadectwa'];
        			$mform->addElement('text', 'profile_field_'.$field->shortname, get_string('formusernumbercert', 'block_int_partneruser'), 'maxlength="100" size="30" ');
        			$mform->setType('profile_field_'.$field->shortname, PARAM_TEXT); 

        			$mform->addElement('html', '<div class="nrswiadectwano" >');
        			//$mform->addElement('html', '<input name="profile_field_nrswiadectwabrak" type="checkbox" value="1" id="id_profile_field_nrswiadectwabrak" />');
        			$mform->addElement('checkbox', 'profile_field_nrswiadectwabrak');
        			//$mform->disabledIf('reset_gradebook_grades', 'reset_gradebook_items', 'checked');
        			$mform->addElement('html', '<div style="float: right; text-align: center;">'.get_string('formusernumbercertno', 'block_int_partneruser').'</div>');
        			$mform->addElement('html', '</div>');
        			
        			$mform->addElement('html', '</div>');
        			
        		}        		
        		
        		$mform->addElement('html', '<div class="fitem fitem_ftext">');
        		$mform->addElement('html', '<div class="fitemtitle" style="height: 44px;"><label>'.
        				get_string('formusercatcert', 'block_int_partneruser').'</label></div>');
        		$mform->addElement('html', '<div class="category">');
        		
        		foreach ($fields as $field) {
        		
        			if($field->shortname == 'nrswiadectwa'){        				
        				continue;
        			}
        			
        			if($field->shortname == 'nrswiadectwabrak'){        				
        				continue;
        			}
        			
        			if($field->shortname == 'katswiadectwabrak'){
        				continue;
        			}
        			 
        			$mform->addElement('checkbox', 'profile_field_'.$field->shortname, '', format_string($field->name));
        		}
        		$mform->addElement('html', '</div></div>');  

        		
        		$mform->addElement('html', '<div class="katswiadectwano" >');
        		//$mform->addElement('html', '<br><input name="profile_field_katswiadectwabrak" type="checkbox" value="1" id="id_profile_field_katswiadectwabrak" />');
        		$mform->addElement('checkbox', 'profile_field_katswiadectwabrak');
        		$mform->addElement('html', '<div style="float: right; text-align: center;">'.get_string('formusercatcertno', 'block_int_partneruser').'</div>');
        		$mform->addElement('html', '</div>');
        		
        		
        	
        		
        	}
        }
        $mform->addElement('html', '</div>');
        
        $mform->addElement('html', '</div>');//second line
        
        
        $mform->addElement('html', '<div id="fitem_id_submitbutton" class="fitem fitem_actionbuttons fitem_fsubmit">');
        $mform->addElement('html', '<div class="fbutton">');
        $mform->addElement('html', '<input name="submitbutton" value="'.get_string('formusersave', 'block_int_partneruser').'" type="submit" id="id_submitbutton" />');
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="fbutton">');
        $mform->addElement('html', '<button onclick="location.href=\''.$CFG->wwwroot.'/blocks/int_partneruser/profil.php?id='.$userid.'\';">'.get_string('formuserback', 'block_int_partneruser').'</button>');
        $mform->addElement('html', '</div>');
        		
    
        $mform->addElement('html', '</div>');
 
        

        //$this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');

        if ($user = $DB->get_record('user', array('id'=>$userid))) {
            /// Next the customisable profile fields
            profile_definition_after_data($mform, $user->id);
        } else {
            profile_definition_after_data($mform, 0);
        }
    }

    /*
    function validation($usernew, $files) {
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;
        $user    = $DB->get_record('user', array('id'=>$usernew->id));
               
        if(!$this->check_pesel($usernew->profile_field_pesel)) {
        	$errors['profile_field_pesel'] = get_string('errorpesel', 'auth_int_keygen');
        }
        
        /// Next the customisable profile fields
        $errors += profile_validation($usernew, $files);
   
        return $errors;
    }
    */
    /*
    function check_pesel($str) {
    	if (!preg_match('/^[0-9]{11}$/',$str)) {
    		return false;
    	}
    
    	$arrSteps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3); 
    	$intSum = 0;
    	for ($i = 0; $i < 10; $i++)	{
    		$intSum += $arrSteps[$i] * $str[$i]; 
    	}
    	$int = 10 - $intSum % 10; 
    	$intControlNr = ($int == 10)?0:$int;
    	if ($intControlNr == $str[10]) {
    		return true;
    	}
    	return false;
    }
    */
}


