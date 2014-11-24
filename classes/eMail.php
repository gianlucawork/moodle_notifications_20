<?php
namespace block_notifications;

use report_eventlist_list_generator;

//***************************************************
// Mail notification
//***************************************************
class eMail {

	function notify( $changelist, $user, $course ){
		$admin = current(get_admins());
		$html_message = $this->html_mail( $changelist, $course );
		$text_message = $this->text_mail( $changelist, $course );
		$subject = get_string('mailsubject', 'block_notifications');
		$subject.= ": ".format_string( $course->fullname, true );
		email_to_user( $user, $admin, $subject, $text_message, $html_message );
	}


	function html_mail( $changelist, $course ) {
		global $CFG;

		$mailbody = '<head>';

		$mailbody .= '</head>';
		$mailbody .= '<body id="email">';
		$mailbody .= '<div class="header">';
		$mailbody .= get_string('mailsubject', 'block_notifications').' ';
		$mailbody .= "&laquo; <a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</a> &raquo; ";
		$mailbody .= '</div>';
		$mailbody .= '<div class="content">';
		$mailbody .= '<ul>';

		$events = report_eventlist_list_generator::get_all_events_list();

		foreach ( $changelist as $item ) {
			$mailbody .='<li>';
			$mailbody .= preg_replace('/\\\.*$/', '', $events[$item->event]['raweventname']) . ' on ' . date("D M j G:i:s T Y", $item->time_created);
			if(preg_match('/deleted/', $item->event)) {
				$mailbody .= " $item->name";
			} else {
				$mailbody .=": <a href=\"".$this->extract_url($item)."\">$item->name</a>";
			}
			$mailbody .= '</li>';
		}

		$mailbody .= '</ul>';
		$mailbody .= '</div>';
		$mailbody .= '</body>';

		return $mailbody;
	}

	function text_mail( $changelist, $course ) {
		global $CFG;

		$mailbody = get_string( 'mailsubject', 'block_notifications' ).': '.$course->fullname.' ';
		$mailbody .= $CFG->wwwroot.'/course/view.php?id='.$course->id."\r\n\r\n";

		$events = report_eventlist_list_generator::get_all_events_list();

		foreach ( $changelist as $item ) {
			$mailbody .= preg_replace('/\\\.*$/', '', $events[$item->event]['raweventname']) . ' on ' . date("D M j G:i:s T Y", $item->time_created);
			$mailbody .= ": $item->name";
			if(preg_match('/deleted/', $item->event)) {
				$mailbody .="\r\n\r\n";
			} else {
				$mailbody .="\r\n".$this->extract_url($item)."\r\n\r\n";
			}
		}
		return $mailbody;
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
}
?>
