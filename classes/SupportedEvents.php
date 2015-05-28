<?php
namespace block_notifications;

//***************************************************
// SupportedEvents class
//***************************************************

class SupportedEvents {
	// the list is structured as key => eventname
	// the key is the field name in the block_notifications_courses table, it is the Short name
	// the eventname is the moodle event name, this is the standard name
	private static $list = array(
			'calendar_event_created' => '\core\event\calendar_event_created',
			'calendar_event_deleted' => '\core\event\calendar_event_deleted',
			'calendar_event_updated' => '\core\event\calendar_event_updated',
			'course_module_created' => '\core\event\course_module_created',
			'course_module_deleted' => '\core\event\course_module_deleted',
			'course_module_updated' => '\core\event\course_module_updated',
			'chapter_created' => '\mod_book\event\chapter_created',
			'chapter_deleted' => '\mod_book\event\chapter_deleted',
			'chapter_updated' => '\mod_book\event\chapter_updated',
			'field_created' => '\mod_data\event\field_created',
			'field_deleted' => '\mod_data\event\field_deleted',
			'field_updated' => '\mod_data\event\field_updated',
			'record_created' => '\mod_data\event\record_created',
			'record_deleted' => '\mod_data\event\record_deleted',
			'record_updated' => '\mod_data\event\record_updated',
			'template_updated' => '\mod_data\event\template_updated',
			'folder_updated' => '\mod_folder\event\folder_updated',
			'discussion_created' => '\mod_forum\event\discussion_created',
			'discussion_deleted' => '\mod_forum\event\discussion_deleted',
			'discussion_moved' => '\mod_forum\event\discussion_moved',
			'discussion_updated' => '\mod_forum\event\discussion_updated',
			'post_created' => '\mod_forum\event\post_created',
			'post_deleted' => '\mod_forum\event\post_deleted',
			'post_updated' => '\mod_forum\event\post_updated',
			'category_created' => '\mod_glossary\event\category_created',
			'category_deleted' => '\mod_glossary\event\category_deleted',
			'category_updated' => '\mod_glossary\event\category_updated',
			'glossary_comment_created' => '\mod_glossary\event\comment_created',
			'glossary_comment_deleted' => '\mod_glossary\event\comment_deleted',
			'entry_approved' => '\mod_glossary\event\entry_approved',
			'entry_created' => '\mod_glossary\event\entry_created',
			'entry_deleted' => '\mod_glossary\event\entry_deleted',
			'entry_disapproved' => '\mod_glossary\event\entry_disapproved',
			'entry_updated' => '\mod_glossary\event\entry_updated',
			'wiki_comment_created' => '\mod_wiki\event\comment_created',
			'wiki_comment_deleted' => '\mod_wiki\event\comment_deleted',
			'page_created' => '\mod_wiki\event\page_created',
			'page_deleted' => '\mod_wiki\event\page_deleted',
			'page_updated' => '\mod_wiki\event\page_updated'
		);

	public static function getStandardNames() {
		$events = array();
		foreach(self::$list as $block_table_field_name => $standard_name) {
			$events[$standard_name] = 1;
		}
		return $events;
		//$events = report_eventlist_list_generator::get_all_events_list();
	}

	public static function getShortNames() {
		$events = array();
		foreach(self::$list as $block_table_field_name => $standard_name) {
			$events[$block_table_field_name] = $standard_name;
		}
		return $events;
	}
}
?>
