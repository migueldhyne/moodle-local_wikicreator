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
 * French language strings for the local_wikicreator plugin.
 *
 * @package   local_wikicreator
 * @copyright 2025, Miguël Dhyne <miguel.dhyne@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['badge_exists']          = 'EXISTE';
$string['badge_new']             = 'NOUVEAU';
$string['btn_back_manage']       = 'Retour a la gestion';
$string['btn_copy_json']         = 'Copier le JSON';
$string['btn_create']            = 'Creer les pages';
$string['btn_download_json']     = 'Telecharger le JSON';
$string['btn_export']            = 'Exporter en JSON';
$string['btn_open_manage']       = 'Ouvrir l\'interface de gestion';
$string['btn_preview']           = 'Previsualiser';
$string['btn_run_creation']      = 'Oui, creer les pages';
$string['btn_use_exported']      = 'Utiliser dans l\'onglet creation';
$string['btn_validate_json']     = 'Valider le JSON';
$string['cancel']                = 'Annuler';
$string['change']                = 'Changer';
$string['confirm_message']       = 'Vous etes sur le point de creer des pages dans le wiki {wiki} pour {groups} groupe(s). {new} nouvelle(s) page(s) seront creee(s), {skipped} seront ignoree(s) (existent deja).';
$string['confirm_run_legacy']    = 'Vous etes sur le point de creer des pages dans le wiki <strong>{$a->wiki}</strong> : {$a->pages} page(s) x {$a->groups} groupe(s). Etes-vous sur(e) ?';
$string['confirm_title']         = 'Confirmer la creation';
$string['copied']                = 'Copie !';
$string['course_label_id']       = 'ID :';
$string['course_label_short']    = 'Nom abrege :';
$string['create_pages']          = 'Creer les pages du wiki';
$string['export_info']           = 'Selectionnez un cours et un wiki pour extraire toutes ses pages en JSON. Vous pourrez ensuite reutiliser ce JSON pour recreer le wiki ailleurs.';
$string['export_title']          = 'Exporter un wiki existant';
$string['exporting']             = 'Export en cours...';
$string['group_label_id']        = 'ID :';
$string['group_not_found']       = 'Le groupe ID {$a} n\'existe pas.';
$string['invalid_page_title']    = 'Titre de page invalide pour le groupe {$a}.';
$string['invalid_settings']      = 'Les parametres ne sont pas valides. Veuillez verifier la configuration.';
$string['invalid_wikiid']        = 'ID de wiki invalide.';
$string['json_empty_key']        = 'Un titre de page (cle) est vide.';
$string['json_error']            = 'Erreur de decodage JSON : {$a}';
$string['json_must_be_object']   = 'Le JSON doit etre un objet { ... }, pas un tableau.';
$string['json_no_html_warning']  = 'Le contenu de la page "{title}" ne semble pas contenir de balises HTML.';
$string['json_page_count']       = '{n} page(s) definie(s).';
$string['json_valid']            = 'La structure JSON est valide.';
$string['json_validation']       = 'Erreur JSON :';
$string['json_value_not_string'] = 'Le contenu de la page "{title}" doit etre une chaine de texte.';
$string['loading']               = 'Chargement...';
$string['manage_desc']           = 'L\'interface de gestion permet la recherche de cours, la selection de wikis et de groupes, la previsualisation et l\'export de wikis.';
$string['manage_title']          = 'Createur de Wiki - Gestion';
$string['no_groups_found']       = 'Aucun groupe trouve dans ce cours.';
$string['no_pages_defined']      = 'Aucune page definie dans la configuration.';
$string['no_results']            = 'Aucun resultat trouve.';
$string['no_valid_group']        = 'Aucun groupe valide trouve.';
$string['no_wiki_found']         = 'Aucun wiki trouve dans ce cours.';
$string['page_creation_error']   = 'Erreur lors de la creation de la page "{$a}" pour le groupe {$b} : {$c}';
$string['pages_desc']            = 'Objet JSON : cles = titres des pages, valeurs = contenu HTML.';
$string['pluginname']            = 'Createur de Wiki';
$string['preview_for']           = 'Previsualisation pour le wiki :';
$string['privacy:metadata']      = 'Le plugin Wiki Creator ne stocke aucune donnee personnelle.';
$string['reminder_step1']        = 'L\'activite wiki de destination doit deja exister dans le cours (avec le format HTML force).';
$string['reminder_step2']        = 'Si vous travaillez avec des groupes, ceux-ci doivent etre configures dans le cours et le wiki doit utiliser le mode "Groupes separes".';
$string['reminder_step3']        = 'Une premiere page doit avoir ete creee manuellement dans chaque sous-wiki de groupe afin que les sous-wikis soient correctement initialises.';
$string['reminder_text']         = 'Assurez-vous que les conditions suivantes sont remplies :';
$string['reminder_title']        = 'Avant de commencer';
$string['render_hide']           = 'Masquer le rendu';
$string['render_show']           = 'Voir le rendu';
$string['search_course']         = 'Rechercher un cours';
$string['search_course_desc']    = 'Tapez au moins 2 caracteres du nom du cours, de son nom abrege ou de son identifiant numerique.';
$string['search_placeholder']    = 'Nom du cours ou ID...';
$string['select_all']            = 'Tout selectionner';
$string['select_groups']         = 'Choisir les groupes cibles';
$string['select_groups_desc']    = 'Cliquez sur les groupes pour lesquels les pages seront creees.';
$string['select_none']           = 'Tout deselectionner';
$string['select_wiki']           = 'Choisir un wiki';
$string['select_wiki_desc']      = 'Cliquez sur le wiki que vous souhaitez alimenter.';
$string['settings_pages']        = 'Pages (JSON)';
$string['step_course']           = 'Choisir un cours';
$string['step_groups']           = 'Selectionner les groupes';
$string['step_pages']            = 'Definir les pages (JSON)';
$string['step_preview']          = 'Previsualiser et creer';
$string['step_wiki']             = 'Choisir un wiki';
$string['subwiki_creation_error'] = 'Erreur lors de la creation du sous-wiki pour le groupe {$a} : {$b}';
$string['subwiki_will_create']   = 'le sous-wiki sera cree';
$string['success_message']       = 'Operation reussie : les pages du wiki ont ete creees.';
$string['summary']               = '{$a->created} page(s) creee(s), {$a->skipped} page(s) ignoree(s) car deja existantes.';
$string['tab_create']            = 'Creer des pages';
$string['tab_export']            = 'Exporter un wiki';
$string['total_pages_preview']   = 'Total de pages a traiter';
$string['unknown_action']        = 'Action inconnue.';
$string['usegroupprefix']        = 'Utiliser le prefixe de groupe';
$string['usegroupprefix_desc']   = 'Si coche, le nom du groupe sera ajoute automatiquement en prefixe (avec un formatage HTML predefini) a chaque page creee.';
$string['version_creation_error'] = 'Erreur lors de la creation de la version pour "{$a}" (groupe {$b}) : {$c}';
$string['wiki_label_id']         = 'ID :';
$string['wiki_label_mode']       = 'Mode :';
$string['wikicreator']           = 'Createur de Wiki';
