<?php
class block_notifications_edit_form extends block_edit_form {
	protected function specific_definition( $mform ) {
		global $CFG;
		global $COURSE;

		$Course = new Course();
		$course_notification_setting = $Course->get_registration( $COURSE->id );
		// Fields for editing HTML block title and contents.
		$mform->addElement( 'header', 'configheader', get_string( 'blocksettings', 'block' ) );

		$attributes = array();
		$attributes['disabled'] = 'disabled';
		$attributes['group'] = 'notifications_settings';

		if( $CFG->block_notifications_email_channel == 1 ) {
			$mform->addElement( 'checkbox', 'notify_by_email', get_string('notify_by_email', 'block_notifications') );
		} else {
			$mform->addElement( 'advcheckbox', 'notify_by_email', get_string('notify_by_email', 'block_notifications'), null, $attributes );
		}

		if ( isset($course_notification_setting->notify_by_email) and $course_notification_setting->notify_by_email == 1 ) {
			$mform->setDefault( 'notify_by_email', 1 );
		}

		if( $CFG->block_notifications_sms_channel == 1 and class_exists('SMS') ) {
			$mform->addElement( 'checkbox', 'notify_by_sms', get_string('notify_by_sms', 'block_notifications') );
		} else {
			$mform->addElement( 'advcheckbox', 'notify_by_sms', get_string('notify_by_sms', 'block_notifications'), null, $attributes );
		}

		if ( isset($course_notification_setting->notify_by_sms) and $course_notification_setting->notify_by_sms == 1 ) {
			$mform->setDefault( 'notify_by_sms', 1 );
		}

		if( $CFG->block_notifications_rss_channel == 1 ) {
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

		if(
			$CFG->block_notifications_email_channel == 1 or
			$CFG->block_notifications_sms_channel == 1
		) {
	 		$options = array();
			for( $i=1; $i<25; ++$i ) {
				$options[$i] = $i;
			}
			$mform->addElement( 'select', 'notification_frequency', get_string('notification_frequency', 'block_notifications'), $options );
			$mform->setDefault( 'notification_frequency', $course_notification_setting->notification_frequency/3600 );
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

		$mform->addElement( 'html', '<div class="qheader" style="margin-top: 20px">'.get_string('actions_explanation', 'block_notifications').'</div>' );

		$mform->addElement( 'html',
			'<style type="text/css">
			<!--
				.block_notifications_action_name {
					font-weight: bold;
				}
			-->
			</style>'
		);

		if( $CFG->block_notifications_action_added == 1 ) {
			$mform->addElement( 'checkbox',
								'action_added',
								'<span class="block_notifications_action_name">' . get_string('added', 'block_notifications').'</span>'  .' : '. get_string('action_added_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_added',
								'<span class="block_notifications_action_name">' . get_string('added', 'block_notifications').'</span>'  .' : '. get_string('action_added_explanation', 'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_added) and $course_notification_setting->action_added == 1 ) {
			$mform->setDefault( 'action_added', 1 );
		} else {
			$mform->setDefault( 'action_added', 0 );
		}


		if( $CFG->block_notifications_action_updated == 1 ) {
			$mform->addElement( 'checkbox',
								'action_updated',
								'<span class="block_notifications_action_name">' . get_string('updated', 'block_notifications').'</span>'  .' : '. get_string('action_updated_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_updated',
								'<span class="block_notifications_action_name">' . get_string('updated', 'block_notifications').'</span>'  .' : '. get_string('action_updated_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_updated) and $course_notification_setting->action_updated == 1 ) {
			$mform->setDefault( 'action_updated', 1 );
		} else {
			$mform->setDefault( 'action_updated', 0 );
		}


		if( $CFG->block_notifications_action_edited == 1 ) {
			$mform->addElement( 'checkbox',
								'action_edited',
								'<span class="block_notifications_action_name">' . get_string('edited', 'block_notifications').'</span>'  .' : '. get_string('action_edited_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_edited',
								'<span class="block_notifications_action_name">' . get_string('edited', 'block_notifications').'</span>'  .' : '. get_string('action_edited_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_edited) and $course_notification_setting->action_edited == 1 ) {
			$mform->setDefault( 'action_edited', 1 );
		} else {
			$mform->setDefault( 'action_edited', 0 );
		}


		if( $CFG->block_notifications_action_deleted == 1 ) {
			$mform->addElement( 'checkbox',
								'action_deleted',
								'<span class="block_notifications_action_name">' . get_string('deleted', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_deleted',
								'<span class="block_notifications_action_name">' . get_string('deleted', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_deleted) and $course_notification_setting->action_deleted == 1 ) {
			$mform->setDefault( 'action_deleted', 1 );
		} else {
			$mform->setDefault( 'action_deleted', 0 );
		}

		if( $CFG->block_notifications_action_added_discussion == 1 ) {
			$mform->addElement( 'checkbox',
								'action_added_discussion',
								'<span class="block_notifications_action_name">' . get_string('added_discussion', 'block_notifications').'</span>'  .' : '. get_string('action_added_discussion_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_added_discussion',
								'<span class="block_notifications_action_name">' . get_string('added_discussion', 'block_notifications').'</span>'  .' : '. get_string('action_added_discussion_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_added_discussion) and $course_notification_setting->action_added_discussion == 1 ) {
			$mform->setDefault( 'action_added_discussion', 1 );
		} else {
			$mform->setDefault( 'action_added_discussion', 0 );
		}

		if( $CFG->block_notifications_action_deleted_discussion == 1 ) {
			$mform->addElement( 'checkbox',
								'action_deleted_discussion',
								'<span class="block_notifications_action_name">' . get_string('deleted_discussion', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_discussion_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_deleted_discussion',
								'<span class="block_notifications_action_name">' . get_string('deleted_discussion', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_discussion_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_deleted_discussion) and $course_notification_setting->action_deleted_discussion == 1 ) {
			$mform->setDefault( 'action_deleted_discussion', 1 );
		} else {
			$mform->setDefault( 'action_deleted_discussion', 0 );
		}

		if( $CFG->block_notifications_action_added_post == 1 ) {
			$mform->addElement( 'checkbox',
								'action_added_post',
								'<span class="block_notifications_action_name">' . get_string('added_post', 'block_notifications').'</span>'  .' : '. get_string('action_added_post_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_added_post',
								'<span class="block_notifications_action_name">' . get_string('added_post', 'block_notifications').'</span>'  .' : '. get_string('action_added_post_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_added_post) and $course_notification_setting->action_added_post == 1 ) {
			$mform->setDefault( 'action_added_post', 1 );
		} else {
			$mform->setDefault( 'action_added_post', 0 );
		}

		if( $CFG->block_notifications_action_updated_post == 1 ) {
			$mform->addElement( 'checkbox',
								'action_updated_post',
								'<span class="block_notifications_action_name">' . get_string('updated_post', 'block_notifications').'</span>'  .' : '. get_string('action_updated_post_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_updated_post',
								'<span class="block_notifications_action_name">' . get_string('updated_post', 'block_notifications').'</span>'  .' : '. get_string('action_updated_post_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_updated_post) and $course_notification_setting->action_updated_post == 1 ) {
			$mform->setDefault( 'action_updated_post', 1 );
		} else {
			$mform->setDefault( 'action_updated_post', 0 );
		}

		if( $CFG->block_notifications_action_deleted_post == 1 ) {
			$mform->addElement( 'checkbox',
								'action_deleted_post',
								'<span class="block_notifications_action_name">' . get_string('deleted_post', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_post_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_deleted_post',
								'<span class="block_notifications_action_name">' . get_string('deleted_post', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_post_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_deleted_post) and $course_notification_setting->action_deleted_post == 1 ) {
			$mform->setDefault( 'action_deleted_post', 1 );
		} else {
			$mform->setDefault( 'action_deleted_post', 0 );
		}

		if( $CFG->block_notifications_action_added_chapter == 1 ) {
			$mform->addElement( 'checkbox',
								'action_added_chapter',
								'<span class="block_notifications_action_name">' . get_string('added_chapter', 'block_notifications').'</span>'  .' : '. get_string('action_added_chapter_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_added_chapter',
								'<span class="block_notifications_action_name">' . get_string('added_chapter', 'block_notifications').'</span>'  .' : '. get_string('action_added_chapter_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_added_chapter) and $course_notification_setting->action_added_chapter == 1 ) {
			$mform->setDefault( 'action_added_chapter', 1 );
		} else {
			$mform->setDefault( 'action_added_chapter', 0 );
		}

		if( $CFG->block_notifications_action_updated_chapter == 1 ) {
			$mform->addElement( 'checkbox',
								'action_updated_chapter',
								'<span class="block_notifications_action_name">' . get_string('updated_chapter', 'block_notifications').'</span>'  .' : '. get_string('action_updated_chapter_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_updated_chapter',
								'<span class="block_notifications_action_name">' . get_string('updated_chapter', 'block_notifications').'</span>'  .' : '. get_string('action_updated_chapter_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_updated_chapter) and $course_notification_setting->action_updated_chapter == 1 ) {
			$mform->setDefault( 'action_updated_chapter', 1 );
		} else {
			$mform->setDefault( 'action_updated_chapter', 0 );
		}

		if( $CFG->block_notifications_action_added_entry == 1 ) {
			$mform->addElement( 'checkbox',
								'action_added_entry',
								'<span class="block_notifications_action_name">' . get_string('added_entry', 'block_notifications').'</span>'  .' : '. get_string('action_added_entry_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_added_entry',
								'<span class="block_notifications_action_name">' . get_string('added_entry', 'block_notifications').'</span>'  .' : '. get_string('action_added_entry_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_added_entry) and $course_notification_setting->action_added_entry == 1 ) {
			$mform->setDefault( 'action_added_entry', 1 );
		} else {
			$mform->setDefault( 'action_added_entry', 0 );
		}

		if( $CFG->block_notifications_action_updated_entry == 1 ) {
			$mform->addElement( 'checkbox',
								'action_updated_entry',
								'<span class="block_notifications_action_name">' . get_string('updated_entry', 'block_notifications').'</span>'  .' : '. get_string('action_updated_entry_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_updated_entry',
								'<span class="block_notifications_action_name">' . get_string('updated_entry', 'block_notifications').'</span>'  .' : '. get_string('action_updated_entry_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_updated_entry) and $course_notification_setting->action_updated_entry == 1 ) {
			$mform->setDefault( 'action_updated_entry', 1 );
		} else {
			$mform->setDefault( 'action_updated_entry', 0 );
		}

		if( $CFG->block_notifications_action_deleted_entry == 1 ) {
			$mform->addElement( 'checkbox',
								'action_deleted_entry',
								'<span class="block_notifications_action_name">' . get_string('deleted_entry', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_entry_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_deleted_entry',
								'<span class="block_notifications_action_name">' . get_string('deleted_entry', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_entry_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_deleted_entry) and $course_notification_setting->action_deleted_entry == 1 ) {
			$mform->setDefault( 'action_deleted_entry', 1 );
		} else {
			$mform->setDefault( 'action_deleted_entry', 0 );
		}

		if( $CFG->block_notifications_action_added_fields == 1 ) {
			$mform->addElement( 'checkbox',
								'action_added_fields',
								'<span class="block_notifications_action_name">' . get_string('added_fields', 'block_notifications').'</span>'  .' : '. get_string('action_added_fields_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_added_fields',
								'<span class="block_notifications_action_name">' . get_string('added_fields', 'block_notifications').'</span>'  .' : '. get_string('action_added_fields_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_added_fields) and $course_notification_setting->action_added_fields == 1 ) {
			$mform->setDefault( 'action_added_fields', 1 );
		} else {
			$mform->setDefault( 'action_added_fields', 0 );
		}

		if( $CFG->block_notifications_action_updated_fields == 1 ) {
			$mform->addElement( 'checkbox',
								'action_updated_fields',
								'<span class="block_notifications_action_name">' . get_string('updated_fields', 'block_notifications').'</span>'  .' : '. get_string('action_updated_fields_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_updated_fields',
								'<span class="block_notifications_action_name">' . get_string('updated_fields', 'block_notifications').'</span>'  .' : '. get_string('action_updated_fields_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_updated_fields) and $course_notification_setting->action_updated_fields == 1 ) {
			$mform->setDefault( 'action_updated_fields', 1 );
		} else {
			$mform->setDefault( 'action_updated_fields', 0 );
		}

		if( $CFG->block_notifications_action_deleted_fields == 1 ) {
			$mform->addElement( 'checkbox',
								'action_deleted_fields',
								'<span class="block_notifications_action_name">' . get_string('deleted_fields', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_fields_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_deleted_fields',
								'<span class="block_notifications_action_name">' . get_string('deleted_fields', 'block_notifications').'</span>'  .' : '. get_string('action_deleted_fields_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_deleted_fields) and $course_notification_setting->action_deleted_fields == 1 ) {
			$mform->setDefault( 'action_deleted_fields', 1 );
		} else {
			$mform->setDefault( 'action_deleted_fields', 0 );
		}

		if( $CFG->block_notifications_action_edited_questions == 1 ) {
			$mform->addElement( 'checkbox',
								'action_edited_questions',
								'<span class="block_notifications_action_name">' . get_string('edited_questions', 'block_notifications').'</span>'  .' : '. get_string('action_edited_questions_explanation',
								'block_notifications') );
		} else {
			$mform->addElement( 'checkbox',
								'action_edited_questions',
								'<span class="block_notifications_action_name">' . get_string('edited_questions', 'block_notifications').'</span>'  .' : '. get_string('action_edited_questions_explanation',
								'block_notifications'),
								null,
								$attributes );
		}
		if ( isset($course_notification_setting->action_edited_questions) and $course_notification_setting->action_edited_questions == 1 ) {
			$mform->setDefault( 'action_edited_questions', 1 );
		} else {
			$mform->setDefault( 'action_edited_questions', 0 );
		}
	}

	function set_data( $defaults ) {
		$block_config = new Object();
		$block_config->notify_by_email = file_get_submitted_draft_itemid( 'notify_by_email' );
		$block_config->notify_by_sms = file_get_submitted_draft_itemid( 'notify_by_sms' );
		$block_config->notify_by_rss = file_get_submitted_draft_itemid( 'notify_by_rss' );
		$block_config->rss_shortname_url_param = file_get_submitted_draft_itemid( 'rss_shortname_url_param' );
		$block_config->notification_frequency = file_get_submitted_draft_itemid( 'notification_frequency' );
		$block_config->email_notification_preset = file_get_submitted_draft_itemid( 'email_notification_preset' );
		$block_config->sms_notification_preset = file_get_submitted_draft_itemid( 'sms_notification_preset' );
		$block_config->action_added = file_get_submitted_draft_itemid( 'action_added' );
		$block_config->action_updated = file_get_submitted_draft_itemid( 'action_updated' );
		$block_config->action_edited = file_get_submitted_draft_itemid( 'action_edited' );
		$block_config->action_deleted = file_get_submitted_draft_itemid( 'action_deleted' );
		$block_config->action_added_discussion = file_get_submitted_draft_itemid( 'action_added_discussion' );
		$block_config->action_deleted_discussion = file_get_submitted_draft_itemid( 'action_deleted_discussion' );
		$block_config->action_added_post = file_get_submitted_draft_itemid( 'action_added_post' );
		$block_config->action_updated_post = file_get_submitted_draft_itemid( 'action_updated_post' );
		$block_config->action_deleted_post = file_get_submitted_draft_itemid( 'action_deleted_post' );
		$block_config->action_added_chapter = file_get_submitted_draft_itemid( 'action_added_chapter' );
		$block_config->action_updated_chapter = file_get_submitted_draft_itemid( 'action_updated_chapter' );
		$block_config->action_added_entry = file_get_submitted_draft_itemid( 'action_added_entry' );
		$block_config->action_updated_entry = file_get_submitted_draft_itemid( 'action_updated_entry' );
		$block_config->action_deleted_entry = file_get_submitted_draft_itemid( 'action_deleted_entry' );
		$block_config->action_added_fields = file_get_submitted_draft_itemid( 'action_added_fields' );
		$block_config->action_updated_fields = file_get_submitted_draft_itemid( 'action_updated_fields' );
		$block_config->action_deleted_fields = file_get_submitted_draft_itemid( 'action_deleted_fields' );
		$block_config->action_edited_questions = file_get_submitted_draft_itemid( 'action_edited_questions' );
		unset( $this->block->config->text );
		parent::set_data( $defaults );
		$this->block->config = $block_config;
	}
}

?>
