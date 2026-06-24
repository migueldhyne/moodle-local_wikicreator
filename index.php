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
 * Accepts parameters via POST (from manage.php) or falls back to plugin config.
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

global $DB, $OUTPUT, $CFG;

require_once($CFG->libdir.'/weblib.php');

// Check if this is a confirmed POST from manage.php.
$ispost = ($_SERVER['REQUEST_METHOD'] === 'POST' && optional_param('confirm', 0, PARAM_INT) === 1);

if ($ispost) {
    require_sesskey();
    $wikiid    = required_param('wikiid', PARAM_INT);
    $pagesjson = required_param('pages', PARAM_RAW);
    $groupscsv = required_param('groups', PARAM_RAW);
    $useprefix = optional_param('useprefix', 0, PARAM_INT);

    // Also save to config for future reference.
    set_config('wikiid', $wikiid, 'local_wikicreator');
    set_config('pages', $pagesjson, 'local_wikicreator');
    set_config('groups', $groupscsv, 'local_wikicreator');
    set_config('usegroupprefix', $useprefix, 'local_wikicreator');
} else {
    // Legacy mode: read from plugin settings.
    $wikiid    = get_config('local_wikicreator', 'wikiid');
    $pagesjson = get_config('local_wikicreator', 'pages');
    $groupscsv = get_config('local_wikicreator', 'groups');
    $useprefix = get_config('local_wikicreator', 'usegroupprefix');

    // If accessed directly (not POST), show a confirmation page.
    if (!optional_param('run', 0, PARAM_INT)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('wikicreator', 'local_wikicreator'));

        // Validate before showing confirmation.
        $valid = true;
        if (empty($wikiid) || !ctype_digit((string)$wikiid)) {
            echo $OUTPUT->notification(get_string('invalid_wikiid', 'local_wikicreator'), 'notifyproblem');
            $valid = false;
        }
        $groupids = array_filter(array_map('trim', explode(',', $groupscsv)), function($id) {
            return ctype_digit($id);
        });
        if ($valid && empty($groupids)) {
            echo $OUTPUT->notification(get_string('no_valid_group', 'local_wikicreator'), 'notifyproblem');
            $valid = false;
        }
        if ($valid) {
            $pages = json_decode($pagesjson, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($pages) || empty($pages)) {
                echo $OUTPUT->notification(get_string('json_error', 'local_wikicreator', json_last_error_msg()), 'notifyproblem');
                $valid = false;
            }
        }

        if ($valid) {
            $wiki = $DB->get_record('wiki', ['id' => $wikiid]);
            $wikiname = $wiki ? $wiki->name : "ID $wikiid";
            $numpages = count($pages);
            $numgroups = count($groupids);

            echo '<div class="alert alert-info">';
            echo '<p>' . get_string('confirm_run_legacy', 'local_wikicreator',
                (object)['wiki' => s($wikiname), 'pages' => $numpages, 'groups' => $numgroups]) . '</p>';
            echo '</div>';

            $runurl = new moodle_url('/local/wikicreator/index.php', ['run' => 1, 'sesskey' => sesskey()]);
            $manageurl = new moodle_url('/local/wikicreator/manage.php');
            echo '<p>';
            echo '<a href="' . $runurl->out(false) . '" class="btn btn-primary">' .
                 get_string('btn_run_creation', 'local_wikicreator') . '</a> ';
            echo '<a href="' . $manageurl->out(false) . '" class="btn btn-secondary">' .
                 get_string('btn_back_manage', 'local_wikicreator') . '</a>';
            echo '</p>';
        } else {
            $manageurl = new moodle_url('/local/wikicreator/manage.php');
            echo '<p><a href="' . $manageurl->out(false) . '" class="btn btn-primary">' .
                 get_string('btn_back_manage', 'local_wikicreator') . '</a></p>';
        }

        echo $OUTPUT->footer();
        exit;
    }

    // If run=1, verify sesskey.
    require_sesskey();
}

// Execute wiki creation.

// Strict validation of wikiid.
if (empty($wikiid) || !ctype_digit((string)$wikiid)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('invalid_wikiid', 'local_wikicreator'), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// Validate and extract group IDs.
$groupids = array_filter(array_map('trim', explode(',', $groupscsv)), function($id) {
    return ctype_digit($id);
});
if (empty($groupids)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('no_valid_group', 'local_wikicreator'), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// Decode JSON with error handling.
$pages = json_decode($pagesjson, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($pages)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('json_error', 'local_wikicreator', json_last_error_msg()), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}
if (empty($pages)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('no_pages_defined', 'local_wikicreator'), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// Counters for final report.
$pagescreated = 0;
$pagesskipped = 0;
$errors = [];

foreach ($groupids as $groupid) {
    // Check group existence.
    $group = $DB->get_record('groups', ['id' => $groupid]);
    if (!$group) {
        $errors[] = get_string('group_not_found', 'local_wikicreator', $groupid);
        continue;
    }

    // Retrieve or create subwiki.
    $subwiki = $DB->get_record('wiki_subwikis', [
        'wikiid'  => $wikiid,
        'groupid' => $groupid,
        'userid'  => 0,
    ]);
    if (!$subwiki) {
        $subwiki = new stdClass();
        $subwiki->wikiid  = $wikiid;
        $subwiki->groupid = $groupid;
        $subwiki->userid  = 0;
        try {
            $subwiki->id = $DB->insert_record('wiki_subwikis', $subwiki);
        } catch (Exception $e) {
            $errors[] = get_string('subwiki_creation_error', 'local_wikicreator', [$groupid, $e->getMessage()]);
            continue;
        }
    }

    // Prepare group prefix (secured HTML).
    $prefix = '';
    if ($useprefix) {
        $groupnameclean = clean_text($group->name, FORMAT_HTML);
        $prefix = '<div style="font-size:20px;"><strong>' . $groupnameclean . '</strong></div>' . "\n";
    }

    foreach ($pages as $title => $content) {
        if (empty($title) || !is_string($title)) {
            $errors[] = get_string('invalid_page_title', 'local_wikicreator', $groupid);
            continue;
        }
        $titleclean = clean_param($title, PARAM_TEXT);

        if ($DB->record_exists('wiki_pages', ['subwikiid' => $subwiki->id, 'title' => $titleclean])) {
            $pagesskipped++;
            continue;
        }

        $finalcontent = $prefix . $content;
        $htmlcontent = clean_text($finalcontent, FORMAT_HTML);

        $page = new stdClass();
        $page->subwikiid     = $subwiki->id;
        $page->title         = $titleclean;
        $page->cachedcontent = $htmlcontent;
        $page->timecreated   = time();
        $page->timemodified  = time();

        try {
            $page->id = $DB->insert_record('wiki_pages', $page);
        } catch (Exception $e) {
            $errors[] = get_string('page_creation_error', 'local_wikicreator', [$titleclean, $groupid, $e->getMessage()]);
            continue;
        }

        $version = new stdClass();
        $version->pageid      = $page->id;
        $version->content     = $htmlcontent;
        $version->version     = 1;
        $version->userid      = 0;
        $version->timecreated = time();
        $version->contentformat = 'html';

        try {
            $DB->insert_record('wiki_versions', $version);
            $pagescreated++;
        } catch (Exception $e) {
            $errors[] = get_string('version_creation_error', 'local_wikicreator', [$titleclean, $groupid, $e->getMessage()]);
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('wikicreator', 'local_wikicreator'));

$summarydata = (object)['created' => $pagescreated, 'skipped' => $pagesskipped];
echo $OUTPUT->notification(get_string('summary', 'local_wikicreator', $summarydata), 'notifysuccess');

if (!empty($errors)) {
    echo $OUTPUT->notification(implode('<br>', $errors), 'notifyproblem');
}

// Link back to manage page.
$manageurl = new moodle_url('/local/wikicreator/manage.php');
echo '<p><a href="' . $manageurl->out(false) . '" class="btn btn-secondary">' .
     get_string('btn_back_manage', 'local_wikicreator') . '</a></p>';

echo $OUTPUT->footer();
