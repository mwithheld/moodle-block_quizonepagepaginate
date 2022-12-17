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
 * Per-instance block config form class.
 *
 * @copyright IntegrityAdvocate.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Ignore some Moodle codechecker PHPCS rules that I do not entirely agree with.
 * @tags
 * @phpcs:disable moodle.Files.LineLength.MaxExceeded
 * @phpcs:disable moodle.PHP.ForbiddenFunctions.FoundWithAlternative
 * @phpcs:disable moodle.PHP.ForbiddenFunctions.Found
 */

declare(strict_types=1);
defined('MOODLE_INTERNAL') || die;

use block_quizonepagepaginate\Utility as bqopp_u;

require_once(__DIR__ . '/lib.php');

class block_quizonepagepaginate_edit_form extends block_edit_form {

    /**
     * Overridden to create any form fields specific to this type of block.
     * We can't add a type check here without causing a warning b/c the parent class does not have the type check.
     *
     * Note: Do not add a type declaration MoodleQuickForm $mform b/c it causes a...
     *       "Warning: Declaration of block_integrityadvocate_edit_form::specific_definition(MoodleQuickForm $mform) should be compatible with block_edit_form::specific_definition($mform)"
     *
     * @param \stdClass|MoodleQuickForm $mform the form being built.
     */
    protected function specific_definition($mform) {
        if (!($mform instanceof MoodleQuickForm)) {
            throw new InvalidArgumentException('$mform must be an instance of MoodleQuickForm and it appears to be a ' . \gettype($mform));
        }

        // Start block specific section in config form.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $this->specific_definition_custom($mform);
    }

    /**
     * Build form fields for this block instance's settings.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function specific_definition_custom(MoodleQuickForm $mform) {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && error_log($fxn . '::Started with $mform=' . bqopp_u::var_dump($mform, true));

        $parentcontext = context::instance_by_id($this->block->instance->parentcontextid);
        $mform->addElement('static', 'topnote', get_string('config_topnote', \QUIZONEPAGEPAGINATE_BLOCK_NAME), get_string('config_topnote_help', \QUIZONEPAGEPAGINATE_BLOCK_NAME));

        $pageoptions = array();
        // Use the same number of options as quiz config, but our own wording bc the quiz config wording for this setting (e.g. "New page every 2 questions") is no longer applicable with this block active, and thus confusing.
        for ($i = 0; $i <= QUIZ_MAX_QPP_OPTION; ++$i) {
            $pageoptions[$i] = $i;
        }
        $elt = $mform->createElement('select', 'config_questionsperpage', get_string('newpage', 'quiz'), $pageoptions, array('id' => 'id_questionsperpage'));
        // Default to 1 question visible at a time.
        $mform->setDefault('config_questionsperpage', 1);
        $mform->addElement($elt);

        $mform->addElement('static', 'blockversion', get_string('config_blockversion', \QUIZONEPAGEPAGINATE_BLOCK_NAME), get_config(\QUIZONEPAGEPAGINATE_BLOCK_NAME, 'version'));
    }
}
