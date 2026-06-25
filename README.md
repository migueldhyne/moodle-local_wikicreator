# Wiki Creator

**Version:** 2025062501
**Required Moodle version:** 3.11+
**Author:** Miguël Dhyne
**License:** GNU GPL v3+

---

## Overview

**Wiki Creator** lets Moodle administrators bulk-create pages in an existing
wiki activity for one or more groups — in a single, guided step.

Instead of creating pages one by one through the Moodle interface, you define
your page structure once as a JSON object (title → HTML content), pick the
target groups, preview the result, and confirm. The plugin handles the rest.

A second tab lets you **export** any existing wiki to JSON, so you can
duplicate or migrate wiki structures across courses or Moodle instances.

---

## Features

### Guided creation workflow

The management interface walks you through five steps:

1. **Course search** — type at least 2 characters of the course name, short
   name or numeric ID. The dropdown shows full name, short name and ID.
2. **Wiki picker** — choose a wiki from those found in the selected course
   (ID and mode displayed).
3. **Group selection** — click chips to select or deselect target groups;
   *Select all* / *Deselect all* shortcuts available.
4. **JSON editor** — define page titles and HTML content. A live syntax check
   highlights errors as you type; a dedicated **Validate JSON** button runs a
   deeper structural check (object type, non-empty keys, string values, HTML
   content hint).
5. **Preview and create** — before anything is written to the database, a
   full dry run shows each page per group with NEW / EXISTS badges and a
   **View render** toggle that opens an inline sandboxed iframe so you can
   see the actual HTML output. A confirmation dialog summarises the counts
   before you commit.

### Wiki export

- Select any course and any wiki.
- All pages from every sub-wiki (all groups) are extracted as structured JSON.
- Three actions available: **Copy JSON** to clipboard, **Download JSON** as a
  file, or **Use in creation tab** to inject the pages directly into the
  editor for reuse elsewhere.

### Group name prefix

An optional checkbox adds the group name as a styled HTML heading at the top
of each created page, useful for identifying group-specific content at a glance.

### Prerequisite reminder

A notice at the top of the interface reminds administrators of three
conditions that must be met before running the creation:

1. The target wiki activity must already exist in the course (HTML format forced).
2. If using groups, they must be configured in the course and the wiki must
   use *Separate groups* mode.
3. A first page must have been created manually in each group sub-wiki so that
   the sub-wikis are properly initialised in the database.

---

## Access

The plugin is registered as an **external admin page** in the Moodle admin
tree. Navigate to:

> Site administration > Plugins > Local plugins > **Wiki Creator**

This opens the management interface directly — no intermediate settings page.
Only users with the `moodle/site:config` capability can access it.

---

## Installation

1. Copy the `wikicreator` folder into `local/` at the root of your Moodle
   installation.
2. Log in as administrator and go to *Site administration > Notifications* to
   trigger the plugin installation.
3. The plugin appears immediately under *Site administration > Plugins >
   Local plugins > Wiki Creator*.

---

## JSON format

Pages are defined as a JSON object where each key is a page title and each
value is the HTML content:

```json
{
  "Home": "<p>Welcome to the wiki.</p>",
  "Guidelines": "<h2>Rules</h2><p>Please read carefully.</p>",
  "Resources": "<ul><li>Link 1</li><li>Link 2</li></ul>"
}
```

The **Validate JSON** button checks that:

- The value is a valid JSON object (not an array).
- No key (page title) is empty.
- All values are strings.
- Values appear to contain HTML (warning only if plain text is detected).

---

## Coding standards

- Single PHP block per file — no inline `<?php ?>` fragments in HTML output.
- All HTML emitted via `html_writer` or `echo <<<HEREDOC`.
- Every PHP file carries a GPL licence header and full `@package` /
  `@copyright` / `@license` docblocks.
- All user-visible strings externalised in `lang/en/` and `lang/fr/`, in
  strict alphabetical order, with no section comments.
- JavaScript receives translated strings via a `json_encode`-encoded
  dictionary (`JSON_HEX_APOS | JSON_HEX_QUOT`); no raw PHP inside JS
  string literals.
- All AJAX calls require a valid Moodle session key (`sesskey`).
- Lines stay within 132 characters (180 hard limit).

---

## GDPR

The plugin does not store any personal data. All content inserted into wiki
pages is sanitised with `clean_text()` before being written to the database.

---

## Language support

English (`lang/en/`) and French (`lang/fr/`) are included and kept in sync.
To add another language, create `lang/xx/local_wikicreator.php` using either
file as a template.

---

## Changelog

### 2025062501
- Fixed PHPCS inline comment capitalisation (`End of ...`).
- Settings page replaced by `admin_externalpage`: clicking the plugin entry
  in the admin tree opens the management interface directly.

### 2025062500
- All user-visible strings externalised to lang files (EN + FR), in strict
  alphabetical order with no section comments.
- `manage.php` rewritten as a single PHP block (`html_writer` + HEREDOC);
  no inline `<?php ?>` tags — eliminates PHPCS "Missing docblock" warnings.
- CSS reformatted to respect the 132-character line limit.
- Prerequisite reminder added at the top of the interface.
- Course dropdown now shows full name, short name and ID on two lines.
- JSON validation: live syntax check + structural validation button
  (object type, empty keys, string values, HTML content hint).
- Preview: per-page **View render** toggle with sandboxed iframe.

### 2025062400
- Interactive management interface with course search, wiki/group pickers.
- Dry-run preview with NEW / EXISTS badges and confirmation dialog.
- Wiki export to JSON (copy, download, inject into editor).
- Privacy provider namespace fixed.

### 2025021700
- Initial release.
