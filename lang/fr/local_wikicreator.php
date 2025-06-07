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
 * Contains all French language strings used by the Wiki Creator plugin,
 * including those for settings, interface labels, and messages.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['create_pages']     = 'Créer les pages du wiki';
$string['group_not_found'] = 'Groupe ID {$a} inexistant.';
$string['invalid_page_title'] = 'Titre de page invalide pour le groupe {$a}.';
$string['invalid_settings'] = 'Les paramètres ne sont pas valides. Veuillez vérifier la configuration.';
$string['invalid_wikiid'] = 'Wiki ID invalide.';
$string['json_error'] = 'Erreur de décodage JSON : {$a}';
$string['no_pages_defined'] = 'Aucune page définie dans la configuration.';
$string['no_valid_group'] = 'Aucun groupe valide trouvé.';
$string['page_creation_error'] = 'Erreur création page "{$a}" pour groupe {$b} : {$c}';
$string['pluginname']       = 'Créateur de Wiki';
$string['settings_groups']  = 'IDs des groupes (séparés par une virgule)';
$string['settings_pages']   = 'Pages (format JSON : {"Titre de la page": "<p>Contenu HTML</p>", ...})';
$string['settings_wikiid']  = 'ID du Wiki';
$string['subwiki_creation_error'] = 'Erreur création subwiki pour groupe {$a} : {$b}';
$string['success_message'] = 'Opération réussie : les pages wiki ont été créées.';
$string['summary'] = '{$a->created} page(s) créée(s), {$a->skipped} page(s) ignorée(s) car déjà existantes.';
$string['usegroupprefix'] = 'Utiliser le préfixe de groupe';
$string['usegroupprefix_desc'] = 'Si coché, le nom du groupe sera ajouté automatiquement en préfixe (avec un code HTML prédéfini) à chaque page créée.';
$string['version_creation_error'] = 'Erreur création version pour "{$a}" (groupe {$b}) : {$c}';
$string['wikicreator'] = 'Créateur de pages Wiki';
