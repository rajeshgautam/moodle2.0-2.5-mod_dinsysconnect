<?php

/**
 * Config all DinsysConnect instances in this course.
 * @version $Id: version.php,v 3.0.012012/04/30 16:41:20 
 * @package    mod_dinsysconnect
 *  * 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->libdir.'/formslib.php');
class mod_dinsysconnect_mod_form extends moodleform_mod 
{
    function definition() {
        global $CFG;
        $mform = $this->_form;

        //$config = get_config('dinsysconnect');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', 'Meeting Title', array('size'=>'48'));
     
        $mform->addElement('passwordunmask', 'pwd','Meeting Password', 'dinsysconnect','maxlength="32" size="12"');
     $mform->addRule('pwd', get_string('missingpassword'), 'required', null, 'server');

        $mform->setDefault('pwd', '');
        $mform->setType('pwd', PARAM_RAW);
        $mform->addElement('date_time_selector', 'dinsysconnecttime', get_string('dinsysconnecttime', 'dinsysconnect'));
   
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
             
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
           
        }
        
        $mform->addRule('name', null, 'required', null, 'client');
        
        $options = array('STAR' => get_string('star','dinsysconnect'),
                 'MESH' => get_string('mesh','dinsysconnect'));
        $mform->addElement('select', 'connectivity', get_string('connectivitydinsys', 'dinsysconnect') ,$options);
        $mform->addHelpButton('connectivity', 'connectivitydinsys', 'dinsysconnect');
    
        $this->add_intro_editor(true, get_string('dinsysconnectintro', 'dinsysconnect'));
       
     $this->standard_coursemodule_elements();
          
        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            // editing existing instance - copy existing files into draft area
            $draftitemid = file_get_submitted_draft_itemid('files');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_dinsysconnect', 'content', 0, array('subdirs'=>true));
            $default_values['files'] = $draftitemid;
        }
    }
}
