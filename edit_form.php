<?php
/////////////////////////////////////////////////////
// COURSE SETTINGS
////////////////////////////////////////////////////
class block_notifications_edit_form extends block_edit_form {
	protected function specific_definition( $mform ) {
		global $CFG;
		global $COURSE;

		$global_config = get_config('block_notifications');

		$Course = new \block_notifications\Course();
		$course_notification_setting = $Course->get_registration( $COURSE->id );
		// Fields for editing HTML block title and contents.
		$mform->addElement( 'header', 'notificationsheader', get_string( 'blocksettings', 'block' ) );

		$attributes = array();
		$attributes['disabled'] = 'disabled';
		$attributes['group'] = 'notifications_settings';

		if( $global_config->email_channel == 1 ) {
			$mform->addElement( 'checkbox', 'notify_by_email', get_string('notify_by_email', 'block_notifications') );
		} else {
			$mform->addElement( 'advcheckbox', 'notify_by_email', get_string('notify_by_email', 'block_notifications'), null, $attributes );
		}

		if ( isset($course_notification_setting->notify_by_email) and $course_notification_setting->notify_by_email == 1 ) {
			$mform->setDefault( 'notify_by_email', 1 );
		}

		if( $global_config->sms_channel == 1 and class_exists('block_notifications\SMS') ) {
			$mform->addElement( 'checkbox', 'notify_by_sms', get_string('notify_by_sms', 'block_notifications') );
		} else {
			$mform->addElement( 'advcheckbox', 'notify_by_sms', get_string('notify_by_sms', 'block_notifications'), null, $attributes );
		}

		if ( isset($course_notification_setting->notify_by_sms) and $course_notification_setting->notify_by_sms == 1 ) {
			$mform->setDefault( 'notify_by_sms', 1 );
		}

		if( $global_config->rss_channel == 1 ) {
			$mform->addElement( 'checkbox', 'notify_by_rss', get_string('notify_by_rss', 'block_notifications') );
			$mform->addElement( 'checkbox', 'rss_shortname_url_param', get_string('rss_by_shortname', 'block_notifications') );
		} else {
			$mform->addElement( 'advcheckbox', 'notify_by_rss', get_string('notify_by_rss', 'block_notifications'), null, $attributes );
			$mform->addElement( 'advcheckbox', 'rss_shortname_url_param', get_string('rss_by_shortname', 'block_notifications'), null, $attributes );
		}

		if ( isset($course_notification_setting->notify_by_rss) and $course_notification_setting->notify_by_rss == 1 ) {
			$mform->setDefault( 'notify_by_rss', 1 );
		}

		if ( isset($course_notification_setting->rss_shortname_url_param) and $course_notification_setting->rss_shortname_url_param == 1 ) {
			$mform->setDefault( 'rss_shortname_url_param', 1 );
		}

		$mform->addElement( 'html', '<div class="qheader" style="margin-top: 20px">'.get_string('course_configuration_presets_comment', 'block_notifications').'</div>' );

		$mform->addElement( 'checkbox', 'email_notification_preset', get_string('email_notification_preset', 'block_notifications') );
		if ( isset($course_notification_setting->email_notification_preset) and $course_notification_setting->email_notification_preset == 1 ) {
			$mform->setDefault( 'email_notification_preset', 1 );
		} else {
			$mform->setDefault( 'email_notification_preset', 0 );
		}

		$mform->addElement( 'checkbox', 'sms_notification_preset', get_string('sms_notification_preset', 'block_notifications') );
		if ( isset($course_notification_setting->sms_notification_preset) and $course_notification_setting->sms_notification_preset == 1 ) {
			$mform->setDefault( 'sms_notification_preset', 1 );
		} else {
			$mform->setDefault( 'sms_notification_preset', 0 );
		}

		$mform->addElement( 'html', '<div class="qheader" style="margin-top: 20px;">'.get_string('events_explanation', 'block_notifications').'</div>' );

		$events = report_eventlist_list_generator::get_all_events_list();

		foreach(\block_notifications\SupportedEvents::getShortNames() as $block_instance_setting => $platform_event_name) {
			$global_setting = preg_replace('/\\\/', '_', $platform_event_name);
			$global_setting = preg_replace('/^_/', '', $global_setting);
			$description = preg_replace('/href="/', 'href="../report/eventlist/', $events[$platform_event_name]['fulleventname']);

			if( $global_config->$global_setting == 1 ) {
				$mform->addElement( 'checkbox', $block_instance_setting, $description );
				if ( isset($course_notification_setting->$block_instance_setting) and $course_notification_setting->$block_instance_setting == 1 ) {
					$mform->setDefault( $block_instance_setting, 1 );
				} else {
					$mform->setDefault( $block_instance_setting, 0 );
				}
			} else {
				$mform->addElement( 'checkbox', $block_instance_setting, $description, null, $attributes );
				$mform->setDefault( $block_instance_setting, 0 );
			}
		}
	}

	function set_data( $defaults ) {
		$block_config = new stdClass();
		$block_config->notify_by_email = file_get_submitted_draft_itemid( 'notify_by_email' );
		$block_config->notify_by_sms = file_get_submitted_draft_itemid( 'notify_by_sms' );
		$block_config->notify_by_rss = file_get_submitted_draft_itemid( 'notify_by_rss' );
		$block_config->rss_shortname_url_param = file_get_submitted_draft_itemid( 'rss_shortname_url_param' );
		$block_config->email_notification_preset = file_get_submitted_draft_itemid( 'email_notification_preset' );
		$block_config->sms_notification_preset = file_get_submitted_draft_itemid( 'sms_notification_preset' );
		foreach(\block_notifications\SupportedEvents::getShortNames() as $block_instance_setting => $platform_event_name) {
			$block_config->$block_instance_setting = file_get_submitted_draft_itemid( $block_instance_setting );
		}
		unset( $this->block->config->text );
		parent::set_data( $defaults );
		$this->block->config = $block_config;
	}
}

?>
