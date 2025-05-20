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
 * This plugin allows administrators to bulk-create pages in an existing wiki
 * using a JSON object and optional group filters. It also supports adding group
 * name prefixes to page titles for better organization.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url(new moodle_url('/local/wikicreator/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('wikicreator', 'local_wikicreator'));
$PAGE->set_heading(get_string('wikicreator', 'local_wikicreator'));

global $DB;

// Retrieve plugin parameters.
$wikiid       = get_config('local_wikicreator', 'wikiid');
$pagesjson   = get_config('local_wikicreator', 'pages');
$groupscsv   = get_config('local_wikicreator', 'groups');
$useprefix    = get_config('local_wikicreator', 'usegroupprefix');

$pages = json_decode($pagesjson, true);
$groupids = array_map('trim', explode(',', $groupscsv));

if (empty($wikiid) || empty($pages) || empty($groupids)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('invalid_settings', 'local_wikicreator'), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// For each group, retrieve or create the sub-wiki, then insert the pages if they don't already exist.
foreach ($groupids as $groupid) {
    if (empty($groupid)) {
        continue;
    }

    // Retrieve the existing sub-wiki for this wikiid and groupid with userid = 0.
    $subwiki = $DB->get_record('wiki_subwikis', [
    'wikiid'  => $wikiid,
    'groupid' => $groupid,
    'userid'  => 0,
    ]);

    // If the sub-wiki does not exist, create it.
    if (!$subwiki) {
        $subwiki = new stdClass();
        $subwiki->wikiid  = $wikiid;
        $subwiki->groupid = $groupid;
        $subwiki->userid  = 0; // Assurez-vous que cette valeur correspond à votre logique.
        $subwiki->id = $DB->insert_record('wiki_subwikis', $subwiki);
    }

    // If the prefix box is ticked, retrieve the group name and prepare the HTML prefix.
    if ($useprefix) {
        $group = $DB->get_record('groups', ['id' => $groupid]);;
        $prefix = $group ? '<div style="font-size:20px;"><strong>' . $group->name . '</strong></div>' . "\n" : '';
    } else {
        $prefix = '';
    }

    // For each page defined in the configuration, insert the page if it does not already exist.
    foreach ($pages as $title => $content) {
        // Add the prefix (if enabled) before the content of the JSON file.
        $finalcontent = $prefix . $content;

        // Convert the final content to HTML.
        $htmlcontent = format_text($finalcontent, FORMAT_HTML);

        // Check whether the page already exists for this sub-wiki.
        if ($DB->record_exists('wiki_pages', ['subwikiid' => $subwiki->id, 'title' => $title])) {
            continue;
        }

        // Create the page in wiki_pages.
        $page = new stdClass();
        $page->subwikiid     = $subwiki->id;
        $page->title         = $title;
        $page->cachedcontent = $htmlcontent;
        $page->timecreated   = time();
        $page->timemodified  = time();
        $page->id = $DB->insert_record('wiki_pages', $page);

        // Create the initial version in wiki_versions.
        $version = new stdClass();
        $version->pageid      = $page->id;
        $version->content     = $htmlcontent;
        $version->version     = 1;
        $version->userid      = 0;
        $version->timecreated = time();
        $version->contentformat = 'html';
        $DB->insert_record('wiki_versions', $version);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('wikicreator', 'local_wikicreator'));
echo $OUTPUT->notification(get_string('success_message', 'local_wikicreator'), 'notifysuccess');
echo $OUTPUT->footer();
