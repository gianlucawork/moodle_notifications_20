<?php
namespace block_notifications;

use stdClass;

//***************************************************
// Course registration management
//***************************************************

class Course {

	protected $logger;

	function __construct() {
		$this->logger = get_log_manager()->get_readers()['logstore_standard'];
	}

	function dump($var) {
		print_r("\n<pre>");
		if(is_array($var)) {
			print_r("\n<br />******************************************************");
			print_r("\n<br />* ARRAY");
			print_r("\n<br />******************************************************");
			print_r("\n<br />");
			foreach($var as $key => $content) {
				print_r("\n<br />----------------<br />\n");
				print_r("\nKEY: ". $key);
				print_r("\n<br />----------------<br />\n");
				print_r("\nCONTENT:");
				print_r("\n<br />================================================<br />\n");
				print_r($content);
				print_r("\n<br />================================================<br />\n");
			}
		} else {
			print_r("\n<br />******************************************************");
			print_r("\n<br />* OBJECT");
			print_r("\n<br />******************************************************");
			print_r("\n<br />================================================<br />\n");
			print_r($var);
			print_r("\n<br />================================================<br />\n");
		}
		print_r("\n</pre>");
	}

	function register( $course_id, $starting_time ) {
		global $DB;
		global $CFG;

		$course=new stdClass();
		$course->course_id = $course_id;
		$course->last_notification_time = $starting_time;

		if( isset($CFG->block_notifications_email_channel) ) {
			$course->notify_by_email = $CFG->block_notifications_email_channel;
		} else {
			$course->notify_by_email = 0;
		}

		if( isset($CFG->block_notifications_sms_channel) ) {
			$course->notify_by_sms = $CFG->block_notifications_sms_channel;
		} else {
			$course->notify_by_sms = 0;
		}

		if( isset($CFG->block_notifications_rss_channel) ) {
			$course->notify_by_rss = $CFG->block_notifications_rss_channel;
		} else {
			$course->notify_by_rss = 0;
		}

		$course->rss_shortname_url_param = 0;
		if( isset($CFG->block_notifications_rss_shortname_url_param) ) {
			$course->rss_shortname_url_param = $CFG->block_notifications_rss_shortname_url_param;
		} else {
			$course->rss_shortname_url_param = 0;
		}

		if ( isset($CFG->block_notifications_email_notification_preset) ) {
			$course->email_notification_preset = $CFG->block_notifications_email_notification_preset;
		} else {
			$course->email_notification_preset = 1;
		}

		if ( isset($CFG->block_notifications_sms_notification_preset) ) {
			$course->sms_notification_preset = $CFG->block_notifications_sms_notification_preset;
		} else {
			$course->sms_notification_preset = 1;
		}

		return $DB->insert_record( 'block_notifications_courses', $course );
	}

	function update_last_notification_time( $course_id, $last_notification_time ) {
		global $DB;

		$course = new stdClass();
		$course->id = $this->get_registration_id( $course_id );
		$course->course_id = $course_id;
		$course->last_notification_time = $last_notification_time;

		return $DB->update_record( 'block_notifications_courses', $course );
	}

	function update_course_notification_settings( $course_id, $settings ) {
		global $DB;

		$course = $settings;
		$course->id = $this->get_registration_id( $course_id );
		$course->course_id = $course_id;
		return $DB->update_record('block_notifications_courses', $course);
	}

	function is_registered( $course_id ) {
		$course_registration = $this->get_registration_id( $course_id );
		if( !is_null($course_registration) ) {
			return true;
		} else {
			return false;
		}
	}

	function get_registration_id( $course_id ){
		$course_registration = $this->get_registration($course_id);
		if( is_null($course_registration) ) {
			return null;
		} else {
			return $course_registration->id;
		}
	}

	function get_registration( $course_id ){
		global $DB;

		$course_registration = $DB->get_records_select( 'block_notifications_courses', "course_id=$course_id" );
		if( isset($course_registration) and is_array($course_registration) and !empty($course_registration)  ) {
			return current($course_registration);
		} else {
			return null;
		}
	}

	function get_last_notification_time( $course_id ) {
		global $DB;

		$course_registration = $DB->get_records_select( 'block_notifications_courses', "course_id=$course_id" );
		if( isset($course_registration) and is_array($course_registration)  and !empty($course_registration) ) {
			return current($course_registration)->last_notification_time;
		} else {
			return null;
		}
	}

	function uses_notifications_block( $course_id ) {
		global $DB, $CFG;

		$id = $DB->get_records_sql( "select instanceid from {$CFG->prefix}context where id in (select parentcontextid from {$CFG->prefix}block_instances where blockname = 'notifications') and instanceid = $course_id" );
		if( empty($id) ) {
			return false;
		} else {
			return true;
		}
	}


	function get_all_courses_using_notifications_block() {
		global $DB, $CFG;

		// join block_instances, context and course and extract all courses
		// that are using notifications block
		return $DB->get_records_sql( " select * from {$CFG->prefix}course where id in
											( select instanceid from {$CFG->prefix}context where id in
												( select parentcontextid from {$CFG->prefix}block_instances where blockname = 'notifications' ) );" );
	}

	// extract the logs for the course whose id is $course_id.
	// the last_notification_time can be an integer or null, if it is null then the block_notification_courses table value is used.
	function extract_standard_logs( $course_id, $last_notification_time ){
		global $DB, $CFG;
		$course_registration = $this->get_registration( $course_id );
		$global_config = get_config('block_notifications');
		// use the block_notification_courses table value if the passed time is null

		if(is_null($last_notification_time)) {
			$last_notification_time = $course_registration->last_notification_time;
		}
		$events = '';
		//$standard_names = SupportedEvents::getStandardNames();
		foreach(SupportedEvents::getShortNames() as $block_instance_setting => $platform_event_name) {
			$eventname = preg_replace('/\\\/', '_', $platform_event_name);
			$eventname = preg_replace('/^_/', '', $eventname);

			if($global_config->$eventname == 1 and $course_registration->$block_instance_setting == 1) {
				$events .= "'".addslashes($platform_event_name)."',";
			}
		}
		// remove the last comma
		if(empty($events)) {
			return false;
		} else {
			$events = rtrim($events, ',');
			$logs = $this->logger->get_events_select("courseid = $course_id and eventname in ($events) and timecreated > $last_notification_time", array(), '', 0, 0);
			return $logs;
		}
	}

	function update_log( $course_id ){
		$registration = $this->get_registration($course_id);
		$this->populate_log($registration->course_id, $registration->last_notification_time, 0);
	}

	function initialize_log( $course_id ){
		$this->populate_log($course_id, time(), 1);
	}

	function populate_log( $course, $time, $status ){
		global $DB;
		// extract log
		$modinfo = get_fast_modinfo($course);
		$logs = $this->extract_standard_logs($course, $time);
		if(empty($logs)) {
			return; // no logs have to be updated
		}
        //We will use this to make sure the course module exists
        $course_modules_arr = $modinfo->get_cms();
		// add new records
		foreach( $logs as $log) {
			// ignore admin activities
			if($this->is_admin($log->get_data()['userid'])) {
				continue;
			}
			/* print_r("\n\nlog::::::::::::::::\n\n"); */
			/* print_r($log); */
			// filter invisible modules
			$skip_module = false;
			$new_record = new stdClass();
			switch($log->get_data()['eventname']) {
				case '\core\event\calendar_event_created':
					$new_record->module = $log->get_data()['target'];
					$new_record->name = $log->get_data()['other']['name'];
				break;

				case '\core\event\calendar_event_updated':
					// check if the previous calendar_event data has been updated
					$new_record->module = $log->get_data()['target'];
					$new_record->name = $log->get_data()['other']['name'];
				break;

				case '\core\event\calendar_event_deleted':
					$new_record->module = $log->get_data()['target'];
					$new_record->name = $log->get_data()['other']['name'];
				break;

				case '\core\event\course_module_deleted':
					$new_record->name = '';
					$new_record->module = $log->get_data()['other']['modulename'];
				break;

				default:
					// try to get the module from the course
					try {
						$module = null;
						if( empty($modinfo->cms[$log->get_data()['contextinstanceid']]) ) {
							continue;
						} else {
                             $coursemoduleid = $log->get_data()['contextinstanceid'];
                             // Check to see if the coursemoduleid exists b/c if it does not exist, 
                             // $modinfo->get_cm() throws a moodle_exception
                             // And procesessing stops:
                             //   Fatal error: Class 'block_notifications\moodle_exception' not found
                             if (!array_key_exists($coursemoduleid, $course_modules_arr)) {
                                 $message = "Failed to find coursemoduleid={$coursemoduleid} in the course coursemodule info";
                                 //We are namespaced in this module, so we must put a backslash before moodle_exception
                                 throw new \moodle_exception('invalidcoursemodule', 'error');
                             }

                             $module = $modinfo->get_cm($coursemoduleid);
						}
						// check if the module is visible.
						// avoid logging invisible modules.
						if(
							$module->visible == 0 or
							( $module->available != 1 and $module->showavailability == 0 )
						) { $skip_module = true; }
						$new_record->module = $module->modname;
						$new_record->name = $module->name;
					} catch (Exception $e) {
						if($e->errorcode == 'invalidcoursemodule') {
							// check if the module info can be extracted from the stardard log
							if(isset($log->get_data()['other']['modulename'])) {
								$new_record->module = $log->get_data()['other']['modulename'];
							} else {
								$new_record->module = 0;
							}

							if(isset($log->get_data()['other']['name'])) {
								$new_record->name = $log->get_data()['other']['name'];
							} else {
								$new_record->name = '';
							}
						} else {
							/*
							print_r('<hr />');
							$this->dump('ERRRRRRRRRRRRRROOOOOOOOOOOOOOOOORRRRRRRRRRRRRRRRR');
							print_r('<hr />');
							foreach($modinfo->get_cms() as $cm) {
								print_r($cm->id . ' | ');
							}
							$this->dump($log);
							$this->dump($e);
							*/
							throw $e;
						}
					}
				break;
			}

			if($skip_module) { continue; }

			$new_record->course_id = $log->get_data()['courseid'];
			$new_record->event = $log->get_data()['eventname'];
			$new_record->module_id = $log->get_data()['contextinstanceid'];
			$new_record->target = $log->get_data()['target'];
			$new_record->target_id = $log->get_data()['objectid'];
			$new_record->time_created = $log->get_data()['timecreated'];
			$new_record->other = json_encode($log->get_data()['other']);
			$new_record->status = $status;

			//print_r("inserting::::::::::::::\n\n");
			//print_r($new_record);

			$DB->insert_record( 'block_notifications_log', $new_record );
		}

		$this->fill_in_deleted_modules_names($course);
	}

	function is_module_logged( $course_id, $module_id, $type ){
		global $DB;

		$log = $DB->get_records_select( 'block_notifications_log', "course_id = $course_id AND module_id = $module_id AND type = '$type'", null,'id' );
		if(empty($log)) {
			return false;
		} else {
			return true;
		}
	}

	function is_admin($userid){
		$admins = get_admins();
		$isadmin = false;
		foreach($admins as $admin) {
			if ($userid == $admin->id) {
				$isadmin = true;
				break;
			}
		}
		return $isadmin;
	}

	function log_exists( $course_id ){
		global $DB;

		$log = $DB->get_records_select('block_notifications_log', "course_id = $course_id", null,'id');
		if(empty($log)) {
			return false;
		} else {
			return true;
		}
	}

	function get_log_entry( $module_id ){
		global $DB;

		$entry = $DB->get_records_select( 'block_notifications_log', "module_id = $module_id" );
		if ( empty($entry) ) {
			return null;
		} else {
			return current( $entry );
		}
	}

	function get_logs( $course_id, $limit ){
		global $DB, $CFG;
		$entries = $DB->get_records_sql( "select * from {$CFG->prefix}block_notifications_log where course_id=$course_id order by id desc limit $limit" );
		if ( empty($entries) ) {
			return null;
		} else {
			return $entries;
		}
	}

	function get_recent_activities( $course_id ){
		global $DB, $CFG;

		//block_notifications_log table plus visible field from course_modules
		$subtable = "( select {$CFG->prefix}block_notifications_log.*, {$CFG->prefix}course_modules.visible
						from {$CFG->prefix}block_notifications_log left join {$CFG->prefix}course_modules
							on ({$CFG->prefix}block_notifications_log.module_id = {$CFG->prefix}course_modules.id) ) logs_with_visibility";
		// select all modules that are visible and whose status is pending
		$recent_activities = $DB->get_records_sql( "select * from $subtable where course_id = $course_id and status='pending' and (visible = 1 or visible is null)" );
		// clear all pending notifications
		if(!empty($recent_activities))
			$DB->execute( "update {$CFG->prefix}block_notifications_log set status = 1 
								where course_id = $course_id and status = 0
									and id in ( select id from $subtable where course_id = $course_id and (visible = 1 or visible is null) )" );
		return $recent_activities;
	}

	function get_course_info( $course_id ) {
		global $CFG, $DB;

		return current( $DB->get_records_sql("select fullname, summary from {$CFG->prefix}course where id = $course_id") );
	}

	function extract_deleted_module_name( $course_id, $module, $module_id, $target, $target_id ) {
		global $CFG, $DB;

		return current( $DB->get_records_sql("select name from {$CFG->prefix}block_notifications_log where course_id = $course_id
												 and module = '$module' and module_id = $module_id and target = '$target'
												 and target_id = $target_id and name != '' order by id desc limit 1") );
	}

	function fill_in_deleted_modules_names( $course_id ) {
		global $CFG, $DB;
		$nameless_modules = $DB->get_records_sql("select * from {$CFG->prefix}block_notifications_log where course_id = $course_id and name = ''");
		foreach($nameless_modules as $nameless_module) {
			$entry = $this->extract_deleted_module_name($course_id, $nameless_module->module, $nameless_module->module_id, $nameless_module->target, $nameless_module->target_id);
			if(isset($entry->name)) {
				$DB->execute( "update {$CFG->prefix}block_notifications_log set name = '$entry->name' where course_id = $course_id
								and module = '$nameless_module->module' and module_id = $nameless_module->module_id  
								and target = '$nameless_module->target' and target_id = $nameless_module->target_id and name = ''");
			}
		}
	}

	// purge entries of courses that have been deleted
	function collect_garbage(){
		global $CFG, $DB;
		$global_config = get_config('block_notifications');

		$complete_course_list = "(select id from {$CFG->prefix}course)";
		// remove entries of courses that have been deleted
		$DB->execute( "delete from {$CFG->prefix}block_notifications_log where course_id not in $complete_course_list" );
		$DB->execute( "delete from {$CFG->prefix}block_notifications_courses where course_id not in $complete_course_list" );

		// prune the older entries; check the global setting: history_length
		$course_ids = $DB->get_records_sql("select course_id from {$CFG->prefix}block_notifications_courses");
		foreach($course_ids as $entry) {
			$id = current($entry);
			$DB->execute("delete from {$CFG->prefix}block_notifications_log where time_created < (select min(time_created) as time_limit from (select * from {$CFG->prefix}block_notifications_log where course_id = $id and status = 1 order by id desc limit $global_config->history_length) kept_history) and course_id = $id and status = 1");
		}
	}

}
