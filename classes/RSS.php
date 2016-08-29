<?php
namespace block_notifications;

include_once realpath(dirname( __FILE__ ).DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."common.php";

use report_eventlist_list_generator;


class RSS {
	function __construct( $course_id ){
		global $CFG, $DB;

		$global_config = get_config('block_notifications');

		$Course = new Course();


		if( !$Course->is_registered($course_id) or !$Course->uses_notifications_block($course_id) ) {
			echo get_string('rss_not_enabled', 'block_notifications');
			return;
		}
		$User = new User();
		$teacher = $User->get_professor( $course_id );
		// if no teacher then add a dummy mail address
		if( empty($teacher) ) {
            $teacher = new \stdClass();
			$teacher->firstname = "No";
			$teacher->lastname = "Teacher";
			$teacher->email = "noteacher@inthiscourse.org";
		}

		$course_info = $Course->get_course_info( $course_id );

		$course_registration = $Course->get_registration( $course_id );

		if ( $course_registration->notify_by_rss != 1 ){
			return; // the rss is not active in the course
		}

		$module_names = \get_module_types_names();

		$now = date('r');
		$course_name = $this->standardize($course_info->fullname);
		$course_summary = $this->standardize($course_info->summary);
		$output = "<?xml version=\"1.0\"?>
					<rss version=\"2.0\">
					<channel>
					<title>$course_name</title>
					<link>$CFG->wwwroot/course/view.php?id=$course_id</link>
					<description>$course_summary</description>
					<language>en-us</language>
					<pubDate>$now</pubDate>
					<lastBuildDate>$now</lastBuildDate>
					<docs>$CFG->wwwroot/course/view.php?id=$course_id</docs>
					<managingEditor>$teacher->email ($teacher->firstname $teacher->lastname)</managingEditor>
					<webMaster>helpdesk@elearninglab.org (Helpdesk eLab)</webMaster>";


		$logs = $Course->get_logs( $course_id, $global_config->history_length );
		$events = report_eventlist_list_generator::get_all_events_list();

		if( !isset($logs) or !is_array($logs) or count($logs) == 0 ) {
			$output .= "<item>";
			$output .= '<title>'.get_string('rss_empty_title', 'block_notifications').'</title>';
			$output .= '<description>'.get_string('rss_empty_description', 'block_notifications').'</description>';
			$output .= "</item>";
		} else {
			$separator = ' - ';
			foreach( $logs as $log ) {
				$output .= "<item>";
				if(empty($module_names[$log->module])) {
					$output .= '<title>'.get_string($log->module, 'block_notifications').': ' . $this->standardize($log->name) . '</title>';
				} else {
					$output .= '<title>' . $module_names[$log->module] . ': ' . $this->standardize($log->name) . '</title>';
				}
				if ( preg_match('/deleted/', $log->event) ) {
					$output .= "<link></link>";
				} else {
					$output .= "<link>".$this->extract_url($log)."</link>";
				}

				$output .= "<description>";
				$output .= $this->standardize(preg_replace('/\\\.*$/', '', $events[$log->event]['raweventname']) . ' on ' . date("r", $log->time_created));
				$output .= "</description>";
				$output .= "</item>";
			}
		}
		$output .= "</channel></rss>";
		header("Content-Type: application/rss+xml");
		echo $output;
	}

	function extract_url($log_entry) {
		global $CFG;

		$url = "$CFG->wwwroot/mod/$log_entry->module/view.php?id=$log_entry->module_id";
		switch($log_entry->target) {
			case 'chapter':
				$url .= "&amp;chapterid=$log_entry->target_id";
			break;
			case 'calendar_event':
				$url = "$CFG->wwwroot/calendar/view.php?course=$log_entry->course_id";
			break;
		}
		return $url;
	}

	function standardize($string) {
		$output = strip_tags($string);
		$output = htmlentities($output);
		return $output;
	}
}


// check the options and initialize RSS

if( empty($_GET['id']) and empty($_GET['shortname'])) {
	die("Please specify the Course id or the Course shortname as url options.");
} else if(empty($_GET['id']) and !empty($_GET['shortname'])) {
	global $DB, $CFG;
	$course = $DB->get_record('course', array('shortname' => urldecode($_GET['shortname'])), $fields='id');
	if($course == false) {
		die("A course with this shortname does not exist. Please specify the correct shortname.");
	} else {
		$course_id = $course->id;
	}
} else {
	$course_id = intval( $_GET['id'] );
}

$rss = new RSS( $course_id );
?>
