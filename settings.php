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
 * Settings for the local_wikicreator plugin.
 *
 * Defines the configuration options available in the Moodle administration interface.
 * These settings allow administrators to specify:
 * - The target Wiki ID
 * - A JSON object defining the wiki pages to create
 * - A list of group IDs (optional)
 * - Whether to use group name prefixes in page titles
 *
 * @package   local_wikicreator
 * @category  admin
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) { // Checks whether the user has the right to access the configuration.
    $settings = new admin_settingpage('local_wikicreator', get_string('wikicreator', 'local_wikicreator'));

    // Wiki ID field.
    $settings->add(new admin_setting_configtext(
        'local_wikicreator/wikiid',
        get_string('settings_wikiid', 'local_wikicreator'),
        '',
        '',
        PARAM_INT
    ));

    // Field for pages (in JSON).
    $settings->add(new admin_setting_configtextarea(
        'local_wikicreator/pages',
        get_string('settings_pages', 'local_wikicreator'),
        '',
        '{"Accueil": "<p>Bienvenue sur le wiki.</p>", "Page1": "<p>Contenu de la page 1</p>"}',
        PARAM_RAW
    ));

    // Field for group IDs.
    $settings->add(new admin_setting_configtext(
        'local_wikicreator/groups',
        get_string('settings_groups', 'local_wikicreator'),
        '',
        '',
        PARAM_RAW
    ));

    // Option : Use group prefix (automatically adds the group name as a prefix to each page).
    $settings->add(new admin_setting_configcheckbox(
        'local_wikicreator/usegroupprefix',
        get_string('usegroupprefix', 'local_wikicreator'),
        get_string('usegroupprefix_desc', 'local_wikicreator'),
        0
    ));

    $ADMIN->add('localplugins', $settings);
}
