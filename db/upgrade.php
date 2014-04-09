<?php
/**
 * Handles upgrading instances of this block.
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_notifications_upgrade($oldversion, $block) {
    global $DB, $CFG;

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

    return true;
}
