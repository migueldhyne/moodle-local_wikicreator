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
 * AJAX handler for local_wikicreator.
 *
 * Provides endpoints for course search, wiki listing, group listing,
 * wiki export and creation preview.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

global $DB;

$action = required_param('action', PARAM_ALPHA);

header('Content-Type: application/json; charset=utf-8');

switch ($action) {
    case 'searchcourses':
        echo json_encode(local_wikicreator_search_courses());
        break;

    case 'getwikis':
        echo json_encode(local_wikicreator_get_wikis());
        break;

    case 'getgroups':
        echo json_encode(local_wikicreator_get_groups());
        break;

    case 'exportwiki':
        echo json_encode(local_wikicreator_export_wiki(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    case 'preview':
        echo json_encode(local_wikicreator_preview());
        break;

    default:
        echo json_encode(['error' => get_string('unknown_action', 'local_wikicreator')]);
        break;
}

/**
 * Search courses by name fragment or numeric ID.
 *
 * @return array JSON-ready associative array with 'results' key.
 */
function local_wikicreator_search_courses() {
    global $DB;

    $q = trim(required_param('q', PARAM_RAW));
    $results = [];

    if (strlen($q) < 2) {
        return ['results' => $results];
    }

    // Search by exact ID when the query is numeric.
    if (ctype_digit($q)) {
        $course = $DB->get_record('course', ['id' => (int) $q], 'id, fullname, shortname');
        if ($course) {
            $results[] = [
                'id'        => (int) $course->id,
                'fullname'  => $course->fullname,
                'shortname' => $course->shortname,
            ];
        }
    }

    // Search by fullname or shortname (case-insensitive).
    $likefn = $DB->sql_like('fullname', ':fn', false);
    $likesn = $DB->sql_like('shortname', ':sn', false);
    $sql = "SELECT id, fullname, shortname
              FROM {course}
             WHERE ($likefn OR $likesn)
               AND id != :siteid
          ORDER BY fullname ASC";
    $params = [
        'fn'     => '%' . $DB->sql_like_escape($q) . '%',
        'sn'     => '%' . $DB->sql_like_escape($q) . '%',
        'siteid' => SITEID,
    ];
    $courses = $DB->get_records_sql($sql, $params, 0, 20);

    foreach ($courses as $c) {
        // Skip duplicates already found by ID.
        $dominated = false;
        foreach ($results as $r) {
            if ($r['id'] === (int) $c->id) {
                $dominated = true;
                break;
            }
        }
        if (!$dominated) {
            $results[] = [
                'id'        => (int) $c->id,
                'fullname'  => $c->fullname,
                'shortname' => $c->shortname,
            ];
        }
    }

    return ['results' => $results];
}

/**
 * Return all wiki activities within a given course.
 *
 * @return array JSON-ready associative array with 'results' key.
 */
function local_wikicreator_get_wikis() {
    global $DB;

    $courseid = required_param('courseid', PARAM_INT);
    $wikis    = $DB->get_records('wiki', ['course' => $courseid], 'name ASC', 'id, name, wikimode');
    $results  = [];

    foreach ($wikis as $w) {
        $results[] = [
            'id'       => (int) $w->id,
            'name'     => $w->name,
            'wikimode' => $w->wikimode,
        ];
    }

    return ['results' => $results];
}

/**
 * Return all groups belonging to a given course.
 *
 * @return array JSON-ready associative array with 'results' key.
 */
function local_wikicreator_get_groups() {
    global $DB;

    $courseid = required_param('courseid', PARAM_INT);
    $groups   = $DB->get_records('groups', ['courseid' => $courseid], 'name ASC', 'id, name');
    $results  = [];

    foreach ($groups as $g) {
        $results[] = [
            'id'   => (int) $g->id,
            'name' => $g->name,
        ];
    }

    return ['results' => $results];
}

/**
 * Export every page of every sub-wiki in a given wiki as structured JSON.
 *
 * @return array JSON-ready export structure (or error).
 */
function local_wikicreator_export_wiki() {
    global $DB;

    $wikiid = required_param('wikiid', PARAM_INT);
    $wiki   = $DB->get_record('wiki', ['id' => $wikiid]);

    if (!$wiki) {
        return ['error' => get_string('invalid_wikiid', 'local_wikicreator')];
    }

    $subwikis   = $DB->get_records('wiki_subwikis', ['wikiid' => $wikiid]);
    $exportdata = [
        'wiki' => [
            'id'       => (int) $wiki->id,
            'name'     => $wiki->name,
            'course'   => (int) $wiki->course,
            'wikimode' => $wiki->wikimode,
        ],
        'subwikis' => [],
    ];

    foreach ($subwikis as $sw) {
        $groupname = '';
        if ($sw->groupid > 0) {
            $group     = $DB->get_record('groups', ['id' => $sw->groupid]);
            $groupname = $group ? $group->name : '';
        }

        $pages     = $DB->get_records('wiki_pages', ['subwikiid' => $sw->id], 'title ASC');
        $pagesdata = [];

        foreach ($pages as $p) {
            $version = $DB->get_record_sql(
                "SELECT content
                   FROM {wiki_versions}
                  WHERE pageid = :pageid
               ORDER BY version DESC
                  LIMIT 1",
                ['pageid' => $p->id]
            );
            $pagesdata[$p->title] = $version ? $version->content : $p->cachedcontent;
        }

        $exportdata['subwikis'][] = [
            'groupid'   => (int) $sw->groupid,
            'groupname' => $groupname,
            'userid'    => (int) $sw->userid,
            'pages'     => $pagesdata,
        ];
    }

    return $exportdata;
}

/**
 * Validate configuration and produce a dry-run preview of what would be created.
 *
 * @return array JSON-ready preview structure (or error).
 */
function local_wikicreator_preview() {
    global $DB;

    $wikiid    = required_param('wikiid', PARAM_INT);
    $pagesjson = required_param('pages', PARAM_RAW);
    $groupsraw = required_param('groups', PARAM_RAW);
    $useprefix = optional_param('useprefix', 0, PARAM_INT);

    // Validate wiki.
    $wiki = $DB->get_record('wiki', ['id' => $wikiid]);
    if (!$wiki) {
        return ['error' => get_string('invalid_wikiid', 'local_wikicreator')];
    }

    // Validate JSON.
    $pages = json_decode($pagesjson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($pages)) {
        return ['error' => get_string('json_error', 'local_wikicreator', json_last_error_msg())];
    }

    // Validate groups.
    $gids = array_filter(array_map('trim', explode(',', $groupsraw)), function ($id) {
        return ctype_digit($id);
    });
    if (empty($gids)) {
        return ['error' => get_string('no_valid_group', 'local_wikicreator')];
    }

    $errors  = [];
    $preview = [];

    foreach ($gids as $gid) {
        $group = $DB->get_record('groups', ['id' => $gid]);
        if (!$group) {
            $errors[] = get_string('group_not_found', 'local_wikicreator', $gid);
            continue;
        }

        $subwiki = $DB->get_record('wiki_subwikis', [
            'wikiid'  => $wikiid,
            'groupid' => $gid,
            'userid'  => 0,
        ]);

        $grouppreview = [
            'groupid'        => (int) $gid,
            'groupname'      => $group->name,
            'subwiki_exists' => !empty($subwiki),
            'pages'          => [],
        ];

        foreach ($pages as $title => $content) {
            $titleclean = clean_param($title, PARAM_TEXT);
            $exists     = false;
            if ($subwiki) {
                $exists = $DB->record_exists('wiki_pages', [
                    'subwikiid' => $subwiki->id,
                    'title'     => $titleclean,
                ]);
            }

            $grouppreview['pages'][] = [
                'title'          => $titleclean,
                'prefix'         => $useprefix ? $group->name . ' — ' : '',
                'already_exists' => $exists,
                'content_preview' => mb_substr(strip_tags($content), 0, 120),
            ];
        }

        $preview[] = $grouppreview;
    }

    return [
        'wiki_name'   => $wiki->name,
        'preview'     => $preview,
        'errors'      => $errors,
        'total_pages' => count($pages) * count($gids),
    ];
}
