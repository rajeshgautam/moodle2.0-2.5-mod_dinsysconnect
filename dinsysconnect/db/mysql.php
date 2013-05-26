<?php // $Id: mysql.php,v 1.3 2006/08/28 16:41:20 mark-nielsen Exp $
/**
 * Upgrade procedures for NEWMODULE
 *
 * @author 
 * @version $Id: mysql.php,v 3.0.012012/04/30 16:41:20 
 * @package mod_dinsysconnect
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/**
 * This function does anything necessary to upgrade 
 * older versions to match current functionality 
 *
 * @uses $CFG
 * @param int $oldversion The prior version number
 * @return boolean Success/Failure
 **/
function dinsysconnect_upgrade($oldversion) {
    global $CFG;

    if ($oldversion < 2006042900) {
      
    }

    return true;
}

?>
