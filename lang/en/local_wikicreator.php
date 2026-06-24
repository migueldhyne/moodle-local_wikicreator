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
 * English language strings for the local_wikicreator plugin.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['badge_exists']          = 'EXISTS';
$string['badge_new']             = 'NEW';
$string['btn_back_manage']       = 'Back to management';
$string['btn_copy_json']         = 'Copy JSON';
$string['btn_create']            = 'Create the pages';
$string['btn_download_json']     = 'Download JSON';
$string['btn_export']            = 'Export as JSON';
$string['btn_open_manage']       = 'Open the management interface';
$string['btn_preview']           = 'Preview';
$string['btn_run_creation']      = 'Yes, create the pages';
$string['btn_use_exported']      = 'Use in creation tab';
$string['btn_validate_json']     = 'Validate JSON';
$string['cancel']                = 'Cancel';
$string['change']                = 'Change';
$string['confirm_message']       = 'You are about to create pages in the wiki {wiki} for {groups} group(s). {new} new page(s) will be created, {skipped} will be skipped (already exist).';
$string['confirm_run_legacy']    = 'You are about to create pages in the wiki <strong>{$a->wiki}</strong>: {$a->pages} page(s) x {$a->groups} group(s). Are you sure?';
$string['confirm_title']         = 'Confirm creation';
$string['copied']                = 'Copied!';
$string['course_label_id']       = 'ID:';
$string['course_label_short']    = 'Short name:';
$string['create_pages']          = 'Create Wiki Pages';
$string['export_info']           = 'Select a course and a wiki to extract all its pages as JSON. You can then reuse this JSON to recreate the wiki elsewhere.';
$string['export_title']          = 'Export an existing wiki';
$string['exporting']             = 'Exporting...';
$string['group_label_id']        = 'ID:';
$string['group_not_found']       = 'Group ID {$a} does not exist.';
$string['invalid_page_title']    = 'Invalid page title for group {$a}.';
$string['invalid_settings']      = 'Invalid settings. Please check the configuration.';
$string['invalid_wikiid']        = 'Invalid wiki ID.';
$string['json_empty_key']        = 'A page title (key) is empty.';
$string['json_error']            = 'JSON decoding error: {$a}';
$string['json_must_be_object']   = 'The JSON must be an object { ... }, not an array.';
$string['json_no_html_warning']  = 'The content for page "{title}" does not seem to contain HTML tags.';
$string['json_page_count']       = '{n} page(s) defined.';
$string['json_valid']            = 'JSON structure is valid.';
$string['json_validation']       = 'JSON error:';
$string['json_value_not_string'] = 'The content for page "{title}" must be a text string.';
$string['loading']               = 'Loading...';
$string['manage_desc']           = 'The management interface provides course search, wiki and group selection, preview and wiki export features.';
$string['manage_title']          = 'Wiki Creator - Management';
$string['no_groups_found']       = 'No groups found in this course.';
$string['no_pages_defined']      = 'No pages defined in the configuration.';
$string['no_results']            = 'No results found.';
$string['no_valid_group']        = 'No valid group found.';
$string['no_wiki_found']         = 'No wiki found in this course.';
$string['page_creation_error']   = 'Error creating page "{$a}" for group {$b}: {$c}';
$string['pages_desc']            = 'JSON object: keys = page titles, values = HTML content.';
$string['pluginname']            = 'Wiki Creator';
$string['preview_for']           = 'Preview for wiki:';
$string['privacy:metadata']      = 'Wiki Creator does not store any personal data.';
$string['reminder_step1']        = 'The target wiki activity must already exist in the course (with HTML forced format).';
$string['reminder_step2']        = 'If you are working with groups, they must be configured in the course and the wiki must use "Separate groups" mode.';
$string['reminder_step3']        = 'A first page must have been created manually in each group sub-wiki so that the sub-wikis are properly initialised.';
$string['reminder_text']         = 'Make sure the following conditions are met:';
$string['reminder_title']        = 'Before you begin';
$string['render_hide']           = 'Hide render';
$string['render_show']           = 'View render';
$string['search_course']         = 'Search for a course';
$string['search_course_desc']    = 'Type at least 2 characters of the course name, its short name or its numeric ID.';
$string['search_placeholder']    = 'Course name or ID...';
$string['select_all']            = 'Select all';
$string['select_groups']         = 'Choose the target groups';
$string['select_groups_desc']    = 'Click on the groups for which pages will be created.';
$string['select_none']           = 'Deselect all';
$string['select_wiki']           = 'Choose a wiki';
$string['select_wiki_desc']      = 'Click on the wiki you want to populate.';
$string['settings_pages']        = 'Pages (JSON)';
$string['step_course']           = 'Select a course';
$string['step_groups']           = 'Select groups';
$string['step_pages']            = 'Define pages (JSON)';
$string['step_preview']          = 'Preview and create';
$string['step_wiki']             = 'Select a wiki';
$string['subwiki_creation_error'] = 'Error creating sub-wiki for group {$a}: {$b}';
$string['subwiki_will_create']   = 'sub-wiki will be created';
$string['success_message']       = 'Operation successful: Wiki pages have been created.';
$string['summary']               = '{$a->created} page(s) created, {$a->skipped} page(s) skipped (already exist).';
$string['tab_create']            = 'Create pages';
$string['tab_export']            = 'Export a wiki';
$string['total_pages_preview']   = 'Total pages to process';
$string['unknown_action']        = 'Unknown action.';
$string['usegroupprefix']        = 'Use group prefix';
$string['usegroupprefix_desc']   = 'If checked, the group name will be automatically added as a prefix (with predefined HTML formatting) to each page created.';
$string['version_creation_error'] = 'Error creating version for "{$a}" (group {$b}): {$c}';
$string['wiki_label_id']         = 'ID:';
$string['wiki_label_mode']       = 'Mode:';
$string['wikicreator']           = 'Wiki Creator';
