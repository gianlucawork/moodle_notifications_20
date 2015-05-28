<?php
/**
 * Handles upgrading instances of this block.
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_notifications_upgrade($oldversion, $block) {
    global $DB, $CFG;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2014040404) {
		// change the size of the action column inside the block_notifications_log
		$DB->execute( "alter table {$CFG->prefix}block_notifications_log modify action varchar(50)");
		// add the url column
		$DB->execute( "alter table {$CFG->prefix}block_notifications_log add column url varchar(100) after type");
		// add the actions switches to the course table; let the teacher select what is notified to the user
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_added int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_updated int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_edited int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_deleted int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_added_discussion int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_deleted_discussion int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_added_post int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_updated_post int(1) not null default 0");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_deleted_post int(1) not null default 0");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_added_chapter int(1) not null default 0");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_updated_chapter int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_added_entry int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_updated_entry int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_deleted_entry int(1) not null default 1");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_added_fields int(1) not null default 0");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_updated_fields int(1) not null default 0");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_deleted_fields int(1) not null default 0");
		$DB->execute( "alter table {$CFG->prefix}block_notifications_courses add column action_edited_questions int(1) not null default 1");
    }

	if ($oldversion < 2014112100) {

        // Define index course_id (not unique) to be added to block_notifications_log.
        $logstable = new xmldb_table('block_notifications_log');
        $coursestable = new xmldb_table('block_notifications_courses');
        $index = new xmldb_index('course_id', XMLDB_INDEX_NOTUNIQUE, array('course_id'));
        $dropfield = new xmldb_field('notification_frequency');
		$otherfield = new xmldb_field('other', XMLDB_TYPE_TEXT, null, null, null, null, null, 'time_created');
        $historyfield = new xmldb_field('history_length', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '30', 'rss_shortname_url_param');


		//*************
		// Log table
		//*************
		// Conditionally launch add index course_id.
		if (!$dbman->index_exists($logstable, $index)) {
			$dbman->add_index($logstable, $index);
		}

		// Conditionally launch add field other.
		if (!$dbman->field_exists($logstable, $otherfield)) {
			$dbman->add_field($logstable, $otherfield);
		}

		//*************
		// Courses table
		//*************
		// Conditionally launch drop field notification_frequency.
		// The block is now taks oriented and not cron oriented.
		if ($dbman->field_exists($coursestable, $dropfield)) {
			$dbman->drop_field($coursestable, $dropfield);
		}

		// Conditionally launch add field history_length.
		if (!$dbman->field_exists($coursestable, $historyfield)) {
			$dbman->add_field($coursestable, $historyfield);
		}

		// Notifications savepoint reached.
		upgrade_block_savepoint(true, 2014112100, 'notifications');
	}

	if ($oldversion < 2015052800) {
        $coursestable = new xmldb_table('block_notifications_courses');
        $discussion_created_field = new xmldb_field('discussion_created', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'folder_updated');
		// Conditionally launch add field history_length.
		if (!$dbman->field_exists($coursestable, $discussion_created_field)) {
			$dbman->add_field($coursestable, $discussion_created_field);
		}

		// Notifications savepoint reached.
		upgrade_block_savepoint(true, 2015052800, 'notifications');
	}

    return true;
}

