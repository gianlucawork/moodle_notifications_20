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
 * Define all the restore steps that wll be used by the restore_notifications_block_task
 */

/**
 * Define the complete notifications  structure for restore
 */
class restore_notifications_block_structure_step extends restore_structure_step {

    protected function define_structure() {
        $debug = false;

        // To know if we are including userinfo
        try {
            $userinfo = $this->get_setting_value('users');
            $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::User info found=' . ($userinfo ? 1 : 0));
        } catch (base_plan_exception $e) {
            $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::User info NOT found');
            $userinfo = false;
        }

        $paths = array();
        $paths[] = new restore_path_element('block', '/block', true);
        $paths[] = new restore_path_element('notifications', '/block/notifications');
        if ($userinfo) {
            $paths[] = new restore_path_element('user', '/block/notifications/users/user');
        }

        // Return the paths wrapped into standard activity structure
        return $paths;
    }

    public function process_block($data) {
        global $DB;
        $debug = false;
        $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::Started');

        $data = (object) $data;

        // For any reason (non multiple, dupe detected...) block not restored, return
        if (!backup::VAR_BLOCKID) {
            $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::Block not restored; return');
            return;
        }

        if (!isset($data->notifications[0]) || !is_array($data->notifications[0]) || empty($data->notifications[0])) {
            $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::No Notifications block data found; return');
            return;
        }

        /*
         * Re-structure the $data object b/c for some reason it comes out of the backup like this:
         *     [notifications] => Array(
         *      [0] => Array(
         *          [id] =>
         *          [notify_by_email] =>
         *          [notify_by_sms] =>
         * 
         * Moodle docs for block restore are non-existent ATM.
         */
        $data->notifications[0]['course_id'] = $this->get_courseid();
        $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::Found courseid=' . print_r($data->notifications[0]['course_id'], true));

        $notifications_data = (object) $data->notifications[0];
        $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::About to insert/update data=' . print_r($notifications_data, true));

        $table = 'block_notifications_courses';
        if ($DB->record_exists($table, array('course_id' => $this->get_courseid()))) {
            $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::About to update_record()');
            $DB->update_record($table, $notifications_data);
        } else {
            $debug && error_log(__CLASS__ . '::' . __FUNCTION__ . '::About to insert_record()');
            unset($notifications_data->id);
            $newitemid = $DB->insert_record($table, $notifications_data);
        }
    }

    public function process_user($data) {
        global $DB;

        $data = (object) $data;

        // For any reason (non multiple, dupe detected...) block not restored, return
        if (!backup::VAR_BLOCKID) {
            return;
        }

        $data->course_id = $this->get_courseid();
        $data->user_id = $this->get_mappingid('user', $data->userid);

        $table = 'block_notifications_users';
        if ($DB->record_exists($table, array('course_id' => $data->course_id, 'user_id' => $data->user_id))) {
            $DB->update_record($table, $data);
        } else {
            $newitemid = $DB->insert_record($table, $data);
        }
    }

}
