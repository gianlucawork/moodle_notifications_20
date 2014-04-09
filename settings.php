<?php
/////////////////////////////////////////////////////
// GLOBAL SETTINGS
////////////////////////////////////////////////////
include_once realpath( dirname( __FILE__ ).DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR."common.php";
include_once LIB_DIR."AbstractSMS.php";
if(file_exists(LIB_DIR."SMS.php")) {
	include_once LIB_DIR."SMS.php";
}

defined( 'MOODLE_INTERNAL' ) || die;
global $CFG;

if ( $ADMIN->fulltree ) {

	$settings->add( new admin_setting_heading('block_notifications_settings', '', get_string('global_configuration_comment', 'block_notifications')) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_email_channel', get_string('email', 'block_notifications'), '', 1) );

	if( class_exists('SMS') ) {
		$settings->add( new admin_setting_configcheckbox('block_notifications_sms_channel', get_string('sms', 'block_notifications'), '', 1) );
	} else {
		$settings->add( new admin_setting_configcheckbox('block_notifications_sms_channel', get_string('sms', 'block_notifications'),  get_string('sms_class_not_implemented', 'block_notifications'), 0) );
	}

	$settings->add( new admin_setting_configcheckbox('block_notifications_rss_channel', get_string('rss', 'block_notifications'), '', 1) );
	
	$settings->add( new admin_setting_configcheckbox('block_notifications_rss_shortname_url_param', get_string('rss_by_shortname', 'block_notifications'), '', 0) );

	$options = array();
	for( $i=1; $i<25; ++$i ) {
		$options[$i] = $i;
	}

	$default = 12;
	if( isset($CFG->block_notifications_frequency) ) {
		$default = $CFG->block_notifications_frequency;
	}

    $settings->add( new admin_setting_configselect('block_notifications_frequency',
													get_string('notification_frequency', 'block_notifications'),
													get_string('notification_frequency_comment', 'block_notifications'), $default , $options) );

	$settings->add( new admin_setting_heading('block_notifications_presets', '', get_string('global_configuration_presets_comment', 'block_notifications')) );

	$settings->add( new admin_setting_configcheckbox('block_notifications_email_notification_preset', get_string('email_notification_preset', 'block_notifications'), get_string('email_notification_preset_explanation', 'block_notifications'), 1) );

	$settings->add( new admin_setting_configcheckbox('block_notifications_sms_notification_preset', get_string('sms_notification_preset', 'block_notifications'), get_string('sms_notification_preset_explanation', 'block_notifications'), 1) );

	$settings->add( new admin_setting_heading('block_notifications_actions', '', get_string('global_actions_explanation', 'block_notifications')) );

	$settings->add( new admin_setting_configcheckbox('block_notifications_action_added', get_string('added', 'block_notifications'), get_string('action_added_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_updated', get_string('updated', 'block_notifications'), get_string('action_updated_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_edited', get_string('edited', 'block_notifications'), get_string('action_edited_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_deleted', get_string('deleted', 'block_notifications'), get_string('action_deleted_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_added_discussion', get_string('added_discussion', 'block_notifications'), get_string('action_added_discussion_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_deleted_discussion', get_string('deleted_discussion', 'block_notifications'), get_string('action_deleted_discussion_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_added_post', get_string('added_post', 'block_notifications'), get_string('action_added_post_explanation', 'block_notifications'), 0) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_updated_post', get_string('updated_post', 'block_notifications'), get_string('action_updated_post_explanation', 'block_notifications'), 0) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_deleted_post', get_string('deleted_post', 'block_notifications'), get_string('action_deleted_post_explanation', 'block_notifications'), 0) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_added_chapter', get_string('added_chapter', 'block_notifications'), get_string('action_added_chapter_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_updated_chapter', get_string('updated_chapter', 'block_notifications'), get_string('action_updated_chapter_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_added_entry', get_string('added_entry', 'block_notifications'), get_string('action_added_entry_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_updated_entry', get_string('updated_entry', 'block_notifications'), get_string('action_updated_entry_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_deleted_entry', get_string('deleted_entry', 'block_notifications'), get_string('action_deleted_entry_explanation', 'block_notifications'), 1) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_added_fields', get_string('added_fields', 'block_notifications'), get_string('action_added_fields_explanation', 'block_notifications'), 0) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_updated_fields', get_string('updated_fields', 'block_notifications'), get_string('action_updated_fields_explanation', 'block_notifications'), 0) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_deleted_fields', get_string('deleted_fields', 'block_notifications'), get_string('action_deleted_fields_explanation', 'block_notifications'), 0) );
	$settings->add( new admin_setting_configcheckbox('block_notifications_action_edited_questions', get_string('edited_questions', 'block_notifications'), get_string('action_edited_questions_explanation', 'block_notifications'), 1) );
}

