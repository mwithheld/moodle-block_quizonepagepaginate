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
 * Block language strings.
 *
 * @copyright  IntegrityAdvocate.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Ignore some Moodle codechecker PHPCS rules that I do not entirely agree with.
 * @tags
 * @phpcs:disable moodle.Files.LineLength.MaxExceeded
 * @phpcs:disable moodle.PHP.ForbiddenFunctions.FoundWithAlternative
 * @phpcs:disable moodle.PHP.ForbiddenFunctions.Found
 */

declare(strict_types=1);
defined('MOODLE_INTERNAL') || die;

$string['pluginname'] = 'One Page Paginate';
$string['defaultcontent'] = 'This block is enabled.<br />This block will not show to students ever, or to teachers unless <a href="https://docs.moodle.org/401/en/Course_homepage">edit mode</a> is on.<br />If you Hide the block, you disable its features.';

$string['quizonepagepaginate:addinstance'] = 'Add an One Page Paginate block';
$string['quizonepagepaginate:view'] = 'View the One Page Paginate block';

$string['config_blockversion'] = 'Version';
$string['config_topnote'] = 'Notes';
$string['config_topnote_help'] = 'This block overwrites quiz config for:<br />&nbsp;&nbsp;&bull;&nbsp;&quot;Layout &gt; New Page&quot; to &quot;All questions on one page&quot;<br />&nbsp;&nbsp;&bull;&nbsp;&quot;Appearance &gt; Show blocks during quiz attempts&quot; to &quot;Yes&quot;.<br />This block will appear on &quot;Any quiz module page&quot; regardless of the &quot;Display on page types&quot; setting below.<br />The block will not show to students ever, or to teachers unless editing mode is on. If you Hide the block, you disable its features.';
$string['config_questionsperpage'] = 'Show this many questions at a time';

$string['privacy:metadata'] = 'This block does not store any privacy-relevant data.';
