<?php 
/**
 * Version for DinsysConnect Moodle Activity Module.
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 3.0.012012/04/30 16:41:20 
 * @package mod_dinsysconnect 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

$module->version  = 2012040304;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2011070101;         // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)
$module->component = 'mod_dinsysconnect'; // To check on upgrade, that module sits in correct place
$module->maturity = MATURITY_RC;      // [MATURITY_STABLE | MATURITY_RC | MATURITY_BETA | MATURITY_ALPHA]
$module->release  = '2.1.0'; 

?>
