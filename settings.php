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
 * Settings registration for the local_wikicreator plugin.
 *
 * Registers an external admin page so that manage.php appears directly
 * in the Moodle admin tree under "Local plugins", with no intermediate
 * settings page. Navigating to the plugin entry opens the management
 * interface immediately.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_wikicreator',
        get_string('pluginname', 'local_wikicreator'),
        new moodle_url('/local/wikicreator/manage.php'),
        'moodle/site:config'
    ));

    // Prevent Moodle from creating an empty settings page for this plugin.
    $settings = null;
}
