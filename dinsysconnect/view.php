<?php

/**
 * Join a DinsysConnect Meeting
 *
 * Authors:
 *    
 * 
 * @package   mod_dinsysconnect
 * @version $Id: version.php,v 3.0.01 2011/12/01 16:41:20 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot . '/mod/dinsysconnect/lib.php');
require_once($CFG->libdir . '/completionlib.php');

$url='https://www.dinsys.com/ss.php';

$data['name']='fdf';
$result=trim(post($url,$data));
if (strpos($result,'ERROR') !== false)
       {
            $msg=$result;
            ?>
            <script language="javascript">
alert('<?php echo $msg ?>')
</script>
           <?php
            
          echo $msg;
         return;       
       }

parse_str($result);

$id   = optional_param('id', 0, PARAM_INT);
$c    = optional_param('c', 0, PARAM_INT);
$edit = optional_param('edit', -1, PARAM_BOOL);

if ($id) {
    if (! $cm = get_coursemodule_from_id('dinsysconnect', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }
  
    if (! $dinsysconnect = $DB->get_record('dinsysconnect', array('id'=>$cm->instance))) {
        print_error('invalidid', 'dinsysconnect');
    }

} 
else 
{
 
    if (! $dinsysconnect = $DB->get_record('dinsysconnect', array('id'=>$c)))
	{
        print_error('coursemisconf');
    }
    if (! $course = $DB->get_record('course', array('id'=>$dinsysconnect->course))) 
	{
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('dinsysconnect', $dinsysconnect->id, $course->id)) 
	{
        print_error('invalidcoursemodule');
    }
}

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$PAGE->set_context($context);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'dinsysconnect')));

add_to_log($course->id, 'dinsysconnect', 'view', "view.php?id=$cm->id", $dinsysconnect->id, $cm->id);

$courseshortname = format_string($course->shortname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
$title = $courseshortname . ': ' . format_string($dinsysconnect->name);

// Mark viewed by user (if required)
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Initialize $PAGE
$PAGE->set_url('/mod/dinsysconnect/view.php', array('id' => $cm->id));
$PAGE->set_title($title);
//$PAGE->set_heading($course->fullname);
?>

<h1 style="margin-left:50px;"align="left" ><font size="5" face="Georgia" color="Black"> <img src="pix/logo.png" alt="Logo" style="float:center;margin-left: 10px; margin-right: 10px;" />Dinsys Technologies </font>
</h1>
    
<h1 align="center"><font size="5" face="Georgia" color="#0033FF"> Course : &nbsp; </font>
    <font size="5" face="Georgia" color="Green">
<?php
echo $course->fullname;
?>
    </font>
</h1>   
<?php
if (isset($_GET['MeetingAction']))
{
    if ($_GET['MeetingAction'] == 'start')
    {      
       $startResponse = startMeeting($serverIP,$serverPort,$orgID/*orgID*/,$dinsysconnect->id, $dinsysconnect->pwd,$USER->id,$dinsysconnect->connectivity);

       if (strpos($startResponse,'ERROR') !== false)
       {
           echo 'Meeting cannot be started ! <br>' . $startResponse;
          return;
       }
        list($meetingPort, $meetingPID) = explode('.', $startResponse);
        $dinsysconnect->portno = $meetingPort;
        $dinsysconnect->processid = $meetingPID;
        dinsysconnect_update($dinsysconnect);
        //$ss=$_SERVER['HTTP_REFERER'];
        header("Location: ".$_SERVER['HTTP_REFERER']);
exit();
    } 
    else if ($_GET['MeetingAction'] == 'stop')
    {
        $stopResponse = stopMeeting($serverIP, intval($serverPort),$USER->id,$dinsysconnect-> processid);
       if (strpos($stopResponse,'ERROR') !== false)
       {
           echo 'Meeting cannot be stopped <br>' . $stopResponse ;
          return;
       }     
        $dinsysconnect->portno = 0;
        $dinsysconnect->processid = 0;
        dinsysconnect_update($dinsysconnect);
        header("Location: ".$_SERVER['HTTP_REFERER']);
exit();
    }
    else if ($_GET['MeetingAction'] == 'join')
    {
        if ($dinsysconnect->portno > 0)
        {
         $userSessId =  rand();
         $ss=   sendSessionId($serverIP,$serverPort, $dinsysconnect->portno,$USER->id,strval($userSessId),$_SERVER['REMOTE_ADDR']);
         
        if (strpos($ss,'ERROR') !== false)
       {
           echo 'Meeting cannot be joined <br>' . $ss ;
          return;
       }
         
$joinURL = $dinsysConnectPublishUrl.'?MeetingCode='.$dinsysconnect->id.'&UserName='.$USER->username.'&WebSiteUrl='.$serverIP.'/'.$dinsysconnect->portno.'&MoodleSessionId='.$userSessId.'&UserId='.$USER->id;
header("Location: ".$joinURL);

exit();
        }
    }

}

/// Print the page header
echo $OUTPUT->header();
?>

<h2 align="center"><font size="4" face="Georgia" color="black"><img src="pix/logo.png" alt="Logo" style="float:center;margin-left: 10px; margin-right: 10px;" />Dinsys Connect : &nbsp; </font>
    <font size="4" face="Georgia" color="Green">
<?php
echo $dinsysconnect->name;
?>
    </font>
</h2>   

          
<?php              
      

if ($dinsysconnect->intro) {
    ?>
<style type="text/css">
     p.box
{
border-style:solid;
border-color:#D3D3D3;
border-width:1px;
border-radius: 5px 10px 5px 10px / 10px 5px 10px 5px;
border-radius: 5px;
border-radius: 5px 10px / 10px;
 padding: 20px 
}
</style>  
<?php
   echo "<p class='box'>";
                echo'<font size="3" face="sans serif" color="black">';
      $str = str_replace("<p>", "", $dinsysconnect->intro);
      $str2 = str_replace("</p>", "", $str);

    echo $str2 .'<br><br>';
    echo"</font>";
//echo "</p>"; 
}

if ($dinsysconnect->portno > 0)
{
	$PID = getMeetingPID($serverIP, $serverPort, intval($dinsysconnect->portno));
        if (strpos($PID,'ERROR') !== false)
       {
           echo 'Error in Meeting! <br>' . $PID ;
          return;
       }
		
       if (strpos($PID,'ERROR') !== false || ($PID != (($dinsysconnect->processid) )))
       {
		   $startResponse = startMeeting($serverIP,$serverPort,$orgID/*orgID*/,$dinsysconnect->id, $dinsysconnect->pwd,$USER->id,$dinsysconnect->connectivity);

		   if (strpos($startResponse,'ERROR') !== false)
		   {
			   echo 'Meeting cannot be started ! <br>' . $startResponse;
			  return;
		   }
			list($meetingPort, $meetingPID) = explode('.', $startResponse);
			$dinsysconnect->portno = $meetingPort;
			$dinsysconnect->processid = $meetingPID;
			dinsysconnect_update($dinsysconnect);
		}
	   
$ua = $_SERVER["HTTP_USER_AGENT"];
$windows = 'Windows';
 $pos = strpos($ua,$windows);
if ($pos <= 0) {
    
   echo 'Note : To access this activity you need to login from <b>Windows<b> as Dinsys Connect is compatible to run only on Windows';
       echo '<br><br>';
} 
else {
    $firefox=strpos($ua,'Firefox');
        $chrome=strpos($ua,'Chrome');
        $msie = strpos($ua,'MSIE');
        if($msie > 0)
        { 
			echo '<a href="';
			 $rr='view.php'. '?'.$_SERVER['QUERY_STRING'].'&MeetingAction=join' ;
			   echo htmlentities($rr) ;
			echo '">Join Meeting</a>';
			echo '<br><br>';    
        }
        else if($firefox  > 0 )
        {
					echo '<a href="';
			 $rr='view.php'. '?'.$_SERVER['QUERY_STRING'].'&MeetingAction=join' ;
			   echo htmlentities($rr) ;
			echo '">Join Meeting</a>';
			echo '<br><br>';
			
				 echo 'Note : To access this activity from Firefox, please install a plugin(addon) from the following link : <br>'
		.'<a href ="https://addons.mozilla.org/en-US/firefox/addon/microsoft-net-framework-assist/" >https://addons.mozilla.org/en-US/firefox/addon/microsoft-net-framework-assist/</a>'; 
			 echo '<br><br>';
        }
         else if($chrome > 0 )
        {
            echo '<a href="';
     $rr='view.php'. '?'.$_SERVER['QUERY_STRING'].'&MeetingAction=join' ;
       echo htmlentities($rr) ;
    echo '">Join Meeting</a>';
    echo '<br><br>';
    
    echo 'Note : To access this activity from Chrome, please install a plugin(addon) from the following link : <br>'
.'<a href ="https://chrome.google.com/webstore/detail/eeifaoomkminpbeebjdmdojbhmagnncl/related">https://chrome.google.com/webstore/detail/eeifaoomkminpbeebjdmdojbhmagnncl/related
</a>';      
         echo '<br><br>';
        }
}     
}
else
{
    echo 'Meeting not yet started ! <br><br>'; 
    
}

   $courseid=    $course->id;
    $context_course = get_context_instance(CONTEXT_COURSE,$courseid);
    $iseditortype=FALSE;

    $role = $DB->get_record('role', array('shortname' => 'manager'));
    $users = get_role_users( $role->id, $context_course);
    $iseditortype = array_key_exists($USER->id, $users);
  
    if(!($iseditortype))
    {
    $role = $DB->get_record('role', array('shortname' => 'coursecreator'));
    $users = get_role_users( $role->id, $context_course);
    $iseditortype = array_key_exists($USER->id, $users);
    }

    if(!($iseditortype))
    {
    $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
    $users = get_role_users( $role->id, $context_course);
    $iseditortype = array_key_exists($USER->id, $users);
    }

    if(!($iseditortype))
    {
    $role = $DB->get_record('role', array('shortname' => 'teacher'));
    $users = get_role_users( $role->id, $context_course);
    $iseditortype = array_key_exists($USER->id, $users);
    }

    
   $coursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);

    $admins = get_admins();
   
    foreach ($admins as $admin) {
        if ($USER->id == $admin->id) {
            $iseditortype = true;
            break;
        }
    }
  
   if ($iseditortype)//user is course creator
   {
   
    echo '<a href="';
       if ($dinsysconnect->portno > 0)
       {
            $rr='view.php'. '?'.$_SERVER['QUERY_STRING'].'&MeetingAction=stop' ;
            echo htmlentities($rr) ;
            echo '">Stop Meeting</a>';
			echo '<br><br>'
.'<a href ="http://www.dinsys.com/moodlehelp.aspx?moodleversion=ver2&connectversion=ver1" >Help about Dinsys Connect</a>'; 
            echo '<br><br>';
       }
       else
       {
        $rr='view.php'. '?'.$_SERVER['QUERY_STRING'].'&MeetingAction=start';
            echo htmlentities($rr) ;
            echo '">Start Meeting</a>';
			echo '<br><br>'
.'<a href ="http://www.dinsys.com/moodlehelp.aspx?moodleversion=ver2&connectversion=ver1" >Help about Dinsys Connect</a>'; 
            echo '<br><br>';
       }
       echo "</p>"; 
    
   }
   else
       {
            /*** Do nothing ***/
       }

echo $OUTPUT->footer();

function dinsysconnect_update($dinsysconnect) {
 global $DB;
    $dinsysconnect->timemodified = time();
 
    $DB->update_record('dinsysconnect', $dinsysconnect);
   
        
    return true;
}

