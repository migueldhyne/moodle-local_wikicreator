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
 * Language strings for the local_wikicreator plugin.
 *
 * Contains all English language strings used by the Wiki Creator plugin,
 * including those for settings, interface labels, and messages.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['create_pages']     = 'Create Wiki Pages';
$string['group_not_found'] = 'Group ID {$a} does not exist.';
$string['invalid_page_title'] = 'Invalid page title for group {$a}.';
$string['invalid_settings'] = 'Invalid settings. Please check the configuration.';
$string['invalid_wikiid'] = 'Invalid wiki ID.';
$string['json_error'] = 'JSON decoding error: {$a}';
$string['no_pages_defined'] = 'No pages defined in the configuration.';
$string['no_valid_group'] = 'No valid group found.';
$string['page_creation_error'] = 'Error creating page "{$a}" for group {$b}: {$c}';
$string['pluginname']       = 'WikiCreator';
$string['privacy:metadata']  = 'Wikicreator does not store any personal data.';
$string['settings_groups']  = 'Group IDs (comma-separated)';
$string['settings_pages']   = 'Pages (JSON format: {"Page Title": "<p>HTML Content</p>", ...})';
$string['settings_wikiid']  = 'Wiki ID';
$string['subwiki_creation_error'] = 'Error creating subwiki for group {$a}: {$b}';
$string['success_message'] = 'Operation successful: Wiki pages have been created.';
$string['summary'] = '{$a->created} page(s) created, {$a->skipped} page(s) skipped (already exist).';
$string['usegroupprefix'] = 'Use group prefix';
$string['usegroupprefix_desc'] = 'If checked, the group name will be automatically added as a prefix (using a predefined HTML code) to each page created.';
$string['version_creation_error'] = 'Error creating version for "{$a}" (group {$b}): {$c}';
$string['wikicreator']      = 'Wiki Creator';
