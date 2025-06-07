# Wiki Creator

**Version:** v2025021700 (17/02/2025)  
**Required Moodle version:** 3.11 or higher  
**Author:** To be specified  
**License:** GNU GPL v3+

## Overview
The **Wiki Creator** plugin allows administrators to bulk-create pages in an existing Moodle wiki, based on:
- The wiki ID  
- A JSON object defining page titles and HTML content  
- A list of group IDs

It also provides the option to automatically add a prefix corresponding to each group's name at the top of the page.

## Requirements
- Moodle 3.11 or higher  
- PHP 7.x or higher  
- Administrator permissions to access settings and create pages

## Installation
1. Copy the `wikicreator` folder into the `local/` directory of your Moodle installation.  
2. Log in to Moodle as an administrator and trigger the plugin upgrade (Site administration > Notifications).  
3. Verify that **Wiki Creator** appears in the “Local plugins” section.

## Usage

> **WARNING:** Before using, you must create a wiki (forced HTML format + separate groups) and a first page for each group.

1. Go to **Site administration > Plugins > Local plugins > Wiki Creator**.  
2. Enter:
   - **Wiki ID**: numeric ID of the target wiki (in the wiki URL, look for wid=XXX)
   - **Pages**: JSON with page titles and HTML content  
   - **Group IDs**: list of group IDs (in the group-specific wiki URL, look for group=XXX)
   - **Use group prefix**: check to enable group prefix – the group name will appear at the top of each page
3. Save the settings.  
4. Visit `yourmoodle/local/wikicreator/index.php`  
5. The plugin will display a detailed success or error report, indicating how many pages were created, skipped, or if any errors occurred (invalid configuration, bad JSON, missing groups, etc.).

## Language support

- The plugin supports English by default.  
- You can add more languages by creating files in `local/wikicreator/lang/` (for example, `fr` for French).

## GDPR and Security

- **Only Moodle site administrators** can access and use this plugin.
- **Data Privacy:** The content created via this plugin is the responsibility of the administrator. **Do not include personal or sensitive data** in the generated pages, in accordance with GDPR or your local privacy policy.
- All content inserted into wiki pages is sanitized to prevent security risks (XSS, etc.).

## Changelog
- **v2025021700 (17/02/2025)**: Initial release.

## License
This plugin is distributed under the **GNU GPL v3+** license.
