<?php  
/**
 * Library of functions and constants for module dinsysconnect
 *
 * @author 
 * @version $Id: lib.php,v 3.0.012012/04/30 16:41:20 
 * @package mod_dinsysconnect
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/


require_once($CFG->dirroot.'/calendar/lib.php');
$dinsysconnect_CONSTANT = 7;     /// for example


function stopMeeting($ipString, $portInt,$presenterIdInt, $processIdInt)
{
       $fp = fsockopen($ipString, $portInt, $errno, $errstr, 30);
    if (!$fp) {
        return 'ERROR'.$errstr;
    } else {
                   //msg type
        $binarydata = pack("l", 15);
        fwrite($fp, $binarydata, strlen($binarydata));
   

        $binarydata = pack("N", $presenterIdInt);
        fwrite($fp, $binarydata, strlen($binarydata));

        $binarydata = pack("N", $processIdInt);
        fwrite($fp, $binarydata, strlen($processIdInt));

        fclose($fp);
        return 'OK';
    }    
}

function relayConnection($ipString, $serverPort, $targetIP, $targetPort)
{
    $fp = fsockopen($ipString, $serverPort, $errno, $errstr, 30);
    
    if (!$fp) {
        return 'ERROR '.$errstr;
    } 
    $binarydata = pack("l", 19);
    fwrite($fp, $binarydata, strlen($binarydata));

    $binarydata = pack("N", -1);
    fwrite($fp, $binarydata, strlen($binarydata));

    $binarydata = pack("N", strlen($targetIP));
    fwrite($fp, $binarydata, strlen($binarydata));
    //sessionId data
    $binstr = pack_str($targetIP,strlen($targetIP));
    fwrite($fp, $binstr, strlen($binstr));

    $binarydata = pack("N", $targetPort);
    fwrite($fp, $binarydata, strlen($binarydata));
    
    return $fp;
}

function getMeetingPID($ipString, $serverPort, $meetingPort)
{
        $fp = relayConnection($ipString, $serverPort, $ipString, $meetingPort);
    
    if (is_a($fp,'string') && strpos($fp,'ERROR') !== false)
       {
          echo 'error in getting meeting PID ! <br>'.$fp;
         return;
       }   

       //msg type
		$binarydata = pack("l", 58);
		fwrite($fp, $binarydata, strlen($binarydata));
	   

		$binarydata = pack("N", $presenterIdInt);
		fwrite($fp, $binarydata, strlen($binarydata));
	
		$binarydata = fread($fp,4);
		$PID=unpack("N",$binarydata);
                fclose($fp);
		return $PID[1];
}

function startMeeting($ipString, $portInt,$orgIdInt,$meetinCodeString, $meetingPwdString, $presenterIdInt,$connectivityString)
{
       $fp = fsockopen($ipString, $portInt, $errno, $errstr, 30);
    if (!$fp) {
        return 'ERROR '.$errstr;
    } 
	else {
    
            //msg type
    $binarydata = pack("l", 2);
    fwrite($fp, $binarydata, strlen($binarydata));
   

    $binarydata = pack("N", $presenterIdInt);
    fwrite($fp, $binarydata, strlen($binarydata));
    
        //sessionId
    //sessionId length
    $binarydata = pack("N", strlen($meetinCodeString));
    fwrite($fp, $binarydata, strlen($binarydata));
    //sessionId data
    $binstr = pack_str($meetinCodeString,strlen($meetinCodeString));
    fwrite($fp, $binstr, strlen($binstr));


    $binarydata = pack("N", strlen($meetingPwdString));
    fwrite($fp, $binarydata, strlen($binarydata));
    //sessionId data
    $binstr = pack_str($meetingPwdString,strlen($meetingPwdString));
    fwrite($fp, $binstr, strlen($binstr));
   
    $binarydata = pack("N", $orgIdInt | 0x0c000000);
    fwrite($fp, $binarydata, strlen($binarydata));
    
     $binarydata = pack("N", $presenterIdInt);
    fwrite($fp, $binarydata, strlen($binarydata));
        
     $binarydata = pack("N", strlen($connectivityString));
    fwrite($fp, $binarydata, strlen($binarydata));
    //sessionId data
    $binstr = pack_str($connectivityString,strlen($connectivityString));
    fwrite($fp, $binstr, strlen($binstr));
   
    $binarydata = fread($fp,4);
    $meetingPort=unpack("N",$binarydata);
   
    $binarydata = fread($fp,4);
    $PID=unpack("N",$binarydata);
   
    fclose($fp);
    return $meetingPort[1].'.'.$PID[1];

    }
}

function sendSessionId($ipString, $serverPort, $portInt, $userIdInt, $sessionIdString, $clientIP)
{
       
   $fp = relayConnection($ipString, $serverPort, $ipString, $portInt);
    
    if (is_a($fp,'string') && strpos($fp,'ERROR') !== false)
       {
          echo 'error in getting meeting PID ! <br>'.$fp;
         return;
       }   
    
    //msg type
    $binarydata = pack("l", 51);
    fwrite($fp, $binarydata, strlen($binarydata));
    
    //client id
    $binarydata = pack("N", $userIdInt);
    fwrite($fp, $binarydata, strlen($binarydata));

    //sessionId
    //sessionId length
    $binarydata = pack("N", strlen($sessionIdString));
    fwrite($fp, $binarydata, strlen($binarydata));
    //sessionId data
    $binstr = pack_str($sessionIdString,strlen($sessionIdString));
    fwrite($fp, $binstr, strlen($binstr));
    
        //sessionId length
    $binarydata = pack("N", strlen($clientIP));
    fwrite($fp, $binarydata, strlen($binarydata));
    //sessionId data
    $binstr = pack_str($clientIP,strlen($clientIP));
    fwrite($fp, $binstr, strlen($binstr));
    
    
    fclose($fp);
    return 'OK';

}
function unpack_str($str, $len) {
        $tmp_arr = unpack("c".$len."chars", $str);
        $out_str = "";
        foreach($tmp_arr as $v) {
            if($v>0) {
                $out_str .= chr($v);
            }
        }
       
        return $out_str;
    }
    
function pack_str($str, $len) {       
    $out_str = "";
    for($i=0; $i<$len; $i++) {
        $out_str .= pack("c", ord(substr($str, $i, 1)));
    }
    return $out_str;
} 
    
function bin2asc ($binary)
{
  $i = 0;
  while ( strlen($binary) > 3 )
  {
    $byte[$i] = substr($binary, 0, 8);
    $byte[$i] = base_convert($byte[$i], 2, 10);
    $byte[$i] = chr($byte[$i]);
    $binary = substr($binary, 8);
    $ascii = "$ascii$byte[$i]";
  }
  return $ascii;
}
function asc2bin ($ascii)
{
  while ( strlen($ascii) > 0 )
  {
    $byte = ""; $i = 0;
    $byte = substr($ascii, 0, 1);
    while ( $byte != chr($i) ) { $i++; }
    $byte = base_convert($i, 10, 2);
    $byte = str_repeat("0", (8 - strlen($byte)) ) . $byte; # This is an endian (architexture) specific line, you may need to alter it.
    $ascii = substr($ascii, 1);
    $binary = "$binary$byte";
  }
  return $binary;
} 

function post($Url,$post_elements)
{
if (!function_exists('curl_init')){
die('CURL is not installed!');
}
$flag=false;
$elements='';
foreach ($post_elements as $name=>$value)
{
if ($flag)
$elements.='&';
$elements.="{$name}=".urlencode($value);
$flag=true;
}

$ch = curl_init();
// set URL to download
curl_setopt($ch, CURLOPT_URL, $Url);

// set referer:
//curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/");

// user agent:
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/yyyymmdd Firefox/4.0.1");

// remove header? 0 = yes, 1 = no
curl_setopt($ch, CURLOPT_HEADER, 0);

// should curl return or print the data? true = return, false = print
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// timeout in seconds
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);

//post data
curl_setopt($ch, CURLOPT_POSTFIELDS, $elements);

// download the given URL, and return output
$output = curl_exec($ch);

// close the curl resource, and free system resources
curl_close($ch);

// print output
return $output;
};
        
     

function dinsysconnect_add_instance($dinsysconnect) {
     global $DB;
     global $COURSE,$USER;
     
    $dinsysconnect->timemodified = time();
        if (! $course = $DB->get_record('course', array('id'=>$dinsysconnect->course))) {
        print_error('coursemisconf');
    }
    $courseid=    $course->id;
    $context_course = get_context_instance(CONTEXT_COURSE,$courseid);
    
    $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
    $students_course = get_role_users( null, $context_course);    # May have to add extra stuff in here #
    $returnid = $DB->insert_record("dinsysconnect", $dinsysconnect);
    $event = NULL;
    $event->name        = $dinsysconnect->name;
    $event->description = format_module_intro('dinsysconnect', $dinsysconnect, $dinsysconnect->coursemodule);
    $event->courseid    = $dinsysconnect->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'dinsysconnect';
    $event->instance    = $returnid;
    $event->eventtype   = 'dinsysconnecttime';
    $event->timestart   = $dinsysconnect->dinsysconnecttime;
    $event->timeduration = 0;
    

    calendar_event::create($event);

    return $returnid;
   // return insert_record("dinsysconnect", $dinsysconnect);
}

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function dinsysconnect_update_instance($dinsysconnect) {
 global $DB;
    $dinsysconnect->timemodified = time();
   $dinsysconnect->id = $dinsysconnect->instance;

    $DB->update_record('dinsysconnect', $dinsysconnect);
    
     $event = null;

    if ($event->id = $DB->get_field('event', 'id', array('modulename'=>'dinsysconnect', 'instance'=>$dinsysconnect->id))) {

        $event->name        = $dinsysconnect->name;
        $event->description = $dinsysconnect->intro;
        $event->timestart   = $dinsysconnect->dinsysconnecttime;

      //  $event->description = format_module_intro('dinsysconnect', $dinsysconnect, $dinsysconnect->coursemodule);
    $event->courseid    = $dinsysconnect->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'dinsysconnect';
    $event->instance    = $dinsysconnect->id;
    $event->eventtype   = 'dinsysconnecttime';
    $event->timestart   = $dinsysconnect->dinsysconnecttime;
    $event->timeduration = 0;
    
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
            
        } else {
            calendar_event::create($event);     
        }
        
    return true;
}

/**
 * Given an ID of an instance of this module, 
 * this function will permanently delete the instance 
 * and any data that depends on it. 
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function dinsysconnect_delete_instance($id) {
   global $DB;

   $result = true;

    if (! $dinsysconnect = $DB->get_record('dinsysconnect', array('id'=>$id))) {
        return false;
    }
    if (! $DB->delete_records('event', array('modulename'=>'dinsysconnect', 'instance'=>$dinsysconnect->id))) {
        $result = false;
    }
    if (! $DB->delete_records('dinsysconnect', array('id'=>$dinsysconnect->id))) {
        $result = false;
    }
    return $result;

   
 $url='https://www.dinsys.com/ss.php';

$data['name']='fdf';
$result=trim(post($url,$data));
parse_str($result);
     $stopResponse = stopMeeting($serverIP,  intval($serverPort),$USER->id,$dinsysconnect-> processid);
     // $startResponse = startMeeting($serverIP,$serverPort,$orgID/*orgID*/,$dinsysconnect->id, $dinsysconnect->pwd,$USER->id);
      if (strpos($stoptResponse,'ERROR') !== false)
       {
          echo 'Meeting cannot be stopped ! <br>'. $stopResponse;
         return;
       }     
}

/**
 * Return a small object with summary information about what a 
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function dinsysconnect_user_outline($course, $user, $mod, $dinsysconnect) {
    return $return;
}

/**
 * Print a detailed representation of what a user has done with 
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function dinsysconnect_user_complete($course, $user, $mod, $dinsysconnect) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity 
 * that has occurred in dinsysconnect activities and print it out. 
 * Return true if there was output, or false is there was none. 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function dinsysconnect_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such 
 * as sending out mail, toggling flags etc ... 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function dinsysconnect_cron () {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module, 
 * indexed by user.  It also returns a maximum allowed grade.
 * 
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $dinsysconnectid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function dinsysconnect_grades($dinsysconnectid) {
   return NULL;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of dinsysconnect. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $dinsysconnectid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function dinsysconnect_get_participants($dinsysconnectid) {
    return false;
}

/**
 * This function returns if a scale is being used by one dinsysconnect
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $dinsysconnectid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function dinsysconnect_scale_used ($dinsysconnectid,$scaleid) {
    $return = false;

    //$rec = get_record("dinsysconnect","id","$dinsysconnectid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}
   
    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other dinsysconnect functions go here.  Each of them must have a name that 
/// starts with dinsysconnect_


?>
