<?php
//***************************************************
// Cron Note
//***************************************************
// this block is registered as task.
// it does not implement the cron() function anymore.
// please check the classes/task/notify.php file for
// more details.
//***************************************************


include_once realpath(dirname( __FILE__ ).DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR."common.php";

use block_notifications\Course;
use block_notifications\User;

class block_notifications extends block_base {

//***************************************************
// Init
//***************************************************
	function init() {
		$this->title = get_string('pluginname', 'block_notifications');
	}

	function has_config() { return true; }

	function after_install() { }

	function before_delete() { }

	function applicable_formats() {
		return array('course-view' => true);
	}


//***************************************************
// Configurations
//***************************************************
	function specialization() {
		global $COURSE;
		$Course = new Course();
		// if the course has not been registered so far
		// then register the course and set the starting time
		// for notifications
		if( !$Course->is_registered($COURSE->id) ) {
			$Course->register($COURSE->id, time());
		}
		// intialize logs; perform this operation just once
		if( !$Course->log_exists($COURSE->id) ) {
			$Course->initialize_log($COURSE->id);
		}
	}

	function instance_allow_config() {
		return true;
	}

	function instance_config_save($data, $nolongerused = false) {
		global $COURSE;
		$Course = new Course();
		$Course->update_course_notification_settings($COURSE->id, $data);
  		return true;
	}

	function personal_settings($course_registration){
		global $CFG;
		global $COURSE;
		global $USER;
		global $PAGE;

		$global_config = get_config('block_notifications');
		// if admin user or both sms and email notifications
		// are disabled in the course then do not display user preferences
		if(
			($global_config->email_channel != 1 and $global_config->sms_channel != 1) or
			($course_registration->notify_by_email == 0 and $course_registration->notify_by_sms == 0 )
		) {
			return '';
		} else {
			$User = new User();
			$user_preferences = $User->get_preferences($USER->id, $COURSE->id);

			// intialize preferences if preferences if necessary
			if(is_null($user_preferences)) {
				$user_preferences = new stdClass();
				$user_preferences->user_id = $USER->id;
				$user_preferences->course_id = $COURSE->id;
				$user_preferences->notify_by_email = $course_registration->email_notification_preset;
				$user_preferences->notify_by_sms = $course_registration->sms_notification_preset;
				$User->initialize_preferences(	$user_preferences->user_id,
												$user_preferences->course_id,
												$user_preferences->notify_by_email,
												$user_preferences->notify_by_sms );
			}

			// prepare mail notification status
			$mail_notification_status = '';
			if( isset($user_preferences->notify_by_email) and $user_preferences->notify_by_email == 1) { $mail_notification_status = 'checked="checked"'; }

			$sms_notification_status = '';
			if( isset($user_preferences->notify_by_sms) and $user_preferences->notify_by_sms == 1) { $sms_notification_status = 'checked="checked"'; }

			//user preferences interface
			$PAGE->requires->jquery();
			$PAGE->requires->js('/blocks/notifications/js/user_preferences_interface.php');
			$up_interface ="";
			$up_interface.='<div id="notifications_config_preferences">'; // main div
			$up_interface.='<a id="notifications_user_preferences_trigger" href="#" onclick="show_user_preferences_panel()">';
			$up_interface.= get_string('user_preference_settings', 'block_notifications');
			$up_interface.= '</a>';
			$up_interface.='<div id="notifications_user_preferences" style="display:none">';// div a
			$up_interface.='<div>'; // div b
			$up_interface.= get_string('user_preference_header', 'block_notifications');
			$up_interface.='</div>'; // div b end
			$up_interface.='<form id="user_preferences" action="">';
			$up_interface.='<input type="hidden" name="user_id" value="'.$USER->id.'" />';
			$up_interface.='<input type="hidden" name="course_id" value="'.$COURSE->id.'" />';
			if ( $global_config->email_channel == 1 and $course_registration->notify_by_email == 1 ) {
				$up_interface.='<div>'; // div c
				$up_interface.="<input type='checkbox' name='notify_by_email' value='1' $mail_notification_status />";
				$up_interface.= get_string('notify_by_email', 'block_notifications');
				$up_interface.='</div>'; // div c end
			}
			if ( class_exists('block_notifications\SMS') and $global_config->sms_channel == 1 and $course_registration->notify_by_sms == 1 ) {
				$up_interface.='<div>'; // div d end
				$up_interface.="<input type='checkbox' name='notify_by_sms' value='1' $sms_notification_status />";
				$up_interface.= get_string('notify_by_sms', 'block_notifications');
				$up_interface.='</div>'; // div d end
			}
			$up_interface.='</form>';
			$up_interface.='<input type="button" name="save_user_preferences" value="'.get_string('savechanges').'" onclick="save_user_preferences()" />';
			$up_interface.='<input type="button" name="cancel" value="'.get_string('cancel').'" onclick="hide_user_preferences_panel()" />';
			$up_interface.='</div>'; // div a end
			$up_interface.='</div>'; // main div end
			return $up_interface;
		}
		/*
		*/
	}

//***************************************************
// Block content
//***************************************************
	function get_content() {
		if ($this->content !== NULL) {
			return $this->content;
		}

		global $COURSE;
		global $USER;
		global $CFG;

		$this->content   = new stdClass;
		$Course = new Course();
		$global_config = get_config('block_notifications');
		$course_registration = $Course->get_registration($COURSE->id);
		
		if (
			( $global_config->email_channel != 1 and $global_config->sms_channel != 1 and $global_config->rss_channel != 1) or
			( $course_registration->notify_by_email == 0 and $course_registration->notify_by_sms == 0 and $course_registration->notify_by_rss == 0 )
		){

			$this->content->text =  get_string('configuration_comment', 'block_notifications');

		} else {
			// last notification info
			$this->content->text = "<span style='font-size: 12px'>";
			$this->content->text.= get_string('last_notification', 'block_notifications');
			$this->content->text.= ": ".date("j M Y G:i:s",$course_registration->last_notification_time);
			$this->content->text.= "</span><br />";

			if ( $global_config->email_channel == 1 and $course_registration->notify_by_email == 1 ) {
				$this->content->text.= "<img src='$CFG->wwwroot/blocks/notifications/images/Mail-icon.png' ";
				$this->content->text.= "alt='e-mail icon' ";
				$this->content->text.= "title='".get_string('email_icon_tooltip', 'block_notifications')."' />";
				//$this->content->text.= '<br />';
			}

			if ( $global_config->sms_channel == 1 and $course_registration->notify_by_sms == 1 and class_exists('block_notifications\SMS') ) {
				if( empty($USER->phone2) ) {
					//$this->content->text.= "<a target='_blank' href='$CFG->wwwroot/help.php?module=plugin&file=../blocks/notifications/lang/en_utf8/help/prova.html'>";
					$this->content->text.= "<a target='_blank' href='$CFG->wwwroot/blocks/notifications/help.php'>";
					$this->content->text.= "<img src='$CFG->wwwroot/blocks/notifications/images/SMS-icon_warning.png' ";
					$this->content->text.= "alt='sms warning icon' ";
					$this->content->text.= "title='".get_string('sms_icon_phone_number_missing_tooltip', 'block_notifications')."' />";
					$this->content->text.= "</a>";
				} else {
					$this->content->text.= "<img src='$CFG->wwwroot/blocks/notifications/images/SMS-icon.png' ";
					$this->content->text.= "alt='sms icon' ";
					$this->content->text.= "title='".get_string('sms_icon_tooltip', 'block_notifications')."' />";
				}
				//$this->content->text.= '<br />';
			}
			if ( $global_config->rss_channel == 1 and $course_registration->notify_by_rss == 1 ) {
				if ( isset($course_registration->rss_shortname_url_param) and $course_registration->rss_shortname_url_param == 1 ) {
					$this->content->text.= "<a target='_blank' href='$CFG->wwwroot/blocks/notifications/classes/RSS.php?shortname=".urlencode($COURSE->shortname)."'>";
				} else {
					$this->content->text.= "<a target='_blank' href='$CFG->wwwroot/blocks/notifications/classes/RSS.php?id=$COURSE->id'>";
				}
				$this->content->text.= "<img src='$CFG->wwwroot/blocks/notifications/images/RSS-icon.png' ";
				$this->content->text.= "alt='rss icon' ";
				$this->content->text.= "title='".get_string('rss_icon_tooltip', 'block_notifications')."' />";
				$this->content->text.= "</a>";
			}

		}

		$this->content->text.= $this->personal_settings($course_registration);
		$this->content->footer = '';
		return $this->content;
	}
}
?>
