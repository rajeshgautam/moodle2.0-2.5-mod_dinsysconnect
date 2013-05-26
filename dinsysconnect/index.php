<?php // $Id: index.php,v 1.5 2006/08/28 16:41:20 mark-nielsen Exp $
/**
 * This page lists all the instances of dinsysconnect in a particular course
 *
 * @author 
 * @version $Id: index.php,v 3.0.012012/04/30 16:41:20 
 * @package mod_dinsysconnect
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/


    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "dinsysconnect", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strdinsysconnects = get_string("modulenameplural", "dinsysconnect");
    $strdinsysconnect  = get_string("modulename", "dinsysconnect");


/// Print the header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }

    print_header("$course->shortname: $strdinsysconnects", "$course->fullname", "$navigation $strdinsysconnects", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $dinsysconnects = get_all_instances_in_course("dinsysconnect", $course)) {
        notice("There are no dinsysconnects", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("center", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("left", "left", "left");
    }

    foreach ($dinsysconnects as $dinsysconnect) {
        if (!$dinsysconnect->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$dinsysconnect->coursemodule\">$dinsysconnect->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$dinsysconnect->coursemodule\">$dinsysconnect->name</a>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($dinsysconnect->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
