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
 * Interactive management page for the Wiki Creator plugin.
 *
 * Provides a step-by-step interface for selecting a course, a wiki and groups,
 * editing the JSON page definitions, previewing the outcome and launching
 * the creation. A second tab allows exporting an existing wiki to JSON.
 *
 * All HTML is emitted from a single PHP block (no inline <?php ?> tags)
 * so that PHPCS does not require a docblock per inline fragment.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_url(new moodle_url('/local/wikicreator/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('manage_title', 'local_wikicreator'));
$PAGE->set_heading(get_string('manage_title', 'local_wikicreator'));
$PAGE->set_pagelayout('admin');

global $OUTPUT;

// Saved configuration (used to pre-fill the JSON editor).
$savedpages  = get_config('local_wikicreator', 'pages');
$savedprefix = get_config('local_wikicreator', 'usegroupprefix');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manage_title', 'local_wikicreator'));

$sesskey   = sesskey();
$ajaxurl   = new moodle_url('/local/wikicreator/ajax.php');
$createurl = new moodle_url('/local/wikicreator/index.php');

// Build a JSON-safe dictionary of every string the JavaScript needs.
// Using json_encode() guarantees proper escaping of quotes and apostrophes.
$jsstrings = json_encode([
    'badge_exists'          => get_string('badge_exists',          'local_wikicreator'),
    'badge_new'             => get_string('badge_new',             'local_wikicreator'),
    'btn_copy_json'         => get_string('btn_copy_json',         'local_wikicreator'),
    'btn_export'            => get_string('btn_export',            'local_wikicreator'),
    'btn_preview'           => get_string('btn_preview',           'local_wikicreator'),
    'btn_validate_json'     => get_string('btn_validate_json',     'local_wikicreator'),
    'change'                => get_string('change',                'local_wikicreator'),
    'confirm_message'       => get_string('confirm_message',       'local_wikicreator'),
    'copied'                => get_string('copied',                'local_wikicreator'),
    'course_label_id'       => get_string('course_label_id',       'local_wikicreator'),
    'course_label_short'    => get_string('course_label_short',    'local_wikicreator'),
    'exporting'             => get_string('exporting',             'local_wikicreator'),
    'group_label_id'        => get_string('group_label_id',        'local_wikicreator'),
    'json_empty_key'        => get_string('json_empty_key',        'local_wikicreator'),
    'json_must_be_object'   => get_string('json_must_be_object',   'local_wikicreator'),
    'json_no_html_warning'  => get_string('json_no_html_warning',  'local_wikicreator'),
    'json_page_count'       => get_string('json_page_count',       'local_wikicreator'),
    'json_valid'            => get_string('json_valid',            'local_wikicreator'),
    'json_validation'       => get_string('json_validation',       'local_wikicreator'),
    'json_value_not_string' => get_string('json_value_not_string', 'local_wikicreator'),
    'loading'               => get_string('loading',               'local_wikicreator'),
    'no_groups_found'       => get_string('no_groups_found',       'local_wikicreator'),
    'no_results'            => get_string('no_results',            'local_wikicreator'),
    'no_wiki_found'         => get_string('no_wiki_found',         'local_wikicreator'),
    'preview_for'           => get_string('preview_for',           'local_wikicreator'),
    'render_hide'           => get_string('render_hide',           'local_wikicreator'),
    'render_show'           => get_string('render_show',           'local_wikicreator'),
    'subwiki_will_create'   => get_string('subwiki_will_create',   'local_wikicreator'),
    'total_pages_preview'   => get_string('total_pages_preview',   'local_wikicreator'),
    'wiki_label_id'         => get_string('wiki_label_id',         'local_wikicreator'),
    'wiki_label_mode'       => get_string('wiki_label_mode',       'local_wikicreator'),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

// Pre-compute all translated strings used in the HTML so no inline PHP is needed.
$str = [
    'reminder_title'     => get_string('reminder_title',     'local_wikicreator'),
    'reminder_text'      => get_string('reminder_text',      'local_wikicreator'),
    'reminder_step1'     => get_string('reminder_step1',     'local_wikicreator'),
    'reminder_step2'     => get_string('reminder_step2',     'local_wikicreator'),
    'reminder_step3'     => get_string('reminder_step3',     'local_wikicreator'),
    'tab_create'         => get_string('tab_create',         'local_wikicreator'),
    'tab_export'         => get_string('tab_export',         'local_wikicreator'),
    'step_course'        => '&#9312; ' . get_string('step_course',    'local_wikicreator'),
    'step_wiki'          => '&#9313; ' . get_string('step_wiki',      'local_wikicreator'),
    'step_groups'        => '&#9314; ' . get_string('step_groups',    'local_wikicreator'),
    'step_pages'         => '&#9315; ' . get_string('step_pages',     'local_wikicreator'),
    'step_preview'       => '&#9316; ' . get_string('step_preview',   'local_wikicreator'),
    'search_course'      => get_string('search_course',      'local_wikicreator'),
    'search_course_desc' => get_string('search_course_desc', 'local_wikicreator'),
    'search_placeholder' => s(get_string('search_placeholder', 'local_wikicreator')),
    'select_wiki'        => get_string('select_wiki',        'local_wikicreator'),
    'select_wiki_desc'   => get_string('select_wiki_desc',   'local_wikicreator'),
    'select_groups'      => get_string('select_groups',      'local_wikicreator'),
    'select_groups_desc' => get_string('select_groups_desc', 'local_wikicreator'),
    'select_all'         => get_string('select_all',         'local_wikicreator'),
    'select_none'        => get_string('select_none',        'local_wikicreator'),
    'settings_pages'     => get_string('settings_pages',     'local_wikicreator'),
    'pages_desc'         => get_string('pages_desc',         'local_wikicreator'),
    'saved_json'         => s($savedpages ? $savedpages : '{"Accueil": "<p>Bienvenue sur le wiki.</p>"}'),
    'btn_validate_json'  => get_string('btn_validate_json',  'local_wikicreator'),
    'usegroupprefix'     => get_string('usegroupprefix',     'local_wikicreator'),
    'usegroupprefix_desc' => get_string('usegroupprefix_desc', 'local_wikicreator'),
    'btn_preview'        => get_string('btn_preview',        'local_wikicreator'),
    'export_title'       => get_string('export_title',       'local_wikicreator'),
    'export_info'        => get_string('export_info',        'local_wikicreator'),
    'btn_export'         => get_string('btn_export',         'local_wikicreator'),
    'btn_copy_json'      => get_string('btn_copy_json',      'local_wikicreator'),
    'btn_download_json'  => get_string('btn_download_json',  'local_wikicreator'),
    'btn_use_exported'   => get_string('btn_use_exported',   'local_wikicreator'),
    'confirm_title'      => get_string('confirm_title',      'local_wikicreator'),
    'cancel'             => get_string('cancel',             'local_wikicreator'),
    'btn_create'         => get_string('btn_create',         'local_wikicreator'),
    'ajaxurl'            => json_encode($ajaxurl->out(false)),
    'createurl'          => json_encode($createurl->out(false)),
    'sesskey'            => json_encode($sesskey),
    'jsstrings'          => $jsstrings,
];

// CSS block (static, no PHP needed).
echo <<<HTML
<style>
.wc-container {max-width: 960px; margin: 0 auto;}
.wc-reminder {
    background: #fff3cd; border: 1px solid #ffecb5;
    border-radius: 8px; padding: 16px 20px; margin-bottom: 20px;
}
.wc-reminder h4 {margin: 0 0 6px; font-size: 1rem; color: #664d03;}
.wc-reminder p {margin: 0; font-size: .88rem; color: #664d03;}
.wc-reminder ul {margin: 6px 0 0; padding-left: 20px; font-size: .88rem; color: #664d03;}
.wc-section {
    background: #fff; border: 1px solid #dee2e6;
    border-radius: 8px; padding: 24px; margin-bottom: 20px;
}
.wc-section h3 {
    margin-top: 0; margin-bottom: 16px; padding-bottom: 8px;
    border-bottom: 2px solid #0f6cbf; font-size: 1.1rem;
}
.wc-field {margin-bottom: 16px;}
.wc-field label {display: block; font-weight: 600; margin-bottom: 4px; font-size: .9rem;}
.wc-desc {color: #6c757d; font-size: .82rem; margin-bottom: 6px;}
.wc-search-wrap {position: relative;}
.wc-search-wrap input[type="text"] {
    width: 100%; padding: 8px 12px; border: 1px solid #ced4da;
    border-radius: 4px; font-size: .95rem; box-sizing: border-box;
}
.wc-dropdown {
    position: absolute; top: 100%; left: 0; right: 0;
    background: #fff; border: 1px solid #ced4da; border-top: none;
    border-radius: 0 0 4px 4px; max-height: 260px; overflow-y: auto;
    z-index: 100; display: none; box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.wc-dropdown-item {padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f0f0f0;}
.wc-dropdown-item:last-child {border-bottom: none;}
.wc-dropdown-item:hover {background: #e9ecef;}
.wc-dropdown-item .wc-item-fullname {font-weight: 600; font-size: .9rem; display: block;}
.wc-dropdown-item .wc-item-meta {color: #6c757d; font-size: .8rem; display: block; margin-top: 2px;}
.wc-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: #d1e7fd; color: #0a58ca;
    padding: 5px 12px; border-radius: 16px; font-size: .85rem; margin-top: 6px;
}
.wc-chip .wc-chip-sub {color: #6c757d; font-size: .78rem;}
.wc-chip .wc-remove {cursor: pointer; font-weight: bold; margin-left: 4px; font-size: 1rem; line-height: 1;}
.wc-wiki-list {display: flex; flex-direction: column; gap: 6px; margin-top: 8px;}
.wc-wiki-item {
    padding: 10px 14px; border: 1px solid #dee2e6;
    border-radius: 6px; cursor: pointer; transition: all .15s;
}
.wc-wiki-item:hover {border-color: #0f6cbf; background: #f0f7ff;}
.wc-wiki-item.selected {border-color: #0f6cbf; background: #d1e7fd;}
.wc-wiki-item .wc-wiki-name {font-weight: 600; display: block;}
.wc-wiki-item .wc-wiki-meta {color: #6c757d; font-size: .8rem;}
.wc-groups-grid {display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;}
.wc-group-chip {
    padding: 6px 12px; border: 1px solid #dee2e6; border-radius: 20px;
    cursor: pointer; font-size: .85rem; transition: all .15s; user-select: none;
}
.wc-group-chip:hover {border-color: #0f6cbf;}
.wc-group-chip.selected {background: #0f6cbf; color: #fff; border-color: #0f6cbf;}
.wc-group-actions {margin-top: 8px;}
.wc-group-actions a {
    font-size: .82rem; margin-right: 12px; cursor: pointer;
    color: #0f6cbf; text-decoration: underline;
}
textarea.wc-json {
    width: 100%; min-height: 200px; font-family: 'Courier New', monospace;
    font-size: .88rem; padding: 10px; border: 1px solid #ced4da;
    border-radius: 4px; resize: vertical; box-sizing: border-box;
}
.wc-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px; border: none; border-radius: 6px;
    font-size: .95rem; cursor: pointer; font-weight: 600; transition: background .15s;
}
.wc-btn-primary {background: #0f6cbf; color: #fff;}
.wc-btn-primary:hover {background: #0b5aa0;}
.wc-btn-success {background: #198754; color: #fff;}
.wc-btn-success:hover {background: #157347;}
.wc-btn-secondary {background: #6c757d; color: #fff;}
.wc-btn-secondary:hover {background: #5a6268;}
.wc-btn-warning {background: #fd7e14; color: #fff;}
.wc-btn-warning:hover {background: #e06c0a;}
.wc-btn:disabled {opacity: .5; cursor: not-allowed;}
.wc-actions {display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;}
.wc-preview-group {border: 1px solid #dee2e6; border-radius: 6px; margin-bottom: 12px; overflow: hidden;}
.wc-preview-group-header {
    background: #f8f9fa; padding: 10px 14px;
    font-weight: 600; border-bottom: 1px solid #dee2e6;
}
.wc-preview-page {
    padding: 8px 14px; border-bottom: 1px solid #f0f0f0;
    display: flex; align-items: center; gap: 8px;
}
.wc-preview-page:last-child {border-bottom: none;}
.wc-badge-new {background: #198754; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: .75rem;}
.wc-badge-exists {background: #ffc107; color: #000; padding: 2px 8px; border-radius: 10px; font-size: .75rem;}
.wc-preview-content {color: #6c757d; font-size: .8rem; font-style: italic;}
.wc-spinner {
    display: inline-block; width: 16px; height: 16px;
    border: 2px solid #fff; border-top-color: transparent;
    border-radius: 50%; animation: wcspin .6s linear infinite;
}
@keyframes wcspin {to {transform: rotate(360deg);}}
.wc-tabs {display: flex; gap: 0; border-bottom: 2px solid #dee2e6; margin-bottom: 0;}
.wc-tab {
    padding: 10px 20px; cursor: pointer; font-weight: 600; font-size: .95rem;
    border: none; background: none; border-bottom: 2px solid transparent;
    margin-bottom: -2px; color: #6c757d; transition: all .15s;
}
.wc-tab:hover {color: #0f6cbf;}
.wc-tab.active {color: #0f6cbf; border-bottom-color: #0f6cbf;}
.wc-tab-content {display: none;}
.wc-tab-content.active {display: block;}
.wc-export-result textarea {
    width: 100%; min-height: 300px;
    font-family: 'Courier New', monospace; font-size: .85rem; box-sizing: border-box;
}
.wc-checkbox {display: flex; align-items: center; gap: 8px;}
.wc-checkbox input[type="checkbox"] {width: 18px; height: 18px;}
.wc-info {
    background: #cfe2ff; border: 1px solid #b6d4fe; border-radius: 6px;
    padding: 12px 16px; font-size: .88rem; color: #084298; margin-bottom: 16px;
}
.wc-confirm-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,.5); z-index: 9999;
    display: none; align-items: center; justify-content: center;
}
.wc-confirm-box {
    background: #fff; border-radius: 10px; padding: 30px;
    max-width: 500px; width: 90%; text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
}
.wc-confirm-box h4 {margin-top: 0;}
.wc-confirm-box .wc-confirm-actions {display: flex; gap: 10px; justify-content: center; margin-top: 20px;}
.wc-hidden {display: none !important;}
.wc-render-toggle {
    font-size: .8rem; color: #0f6cbf; cursor: pointer;
    text-decoration: underline; margin-left: auto; white-space: nowrap;
}
.wc-render-panel {
    background: #fdfdfd; border-top: 1px dashed #dee2e6;
    padding: 0; overflow: hidden; transition: max-height .3s ease;
}
.wc-render-panel iframe {width: 100%; border: none; min-height: 60px; display: block;}
.wc-json-status {margin-top: 8px; padding: 10px 14px; border-radius: 6px; font-size: .85rem;}
.wc-json-status.valid {background: #d1e7dd; border: 1px solid #badbcc; color: #0f5132;}
.wc-json-status.invalid {background: #f8d7da; border: 1px solid #f5c2c7; color: #842029;}
.wc-json-status.warnings {background: #fff3cd; border: 1px solid #ffecb5; color: #664d03;}
.wc-json-status ul {margin: 4px 0 0; padding-left: 18px;}
.wc-json-status li {margin-bottom: 2px;}
</style>
HTML;

// HTML body — all dynamic values interpolated from $str (no inline PHP blocks).
echo html_writer::start_div('wc-container');

// Reminder box.
echo html_writer::start_div('wc-reminder');
echo html_writer::tag('h4', $str['reminder_title']);
echo html_writer::tag('p', $str['reminder_text']);
echo html_writer::start_tag('ul');
echo html_writer::tag('li', $str['reminder_step1']);
echo html_writer::tag('li', $str['reminder_step2']);
echo html_writer::tag('li', $str['reminder_step3']);
echo html_writer::end_tag('ul');
echo html_writer::end_div();

// Tabs.
echo html_writer::start_div('wc-tabs');
echo html_writer::tag('button', $str['tab_create'],
    ['type' => 'button', 'class' => 'wc-tab active', 'data-tab' => 'create']);
echo html_writer::tag('button', $str['tab_export'],
    ['type' => 'button', 'class' => 'wc-tab', 'data-tab' => 'export']);
echo html_writer::end_div();

// TAB: Create.
echo html_writer::start_div('wc-tab-content active', ['id' => 'tab-create']);

// Step 1: course search.
echo html_writer::start_div('wc-section');
echo html_writer::tag('h3', $str['step_course']);
echo html_writer::start_div('wc-field');
echo html_writer::tag('label', $str['search_course'], ['for' => 'wc-course-search']);
echo html_writer::tag('div', $str['search_course_desc'], ['class' => 'wc-desc']);
echo html_writer::start_div('wc-search-wrap');
echo html_writer::empty_tag('input', [
    'type' => 'text', 'id' => 'wc-course-search',
    'autocomplete' => 'off', 'placeholder' => $str['search_placeholder'],
]);
echo html_writer::div('', 'wc-dropdown', ['id' => 'wc-course-dropdown']);
echo html_writer::end_div();
echo html_writer::div('', 'wc-hidden', ['id' => 'wc-course-selected']);
echo html_writer::end_div();
echo html_writer::end_div();

// Step 2: wiki selection.
echo html_writer::start_div('wc-section wc-hidden', ['id' => 'wc-wiki-section']);
echo html_writer::tag('h3', $str['step_wiki']);
echo html_writer::start_div('wc-field');
echo html_writer::tag('label', $str['select_wiki']);
echo html_writer::tag('div', $str['select_wiki_desc'], ['class' => 'wc-desc']);
echo html_writer::div('', 'wc-wiki-list', ['id' => 'wc-wiki-list']);
echo html_writer::end_div();
echo html_writer::end_div();

// Step 3: group selection.
echo html_writer::start_div('wc-section wc-hidden', ['id' => 'wc-group-section']);
echo html_writer::tag('h3', $str['step_groups']);
echo html_writer::start_div('wc-field');
echo html_writer::tag('label', $str['select_groups']);
echo html_writer::tag('div', $str['select_groups_desc'], ['class' => 'wc-desc']);
echo html_writer::div('', 'wc-groups-grid', ['id' => 'wc-groups-grid']);
echo html_writer::start_div('wc-group-actions', ['id' => 'wc-group-actions', 'style' => 'display:none']);
echo html_writer::tag('a', $str['select_all'], ['href' => '#', 'id' => 'wc-select-all']);
echo html_writer::tag('a', $str['select_none'], ['href' => '#', 'id' => 'wc-select-none']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// Step 4: JSON pages editor.
echo html_writer::start_div('wc-section wc-hidden', ['id' => 'wc-pages-section']);
echo html_writer::tag('h3', $str['step_pages']);
echo html_writer::start_div('wc-field');
echo html_writer::tag('label', $str['settings_pages'], ['for' => 'wc-pages-json']);
echo html_writer::tag('div', $str['pages_desc'], ['class' => 'wc-desc']);
echo html_writer::tag('textarea', $str['saved_json'],
    ['class' => 'wc-json', 'id' => 'wc-pages-json']);
echo html_writer::tag('div', '',
    ['id' => 'wc-json-error', 'style' => 'display:none;margin-top:4px;font-size:.85rem;color:#dc3545']);
echo html_writer::start_div('', ['style' => 'margin-top:8px']);
echo html_writer::tag('button', $str['btn_validate_json'], [
    'type'  => 'button',
    'class' => 'wc-btn wc-btn-secondary',
    'id'    => 'wc-btn-validate-json',
    'style' => 'padding:6px 14px;font-size:.85rem',
]);
echo html_writer::end_div();
echo html_writer::tag('div', '', ['id' => 'wc-json-status', 'class' => 'wc-json-status wc-hidden']);
echo html_writer::end_div();

// Prefix checkbox.
echo html_writer::start_div('wc-field');
echo html_writer::start_div('wc-checkbox');
$checkboxattrs = ['type' => 'checkbox', 'id' => 'wc-useprefix'];
if ($savedprefix) {
    $checkboxattrs['checked'] = 'checked';
}
echo html_writer::empty_tag('input', $checkboxattrs);
echo html_writer::tag('label', $str['usegroupprefix'],
    ['for' => 'wc-useprefix', 'style' => 'margin:0;font-weight:normal']);
echo html_writer::end_div();
echo html_writer::tag('div', $str['usegroupprefix_desc'],
    ['class' => 'wc-desc', 'style' => 'margin-left:26px']);
echo html_writer::end_div();
echo html_writer::end_div();

// Step 5: preview + create.
echo html_writer::start_div('wc-section wc-hidden', ['id' => 'wc-action-section']);
echo html_writer::tag('h3', $str['step_preview']);
echo html_writer::start_div('wc-actions');
echo html_writer::tag('button', $str['btn_preview'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-primary', 'id' => 'wc-btn-preview']);
echo html_writer::end_div();
echo html_writer::div('', '', ['id' => 'wc-preview-box', 'style' => 'margin-top:16px']);
echo html_writer::end_div();

echo html_writer::end_div(); // End of tab-create.

// TAB: Export.
echo html_writer::start_div('wc-tab-content', ['id' => 'tab-export']);
echo html_writer::start_div('wc-section');
echo html_writer::tag('h3', $str['export_title']);
echo html_writer::tag('div', $str['export_info'], ['class' => 'wc-info']);

echo html_writer::start_div('wc-field');
echo html_writer::tag('label', $str['search_course'], ['for' => 'wc-export-course-search']);
echo html_writer::start_div('wc-search-wrap');
echo html_writer::empty_tag('input', [
    'type' => 'text', 'id' => 'wc-export-course-search',
    'autocomplete' => 'off', 'placeholder' => $str['search_placeholder'],
]);
echo html_writer::div('', 'wc-dropdown', ['id' => 'wc-export-course-dropdown']);
echo html_writer::end_div();
echo html_writer::div('', 'wc-hidden', ['id' => 'wc-export-course-selected']);
echo html_writer::end_div();

echo html_writer::start_div('wc-field wc-hidden', ['id' => 'wc-export-wiki-field']);
echo html_writer::tag('label', $str['select_wiki']);
echo html_writer::div('', 'wc-wiki-list', ['id' => 'wc-export-wiki-list']);
echo html_writer::end_div();

echo html_writer::start_div('wc-actions wc-hidden', ['id' => 'wc-export-actions']);
echo html_writer::tag('button', $str['btn_export'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-warning', 'id' => 'wc-btn-export']);
echo html_writer::tag('button', $str['btn_copy_json'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-secondary wc-hidden', 'id' => 'wc-btn-copy-json']);
echo html_writer::tag('button', $str['btn_download_json'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-secondary wc-hidden', 'id' => 'wc-btn-download-json']);
echo html_writer::tag('button', $str['btn_use_exported'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-primary wc-hidden', 'id' => 'wc-btn-use-exported']);
echo html_writer::end_div();

echo html_writer::start_div('wc-hidden', ['id' => 'wc-export-result', 'style' => 'margin-top:16px']);
echo html_writer::tag('textarea', '', ['id' => 'wc-export-json', 'readonly' => 'readonly']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div(); // End of tab-export.

echo html_writer::end_div(); // End of wc-container.

// Confirmation dialog.
echo html_writer::start_div('wc-confirm-overlay', ['id' => 'wc-confirm-overlay']);
echo html_writer::start_div('wc-confirm-box');
echo html_writer::tag('h4', $str['confirm_title']);
echo html_writer::tag('p', '', ['id' => 'wc-confirm-message']);
echo html_writer::start_div('wc-confirm-actions');
echo html_writer::tag('button', $str['cancel'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-secondary', 'id' => 'wc-confirm-cancel']);
echo html_writer::tag('button', $str['btn_create'],
    ['type' => 'button', 'class' => 'wc-btn wc-btn-success', 'id' => 'wc-confirm-ok']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// JS config block — all values JSON-encoded, no raw PHP in JS string literals.
echo '<script type="text/javascript">' . "\n";
echo 'var WC_CFG = {' . "\n";
echo '    strings:   ' . $str['jsstrings'] . ',' . "\n";
echo '    ajaxUrl:   ' . $str['ajaxurl'] . ',' . "\n";
echo '    createUrl: ' . $str['createurl'] . ',' . "\n";
echo '    sesskey:   ' . $str['sesskey'] . "\n";
echo '};' . "\n";
echo '</script>' . "\n";

// Main JS controller (fully static — no PHP interpolation needed).
echo <<<ENDOFJS
<script type="text/javascript">
/**
 * Wiki Creator - front-end controller.
 *
 * Handles course search, wiki/group selection, preview and export.
 * Every user-visible string comes from WC_CFG.strings (PHP lang files).
 */
(function() {
    "use strict";

    /* Aliases. */
    var S   = WC_CFG.strings;
    var CFG = WC_CFG;

    /* State. */
    var state = {
        courseid: null, coursename: '',
        wikiid: null,   wikiname: '',
        selectedGroups: [],
        exportCourseid: null, exportWikiid: null, exportData: null
    };

    /* DOM helpers. */
    function byId(id) { return document.getElementById(id); }
    function show(id) { byId(id).classList.remove('wc-hidden'); }
    function hide(id) { byId(id).classList.add('wc-hidden'); }

    /**
     * Escape a string for safe HTML insertion.
     *
     * @param {string} str Raw string.
     * @return {string} Entity-escaped string.
     */
    function esc(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str || ''));
        return d.innerHTML;
    }

    /**
     * Perform an AJAX GET to the plugin endpoint.
     *
     * @param {Object}   params   Key/value query parameters.
     * @param {Function} callback Receives the parsed JSON response.
     */
    function ajax(params, callback) {
        params.sesskey = CFG.sesskey;
        var qs = Object.keys(params).map(function(k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);
        }).join('&');
        var xhr = new XMLHttpRequest();
        xhr.open('GET', CFG.ajaxUrl + '?' + qs);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status !== 200) {
                console.error('[wikicreator] HTTP ' + xhr.status, xhr.responseText);
                callback({error: 'HTTP ' + xhr.status});
                return;
            }
            try { callback(JSON.parse(xhr.responseText)); }
            catch (e) {
                console.error('[wikicreator] JSON parse', e);
                callback({error: 'Parse error'});
            }
        };
        xhr.onerror = function() { callback({error: 'Network error'}); };
        xhr.send();
    }

    /* Tabs. */
    document.querySelectorAll('.wc-tab').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.wc-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.wc-tab-content').forEach(function(c) { c.classList.remove('active'); });
            tab.classList.add('active');
            byId('tab-' + tab.getAttribute('data-tab')).classList.add('active');
        });
    });

    /**
     * Build a dropdown item for a course result.
     *
     * @param {Object} c Course object {id, fullname, shortname}.
     * @return {HTMLElement}
     */
    function buildCourseItem(c) {
        var item = document.createElement('div');
        item.className = 'wc-dropdown-item';
        item.innerHTML = '<span class="wc-item-fullname">' + esc(c.fullname) + '</span>'
            + '<span class="wc-item-meta">'
            + esc(S.course_label_short) + ' ' + esc(c.shortname)
            + ' &mdash; ' + esc(S.course_label_id) + ' ' + c.id
            + '</span>';
        return item;
    }

    /**
     * Build the selected-course chip (fullname + shortname + ID).
     *
     * @param {Object} c Course object.
     * @return {string} HTML string.
     */
    function buildCourseChip(c) {
        return '<span class="wc-chip">'
            + esc(c.fullname)
            + ' <span class="wc-chip-sub">(' + esc(c.shortname)
            + ' &mdash; ' + esc(S.course_label_id) + ' ' + c.id + ')</span>'
            + ' <span class="wc-remove" title="' + esc(S.change) + '">&times;</span>'
            + '</span>';
    }

    /**
     * Wire up an autocomplete course search on the given DOM elements.
     *
     * @param {string}   inputId    ID of the text input.
     * @param {string}   dropdownId ID of the dropdown container.
     * @param {string}   selectedId ID of the selected-chip container.
     * @param {Function} onSelect   Callback(courseId, courseName).
     */
    function setupCourseSearch(inputId, dropdownId, selectedId, onSelect) {
        var input    = byId(inputId);
        var dropdown = byId(dropdownId);
        var selDiv   = byId(selectedId);
        var timer    = null;

        input.addEventListener('input', function() {
            clearTimeout(timer);
            var q = input.value.trim();
            if (q.length < 2) { dropdown.style.display = 'none'; return; }
            timer = setTimeout(function() {
                ajax({action: 'searchcourses', q: q}, function(data) {
                    dropdown.innerHTML = '';
                    if (data.error || !data.results || data.results.length === 0) {
                        var empty = document.createElement('div');
                        empty.className = 'wc-dropdown-item';
                        empty.style.color = '#6c757d';
                        empty.style.cursor = 'default';
                        empty.textContent = S.no_results;
                        dropdown.appendChild(empty);
                        dropdown.style.display = 'block';
                        return;
                    }
                    data.results.forEach(function(c) {
                        var item = buildCourseItem(c);
                        item.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            dropdown.style.display = 'none';
                            input.value = '';
                            input.style.display = 'none';
                            selDiv.innerHTML = buildCourseChip(c);
                            selDiv.classList.remove('wc-hidden');
                            selDiv.querySelector('.wc-remove').addEventListener('click', function() {
                                selDiv.classList.add('wc-hidden');
                                input.style.display = '';
                                input.value = '';
                                input.focus();
                                onSelect(null, '');
                            });
                            onSelect(c.id, c.fullname);
                        });
                        dropdown.appendChild(item);
                    });
                    dropdown.style.display = 'block';
                });
            }, 300);
        });
        input.addEventListener('blur', function() {
            setTimeout(function() { dropdown.style.display = 'none'; }, 250);
        });
        input.addEventListener('focus', function() {
            if (dropdown.children.length && input.value.trim().length >= 2) {
                dropdown.style.display = 'block';
            }
        });
    }

    /**
     * Build a clickable wiki card element.
     *
     * @param {Object}   w       Wiki object {id, name, wikimode}.
     * @param {Function} onClick Callback when the card is clicked.
     * @return {HTMLElement}
     */
    function buildWikiItem(w, onClick) {
        var div = document.createElement('div');
        div.className = 'wc-wiki-item';
        div.innerHTML = '<span class="wc-wiki-name">' + esc(w.name) + '</span>'
            + '<span class="wc-wiki-meta">'
            + esc(S.wiki_label_id) + ' ' + w.id
            + ' &mdash; ' + esc(S.wiki_label_mode) + ' ' + esc(w.wikimode)
            + '</span>';
        div.addEventListener('click', function() { onClick(div, w); });
        return div;
    }

    /* CREATE TAB. */

    setupCourseSearch(
        'wc-course-search', 'wc-course-dropdown', 'wc-course-selected',
        function(courseid, name) {
            state.courseid = courseid;
            state.coursename = name;
            state.wikiid = null;
            state.selectedGroups = [];
            hide('wc-group-section'); hide('wc-pages-section'); hide('wc-action-section');
            if (!courseid) { hide('wc-wiki-section'); return; }
            show('wc-wiki-section');
            var wl = byId('wc-wiki-list');
            wl.innerHTML = '<div class="text-muted">' + esc(S.loading) + '</div>';
            ajax({action: 'getwikis', courseid: courseid}, function(data) {
                wl.innerHTML = '';
                if (!data.results || data.results.length === 0) {
                    wl.innerHTML = '<div class="text-muted">' + esc(S.no_wiki_found) + '</div>';
                    return;
                }
                data.results.forEach(function(w) {
                    wl.appendChild(buildWikiItem(w, function(div, wiki) {
                        wl.querySelectorAll('.wc-wiki-item').forEach(function(el) {
                            el.classList.remove('selected');
                        });
                        div.classList.add('selected');
                        state.wikiid = wiki.id;
                        state.wikiname = wiki.name;
                        loadGroups(courseid);
                    }));
                });
            });
        }
    );

    /**
     * Load groups for a given course and populate the selection chips.
     *
     * @param {number} courseid The course ID.
     */
    function loadGroups(courseid) {
        show('wc-group-section');
        var gg = byId('wc-groups-grid');
        gg.innerHTML = '<div class="text-muted">' + esc(S.loading) + '</div>';
        byId('wc-group-actions').style.display = 'none';
        state.selectedGroups = [];
        hide('wc-pages-section'); hide('wc-action-section');
        ajax({action: 'getgroups', courseid: courseid}, function(data) {
            gg.innerHTML = '';
            if (!data.results || data.results.length === 0) {
                gg.innerHTML = '<div class="text-muted">' + esc(S.no_groups_found) + '</div>';
                return;
            }
            byId('wc-group-actions').style.display = 'block';
            data.results.forEach(function(g) {
                var chip = document.createElement('span');
                chip.className = 'wc-group-chip';
                chip.textContent = g.name + ' (' + S.group_label_id + ' ' + g.id + ')';
                chip.setAttribute('data-id', g.id);
                chip.addEventListener('click', function() {
                    chip.classList.toggle('selected');
                    syncGroups();
                });
                gg.appendChild(chip);
            });
        });
    }

    /** Synchronise state.selectedGroups with the DOM and toggle next steps. */
    function syncGroups() {
        state.selectedGroups = [];
        byId('wc-groups-grid').querySelectorAll('.wc-group-chip.selected').forEach(function(c) {
            state.selectedGroups.push(c.getAttribute('data-id'));
        });
        if (state.selectedGroups.length > 0) {
            show('wc-pages-section'); show('wc-action-section');
        } else {
            hide('wc-pages-section'); hide('wc-action-section');
        }
    }

    byId('wc-select-all').addEventListener('click', function(e) {
        e.preventDefault();
        byId('wc-groups-grid').querySelectorAll('.wc-group-chip').forEach(function(c) {
            c.classList.add('selected');
        });
        syncGroups();
    });
    byId('wc-select-none').addEventListener('click', function(e) {
        e.preventDefault();
        byId('wc-groups-grid').querySelectorAll('.wc-group-chip').forEach(function(c) {
            c.classList.remove('selected');
        });
        syncGroups();
    });

    /* JSON live syntax check. */
    byId('wc-pages-json').addEventListener('input', function() {
        var err = byId('wc-json-error');
        try {
            JSON.parse(this.value);
            err.style.display = 'none';
            this.style.borderColor = '#ced4da';
        } catch (e) {
            err.textContent = S.json_validation + ' ' + e.message;
            err.style.display = 'block';
            this.style.borderColor = '#dc3545';
        }
        byId('wc-json-status').classList.add('wc-hidden');
    });

    /**
     * Validate the JSON string for syntax and structural correctness.
     *
     * @param  {string} raw The raw JSON string.
     * @return {Object} Result with parsed, syntaxError, errors[], warnings[], pageCount.
     */
    function validateJson(raw) {
        var result = {parsed: null, syntaxError: null, errors: [], warnings: [], pageCount: 0};
        var obj;
        try { obj = JSON.parse(raw); }
        catch (e) { result.syntaxError = e.message; return result; }
        if (Array.isArray(obj) || typeof obj !== 'object' || obj === null) {
            result.errors.push(S.json_must_be_object);
            return result;
        }
        var keys = Object.keys(obj);
        result.pageCount = keys.length;
        keys.forEach(function(k) {
            if (k.trim() === '') {
                result.errors.push(S.json_empty_key);
            }
            if (typeof obj[k] !== 'string') {
                result.errors.push(S.json_value_not_string.replace('{title}', k));
            } else if (!/<[a-z][\s\S]*>/i.test(obj[k])) {
                result.warnings.push(S.json_no_html_warning.replace('{title}', k));
            }
        });
        result.parsed = obj;
        return result;
    }

    /* Full validation button. */
    byId('wc-btn-validate-json').addEventListener('click', function() {
        var status = byId('wc-json-status');
        var v = validateJson(byId('wc-pages-json').value);
        if (v.syntaxError) {
            status.className = 'wc-json-status invalid';
            status.innerHTML = '<strong>' + esc(S.json_validation) + '</strong> ' + esc(v.syntaxError);
            status.classList.remove('wc-hidden');
            return;
        }
        if (v.errors.length > 0) {
            status.className = 'wc-json-status invalid';
            status.innerHTML = '<strong>' + esc(S.json_validation) + '</strong><ul>'
                + v.errors.map(function(e) { return '<li>' + esc(e) + '</li>'; }).join('')
                + '</ul>';
            status.classList.remove('wc-hidden');
            return;
        }
        var h = '<strong>&#10003; ' + esc(S.json_valid) + '</strong> &mdash; '
            + esc(S.json_page_count.replace('{n}', v.pageCount));
        if (v.warnings.length > 0) {
            status.className = 'wc-json-status warnings';
            h += '<ul>' + v.warnings.map(function(w) { return '<li>' + esc(w) + '</li>'; }).join('') + '</ul>';
        } else {
            status.className = 'wc-json-status valid';
        }
        status.innerHTML = h;
        status.classList.remove('wc-hidden');
    });

    /* Preview. */
    byId('wc-btn-preview').addEventListener('click', function() {
        var btn = this;
        var v = validateJson(byId('wc-pages-json').value);
        if (v.syntaxError || v.errors.length > 0) {
            byId('wc-btn-validate-json').click();
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<span class="wc-spinner"></span> ' + esc(S.loading);
        var box = byId('wc-preview-box');
        box.innerHTML = '';
        var parsedPages = v.parsed;
        ajax({
            action:    'preview',
            wikiid:    state.wikiid,
            pages:     byId('wc-pages-json').value,
            groups:    state.selectedGroups.join(','),
            useprefix: byId('wc-useprefix').checked ? 1 : 0
        }, function(data) {
            btn.disabled = false;
            btn.textContent = S.btn_preview;
            if (data.error) {
                box.innerHTML = '<div class="alert alert-danger">' + esc(data.error) + '</div>';
                return;
            }
            var h = '<h4>' + esc(S.preview_for) + ' ' + esc(data.wiki_name) + '</h4>'
                + '<p class="text-muted">' + esc(S.total_pages_preview) + ' : ' + data.total_pages + '</p>';
            if (data.errors && data.errors.length) {
                h += '<div class="alert alert-warning">' + data.errors.map(esc).join('<br>') + '</div>';
            }
            var renderId = 0;
            data.preview.forEach(function(grp) {
                h += '<div class="wc-preview-group">'
                    + '<div class="wc-preview-group-header">'
                    + esc(grp.groupname) + ' (' + esc(S.group_label_id) + ' ' + grp.groupid + ')';
                if (!grp.subwiki_exists) {
                    h += ' &mdash; <small style="color:#0d6efd">' + esc(S.subwiki_will_create) + '</small>';
                }
                h += '</div>';
                grp.pages.forEach(function(pg) {
                    var rid = 'wc-render-' + (renderId++);
                    h += '<div class="wc-preview-page">';
                    h += pg.already_exists
                        ? '<span class="wc-badge-exists">' + esc(S.badge_exists) + '</span> '
                        : '<span class="wc-badge-new">' + esc(S.badge_new) + '</span> ';
                    h += '<strong>' + esc(pg.prefix + pg.title) + '</strong>';
                    if (pg.content_preview) {
                        h += ' <span class="wc-preview-content">' + esc(pg.content_preview) + '</span>';
                    }
                    h += '<span class="wc-render-toggle" data-rid="' + rid
                        + '" data-title="' + esc(pg.title) + '">'
                        + '&#9654; ' + esc(S.render_show) + '</span>';
                    h += '</div>';
                    h += '<div class="wc-render-panel wc-hidden" id="' + rid + '"></div>';
                });
                h += '</div>';
            });
            h += '<div class="wc-actions" style="margin-top:16px">'
                + '<button type="button" class="wc-btn wc-btn-success" id="wc-btn-create-go">'
                + esc(byId('wc-confirm-ok').textContent.trim())
                + '</button></div>';
            box.innerHTML = h;
            box.querySelectorAll('.wc-render-toggle').forEach(function(link) {
                link.addEventListener('click', function() {
                    var panel = byId(link.getAttribute('data-rid'));
                    var title = link.getAttribute('data-title');
                    var isOpen = !panel.classList.contains('wc-hidden');
                    if (isOpen) {
                        panel.classList.add('wc-hidden');
                        panel.innerHTML = '';
                        link.innerHTML = '&#9654; ' + esc(S.render_show);
                    } else {
                        var content = (parsedPages && parsedPages[title]) ? parsedPages[title] : '';
                        var prefix = byId('wc-useprefix').checked && data.preview[0]
                            ? '<div style="font-size:20px;font-weight:bold;margin-bottom:10px">'
                              + esc(data.preview[0].groupname) + '</div>'
                            : '';
                        var srcdoc = '<!DOCTYPE html><html><head><meta charset="utf-8">'
                            + '<style>body{font-family:sans-serif;font-size:14px;'
                            + 'padding:12px;margin:0;color:#333}'
                            + ' img{max-width:100%} table{border-collapse:collapse;width:100%}'
                            + ' td,th{border:1px solid #dee2e6;padding:6px 8px}</style>'
                            + '</head><body>' + prefix + content + '</body></html>';
                        var iframe = document.createElement('iframe');
                        iframe.setAttribute('sandbox', 'allow-same-origin');
                        iframe.srcdoc = srcdoc;
                        iframe.onload = function() {
                            try {
                                var ifrH = iframe.contentDocument.body.scrollHeight + 24;
                                iframe.style.height = Math.min(Math.max(ifrH, 60), 500) + 'px';
                            } catch (e) { iframe.style.height = '200px'; }
                        };
                        panel.innerHTML = '';
                        panel.appendChild(iframe);
                        panel.classList.remove('wc-hidden');
                        link.innerHTML = '&#9660; ' + esc(S.render_hide);
                    }
                });
            });
            byId('wc-btn-create-go').addEventListener('click', function() { showConfirm(data); });
        });
    });

    /**
     * Show the creation confirmation overlay with dynamic counts.
     *
     * @param {Object} previewData Preview response from AJAX.
     */
    function showConfirm(previewData) {
        var newP = 0, skipP = 0;
        previewData.preview.forEach(function(grp) {
            grp.pages.forEach(function(pg) {
                if (pg.already_exists) { skipP++; } else { newP++; }
            });
        });
        var msg = S.confirm_message
            .replace('{wiki}',    '<strong>' + esc(previewData.wiki_name) + '</strong>')
            .replace('{groups}',  '<strong>' + previewData.preview.length + '</strong>')
            .replace('{new}',     '<strong>' + newP + '</strong>')
            .replace('{skipped}', '<strong>' + skipP + '</strong>');
        byId('wc-confirm-message').innerHTML = msg;
        byId('wc-confirm-overlay').style.display = 'flex';
    }

    byId('wc-confirm-cancel').addEventListener('click', function() {
        byId('wc-confirm-overlay').style.display = 'none';
    });
    byId('wc-confirm-overlay').addEventListener('click', function(e) {
        if (e.target === this) { this.style.display = 'none'; }
    });
    byId('wc-confirm-ok').addEventListener('click', function() {
        byId('wc-confirm-overlay').style.display = 'none';
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = CFG.createUrl;
        var fields = {
            sesskey:   CFG.sesskey,
            wikiid:    state.wikiid,
            pages:     byId('wc-pages-json').value,
            groups:    state.selectedGroups.join(','),
            useprefix: byId('wc-useprefix').checked ? '1' : '0',
            confirm:   '1'
        };
        Object.keys(fields).forEach(function(k) {
            var inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = k; inp.value = fields[k];
            form.appendChild(inp);
        });
        document.body.appendChild(form);
        form.submit();
    });

    /* EXPORT TAB. */

    setupCourseSearch(
        'wc-export-course-search', 'wc-export-course-dropdown', 'wc-export-course-selected',
        function(courseid) {
            state.exportCourseid = courseid;
            state.exportWikiid = null;
            state.exportData = null;
            hide('wc-export-actions'); hide('wc-export-result');
            hide('wc-btn-copy-json'); hide('wc-btn-download-json'); hide('wc-btn-use-exported');
            if (!courseid) { hide('wc-export-wiki-field'); return; }
            show('wc-export-wiki-field');
            var wl = byId('wc-export-wiki-list');
            wl.innerHTML = '<div class="text-muted">' + esc(S.loading) + '</div>';
            ajax({action: 'getwikis', courseid: courseid}, function(data) {
                wl.innerHTML = '';
                if (!data.results || data.results.length === 0) {
                    wl.innerHTML = '<div class="text-muted">' + esc(S.no_wiki_found) + '</div>';
                    return;
                }
                data.results.forEach(function(w) {
                    wl.appendChild(buildWikiItem(w, function(div, wiki) {
                        wl.querySelectorAll('.wc-wiki-item').forEach(function(el) {
                            el.classList.remove('selected');
                        });
                        div.classList.add('selected');
                        state.exportWikiid = wiki.id;
                        show('wc-export-actions');
                        hide('wc-export-result');
                        hide('wc-btn-copy-json');
                        hide('wc-btn-download-json');
                        hide('wc-btn-use-exported');
                    }));
                });
            });
        }
    );

    byId('wc-btn-export').addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="wc-spinner"></span> ' + esc(S.exporting);
        ajax({action: 'exportwiki', wikiid: state.exportWikiid}, function(data) {
            btn.disabled = false;
            btn.textContent = S.btn_export;
            if (data.error) { alert(data.error); return; }
            state.exportData = data;
            byId('wc-export-json').value = JSON.stringify(data, null, 2);
            show('wc-export-result');
            show('wc-btn-copy-json');
            show('wc-btn-download-json');
            show('wc-btn-use-exported');
        });
    });

    byId('wc-btn-copy-json').addEventListener('click', function() {
        var ta = byId('wc-export-json');
        ta.select();
        document.execCommand('copy');
        var self = this;
        self.textContent = S.copied;
        setTimeout(function() { self.textContent = S.btn_copy_json; }, 2000);
    });

    byId('wc-btn-download-json').addEventListener('click', function() {
        var blob = new Blob([byId('wc-export-json').value], {type: 'application/json'});
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'wiki_export_' + state.exportWikiid + '.json';
        a.click();
        URL.revokeObjectURL(a.href);
    });

    byId('wc-btn-use-exported').addEventListener('click', function() {
        if (!state.exportData || !state.exportData.subwikis) { return; }
        var pages = {};
        for (var i = 0; i < state.exportData.subwikis.length; i++) {
            var sw = state.exportData.subwikis[i];
            if (sw.pages && Object.keys(sw.pages).length > 0) { pages = sw.pages; break; }
        }
        byId('wc-pages-json').value = JSON.stringify(pages, null, 2);
        document.querySelector('.wc-tab[data-tab="create"]').click();
    });

})();
</script>
ENDOFJS;

echo $OUTPUT->footer();
