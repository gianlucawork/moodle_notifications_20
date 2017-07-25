<?php
namespace block_notifications\task;

use block_notifications\User;
use block_notifications\Course;
use block_notifications\eMail;
use block_notifications\SMS;

class notify extends \core\task\scheduled_task {
	public function get_name() {
		// Shown in admin screens
		return get_string('notify', 'block_notifications');
	}


	public function execute() {
		global $CFG;
				$debug = false;
				$debug && error_log(__CLASS__.'::'.__FUNCTION__.'::Started');

		echo "\n\n****** notifications :: begin ******";
		$User = new User();
		// clean deleted users data
		$User->collect_garbage();

		$Course = new Course();
		// clean deleted courses data
		$Course->collect_garbage();

		// get the list of courses that are using this block
		$courses = $Course->get_all_courses_using_notifications_block();
		
		// if no courses are using this block exit
		if( !is_array($courses) or count($courses) < 1 ) {
			$debug && error_log(__CLASS__.'::'.__FUNCTION__.'::No courses found using the notifications plugin');
			echo "\n--> None course is using notifications plugin.";
			echo "\n****** notifications :: end ******\n\n";
			return;
		}
		$debug && error_log(__CLASS__.'::'.__FUNCTION__.'::Found '.count($courses).' courses using the notifications plugin');

		$global_config = get_config('block_notifications');

		echo "\n\nNumber of courses using the block: ".count($courses);
		foreach($courses as $course) {
			// if course is not visible then skip
			if ( $course->visible == 0 ) { continue; }

			// if the course has not been registered so far then register
			echo "\n--> Processing course: $course->fullname";
			if( !$Course->is_registered($course->id) ) {
				$Course->register($course->id, time());
			}

			// get the course registration
			$course_registration = $Course->get_registration($course->id);

			// initialize user preferences and check for new enrolled users in this course
			$enrolled_users = $User->get_all_users_enrolled_in_the_course($course->id);

			if($debug) echo "\n--> Found ".count($enrolled_users)." users";
			foreach($enrolled_users as $user) {
				// check if the user has preferences
				$user_preferences = $User->get_preferences($user->id, $course->id);
				// if the user has not preferences than set the default
				if(is_null($user_preferences)) {
					$user_preferences = new \stdClass();
					$user_preferences->user_id = $user->id;
					$user_preferences->course_id = $course->id;
					$user_preferences->notify_by_email = $course_registration->email_notification_preset;
					$user_preferences->notify_by_sms = $course_registration->sms_notification_preset;
										
					$User->initialize_preferences(	$user_preferences->user_id,
											$user_preferences->course_id,
											$user_preferences->notify_by_email,
											$user_preferences->notify_by_sms );
									if($debug) echo "\n--> User {$user->id} has preferences; notify_by_email={{$user_preferences->notify_by_email}}";
				}
			}

			// if course log entry does not exist
			// or the last notification time is older than two days
			// then reinitialize course log
			if( !$Course->log_exists($course->id) or $course_registration->last_notification_time + 48*3600 < time() ) {
				if($debug) echo "\n--> The course log does not exist, so initialize it";	
				$Course->initialize_log($course->id);
			} else {
				if($debug) echo "\n--> The course log already exists";	
			}

			$Course->update_log($course->id);
			if($debug) echo "\n--> Done course->update_log()";	

			// check if the course has something new or not
			$changelist = $Course->get_recent_activities($course->id);
			if($debug) echo "\n--> Done course->get_recent_activities()";	

			// update the last notification time
			$Course->update_last_notification_time($course->id, time());

			if($debug) echo "\n--> Here is the course changelist=".print_r($changelist, true);
			if( empty($changelist) ) { continue; } // check the next course. No new items in this one.

			foreach($enrolled_users as $user) {
				// get user preferences
				$user_preferences = $User->get_preferences($user->id, $course->id);
				// if the email notification is enabled in the course
				// and if the user has set the emailing notification in preferences
				// then send a notification by email
				if( $global_config->email_channel == 1 and $course_registration->notify_by_email == 1 and $user_preferences->notify_by_email == 1 ) {
					$eMail = new eMail();
					$eMail->notify($changelist, $user, $course);
				}
				// if the sms notification is enabled in the course
				// and if the user has set the sms notification in preferences
				// and if the user has set the mobile phone number
				// then send a notification by sms
				if(
					class_exists('block_notifications\SMS') and
					$global_config->sms_channel == 1 and
					$course_registration->notify_by_sms == 1 and
					$user_preferences->notify_by_sms == 1 and
					!empty($user->phone2)
				) {
					$sms = new SMS();
					$sms->notify($changelist, $user, $course);
				}
			}
		}
		echo "\n****** notifications :: end ******\n\n";
	}
}
