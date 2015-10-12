<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_notifications
 * @subpackage backup-moodle2
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Define all the backup steps that wll be used by the backup_notifications_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
class backup_notifications_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        $debug = false;
        
        // To know if we are including userinfo
        try {
            $userinfo = $this->get_setting_value('users');
            $debug && error_log(__CLASS__.'::'.__FUNCTION__.'::User info found='.($userinfo?1:0));
        } catch (base_plan_exception $e) {
            $debug && error_log(__CLASS__.'::'.__FUNCTION__.'::User info NOT found');
            $userinfo = false;
        }

        // Define each element separated
        $notifications = new backup_nested_element('notifications', array('id'), array(
            'notify_by_email',
            'notify_by_sms',
            'notify_by_rss',
            'email_notification_preset',
            'sms_notification_preset',
            'rss_shortname_url_param',
            'history_length',
            'calendar_event_created',
            'calendar_event_deleted',
            'calendar_event_updated',
            'course_module_created',
            'course_module_deleted',
            'course_module_updated',
            'chapter_created',
            'chapter_deleted',
            'chapter_updated',
            'field_created',
            'field_deleted',
            'field_updated',
            'record_created',
            'record_deleted',
            'record_updated',
            'template_updated',
            'folder_updated',
            'discussion_created',
            'discussion_deleted',
            'discussion_moved',
            'discussion_updated',
            'post_created',
            'post_deleted',
            'post_updated',
            'category_created',
            'category_deleted',
            'category_updated',
            'glossary_comment_created',
            'glossary_comment_deleted',
            'entry_approved',
            'entry_created',
            'entry_deleted',
            'entry_disapproved',
            'entry_updated',
            'wiki_comment_created',
            'wiki_comment_deleted',
            'page_created',
            'page_deleted',
            'page_updated'
        ));

        $users = new backup_nested_element('users');

        $user = new backup_nested_element('user', array('id'), array(
            'user_id',
            'notify_by_email',
            'notify_by_sms'
        ));

        // Build the tree

        $notifications->add_child($users);
        $users->add_child($user);

        // Define sources

        $notifications->set_source_table('block_notifications_courses', array('course_id' => backup::VAR_COURSEID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $user->set_source_table('block_notifications_users', array('course_id' => backup::VAR_COURSEID));
        }

        // Annotations
        $user->annotate_ids('user', 'user_id');

        // Return the root element (notifications), wrapped into standard block structure
        return $this->prepare_block_structure($notifications);
    }

}
