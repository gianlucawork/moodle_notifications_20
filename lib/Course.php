<?php
//***************************************************
// Course registration management
//***************************************************
class Course {

	function register( $course_id, $starting_time ) {
		global $DB;
		global $CFG;

		$course=new Object();
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

		if( isset($CFG->block_notifications_frequency) ) {
			$course->notification_frequency = $CFG->block_notifications_frequency * 3600;
		} else {
			$course->notification_frequency = 12 * 3600;
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

		$course=new Object();
		$course->id = $this->get_registration_id( $course_id );
		$course->course_id = $course_id;
		$course->last_notification_time = $last_notification_time;

		return $DB->update_record( 'block_notifications_courses', $course );
	}

	function update_course_notification_settings( $course_id, $settings ) {
		global $DB;

		$course=new Object();
		$course->id = $this->get_registration_id( $course_id );
		$course->course_id = $course_id;

		$course->notify_by_email = 0;
		if( isset($settings->notify_by_email) and $settings->notify_by_email == 1 ) { $course->notify_by_email = 1; }

		$course->notify_by_sms = 0;
		if( isset($settings->notify_by_sms) and $settings->notify_by_sms == 1 ) { $course->notify_by_sms = 1; }

		$course->notify_by_rss = 0;
		if( isset($settings->notify_by_rss) and $settings->notify_by_rss == 1 ) { $course->notify_by_rss = 1; }

		$course->rss_shortname_url_param = 0;
		if( isset($settings->rss_shortname_url_param) and $settings->rss_shortname_url_param == 1 ) { $course->rss_shortname_url_param = 1; }

		if( isset($settings->notification_frequency) ) {
			$course->notification_frequency = $settings->notification_frequency % 25 * 3600;
		}

		$course->email_notification_preset = 0;
		if( isset($settings->email_notification_preset) and $settings->email_notification_preset == 1 ) { $course->email_notification_preset = 1; }

		$course->sms_notification_preset = 0;
		if( isset($settings->sms_notification_preset) and $settings->sms_notification_preset == 1 ) { $course->sms_notification_preset = 1; }

		$course->action_added = 0;
		if( isset($settings->action_added) and $settings->action_added == 1 ) { $course->action_added = 1; }

		$course->action_updated = 0;
		if( isset($settings->action_updated) and $settings->action_updated == 1 ) { $course->action_updated = 1; }

		$course->action_edited = 0;
		if( isset($settings->action_edited) and $settings->action_edited == 1 ) { $course->action_edited = 1; }

		$course->action_deleted = 0;
		if( isset($settings->action_deleted) and $settings->action_deleted == 1 ) { $course->action_deleted = 1; }

		$course->action_added_discussion = 0;
		if( isset($settings->action_added_discussion) and $settings->action_added_discussion == 1 ) { $course->action_added_discussion = 1; }

		$course->action_deleted_discussion = 0;
		if( isset($settings->action_deleted_discussion) and $settings->action_deleted_discussion == 1 ) { $course->action_deleted_discussion = 1; }

		$course->action_added_post = 0;
		if( isset($settings->action_added_post) and $settings->action_added_post == 1 ) { $course->action_added_post = 1; }

		$course->action_updated_post = 0;
		if( isset($settings->action_updated_post) and $settings->action_updated_post == 1 ) { $course->action_updated_post = 1; }

		$course->action_deleted_post = 0;
		if( isset($settings->action_deleted_post) and $settings->action_deleted_post == 1 ) { $course->action_deleted_post = 1; }

		$course->action_added_chapter = 0;
		if( isset($settings->action_added_chapter) and $settings->action_added_chapter == 1 ) { $course->action_added_chapter = 1; }

		$course->action_updated_chapter = 0;
		if( isset($settings->action_updated_chapter) and $settings->action_updated_chapter == 1 ) { $course->action_updated_chapter = 1; }

		$course->action_added_entry = 0;
		if( isset($settings->action_added_entry) and $settings->action_added_entry == 1 ) { $course->action_added_entry = 1; }

		$course->action_updated_entry = 0;
		if( isset($settings->action_updated_entry) and $settings->action_updated_entry == 1 ) { $course->action_updated_entry = 1; }

		$course->action_deleted_entry = 0;
		if( isset($settings->action_deleted_entry) and $settings->action_deleted_entry == 1 ) { $course->action_deleted_entry = 1; }

		$course->action_added_fields = 0;
		if( isset($settings->action_added_fields) and $settings->action_added_fields == 1 ) { $course->action_added_fields = 1; }

		$course->action_updated_fields = 0;
		if( isset($settings->action_updated_fields) and $settings->action_updated_fields == 1 ) { $course->action_updated_fields = 1; }

		$course->action_deleted_fields = 0;
		if( isset($settings->action_deleted_fields) and $settings->action_deleted_fields == 1 ) { $course->action_deleted_fields = 1; }

		$course->action_edited_questions = 0;
		if( isset($settings->action_edited_questions) and $settings->action_edited_questions == 1 ) { $course->action_edited_questions = 1; }

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

	function get_updated_and_deleted_modules( $course_id ){
		global $DB, $CFG;
		$course_registration = $this->get_registration( $course_id );
		//$last_notification_time = $this->get_last_notification_time( $course_id );
		$last_notification_time = $course_registration->last_notification_time;
		$actions = "";
		if($CFG->block_notifications_action_added == 1 and $course_registration->action_added == 1) {
			$actions.= "'add',";
		}
		if($CFG->block_notifications_action_updated == 1 and $course_registration->action_updated == 1) {
			$actions.= "'update',";
		}
		if($CFG->block_notifications_action_deleted == 1 and $course_registration->action_deleted == 1) {
			$actions.= "'delete mod',";
		}
		if($CFG->block_notifications_action_added_chapter == 1 and $course_registration->action_added_chapter == 1) {
			$actions.= "'add chapter',";
		}
		if($CFG->block_notifications_action_updated_chapter == 1 and $course_registration->action_updated_chapter == 1) {
			$actions.= "'update chapter',";
		}
		if($CFG->block_notifications_action_edited == 1 and $course_registration->action_edited == 1) {
			$actions.= "'edit',";
		}
		if($CFG->block_notifications_action_added_discussion == 1 and $course_registration->action_added_discussion == 1) {
			$actions.= "'add discussion',";
		}
		if($CFG->block_notifications_action_deleted_discussion == 1 and $course_registration->action_deleted_discussion == 1) {
			$actions.= "'delete discussion',";
		}
		if($CFG->block_notifications_action_added_post == 1 and $course_registration->action_added_post == 1) {
			$actions.= "'add post',";
		}
		if($CFG->block_notifications_action_updated_post == 1 and $course_registration->action_updated_post == 1) {
			$actions.= "'update post',";
		}
		if($CFG->block_notifications_action_deleted_post == 1 and $course_registration->action_deleted_post == 1) {
			$actions.= "'delete post',";
		}
		if($CFG->block_notifications_action_added_entry == 1 and $course_registration->action_added_entry == 1) {
			$actions.= "'add entry',";
		}
		if($CFG->block_notifications_action_updated_entry == 1 and $course_registration->action_updated_entry == 1) {
			$actions.= "'update entry',";
		}
		if($CFG->block_notifications_action_deleted_entry == 1 and $course_registration->action_deleted_entry == 1) {
			$actions.= "'delete entry',";
		}
		if($CFG->block_notifications_action_added_fields == 1 and $course_registration->action_added_fields == 1) {
			$actions.= "'fields add',";
		}
		if($CFG->block_notifications_action_updated_fields == 1 and $course_registration->action_updated_fields == 1) {
			$actions.= "'fields update',";
		}
		if($CFG->block_notifications_action_deleted_fields == 1 and $course_registration->action_deleted_fields == 1) {
			$actions.= "'fields delete',";
		}
		if($CFG->block_notifications_action_edited_questions == 1 and $course_registration->action_edited_questions == 1) {
			$actions.= "'editquestions'";
		}
		// remove the last comma
		if(empty($actions)) {
			return false;
		} else {
			$actions = rtrim($actions, ',');
			return $DB->get_records_select( 'log', "course=$course_id and action in ($actions) and module != 'calendar' and time > $last_notification_time group by cmid,action,url", null, 'cmid,action' );
		}
	}



	function update_log( $course ){
		global $DB;

		$modinfo =& get_fast_modinfo($course);
		foreach($modinfo->cms as $cms => $module) {
			// skip labels, invisible modules and logged modules

			if(
				$module->modname == 'label' or
				$module->visible == 0 or
				( $module->available != 1 and $module->showavailability == 0 ) or
				$this->is_module_logged($course->id, $module->id, $module->modname)
			) {
				continue;
			}

			$new_record = new Object();
			$new_record->course_id = $course->id;
			$new_record->module_id = $module->id;
			$new_record->name = $module->name;
			$new_record->type = $module->modname;
			$new_record->action = 'added';
			$new_record->status = 'pending';

			$DB->insert_record( 'block_notifications_log', $new_record );
		}
		// update records
		$course_updates = $this->get_updated_and_deleted_modules( $course->id );

		// if no course updates available then return
		if( empty($course_updates) ) return;

		foreach($course_updates as $course_update) {
			// if $log_row is empty than this module has not been registered
			// it is probably invisible
			$log_row = $this->get_log_entry($course_update->cmid);
			if ( empty($log_row) ) {
				continue;
			} else {
				$new_record = new Object();
				$new_record->course_id = $log_row->course_id;
				$new_record->module_id = $log_row->module_id;
				$new_record->url = $course_update->url;
				$new_record->type = $log_row->type;
				$new_record->status = 'pending';
				// set the name
				if(empty($modinfo->cms[$log_row->module_id])) {
					$new_record->name = $log_row->name;
				} else {
					$new_record->name = $modinfo->cms[$log_row->module_id]->name;
				}
				switch($course_update->action) {
					case 'add':
						$new_record->action = 'added';
					break;

					case 'update':
						$new_record->action = 'updated';
					break;

					case 'edit':
						$new_record->action = 'edited';
					break;

					case 'delete mod':
						$new_record->action = 'deleted';
					break;

					case 'add discussion':
						$new_record->action = 'added_discussion';
					break;

					case 'delete discussion':
						$new_record->action = 'deleted_discussion';
					break;

					case 'add post':
						$new_record->action = 'added_post';
					break;

					case 'update post':
						$new_record->action = 'updated_post';
					break;

					case 'delete post':
						$new_record->action = 'deleted_post';
					break;

					case 'add chapter':
						$new_record->action = 'added_chapter';
					break;

					case 'update chapter':
						$new_record->action = 'updated_chapter';
					break;

					case 'add entry':
						$new_record->action = 'added_entry';
					break;

					case 'update entry':
						$new_record->action = 'updated_entry';
					break;

					case 'delete entry':
						$new_record->action = 'deleted_entry';
					break;

					case 'fields add':
						$new_record->action = 'added_fields';
						$new_record->url = null; // the url does not work in this activity
					break;

					case 'fields update':
						$new_record->action = 'updated_fields';
						$new_record->url = null; // the url does not work in this activity
					break;

					case 'fields delete':
						$new_record->action = 'deleted_fields';
					break;

					case 'editquestions':
						$new_record->action = 'edited_questions';
					break;
				}

				$DB->insert_record( 'block_notifications_log', $new_record );
			}
		}

	}

	function initialize_log( $course ){
		global $DB;

		$modinfo = get_fast_modinfo( $course );
		// drop all previous records
		$DB->delete_records( 'block_notifications_log', array('course_id'=>$course->id)  );
		// add new records
		foreach( $modinfo->cms as $cms => $module ) {
			// filter labels and invisible modules
			if(
				$module->modname == 'label' or
				$module->visible == 0 or
				( $module->available != 1 and $module->showavailability == 0 )
			) { continue; }

			$new_record = new Object();
			$new_record->course_id = $course->id;
			$new_record->module_id = $module->id;
			$new_record->name = $module->name;
			$new_record->type = $module->modname;
			$new_record->action = 'added';
			$new_record->status = 'notified';

			$DB->insert_record( 'block_notifications_log', $new_record );
		}
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
		//print_r($recent_activities);
		// clear all pending notifications
		if(!empty($recent_activities))
			$DB->execute( "update {$CFG->prefix}block_notifications_log set status = 'notified'
								where
									course_id = $course_id and status='pending'
									and id in ( select id from $subtable where course_id = $course_id and (visible = 1 or visible is null) )" );
		return $recent_activities;
	}

	function get_course_info( $course_id ) {
		global $CFG, $DB;

		return current( $DB->get_records_sql("select fullname, summary from {$CFG->prefix}course where id = $course_id") );
	}

	// purge entries of courses that have been deleted
	function collect_garbage(){
		global $CFG, $DB;

		$complete_course_list = "(select id from {$CFG->prefix}course)";
		// remove entries of courses that have been deleted
		$DB->execute( "delete from {$CFG->prefix}block_notifications_log where course_id not in $complete_course_list" );
		$DB->execute( "delete from {$CFG->prefix}block_notifications_courses where course_id not in $complete_course_list" );
	}

}
?>
