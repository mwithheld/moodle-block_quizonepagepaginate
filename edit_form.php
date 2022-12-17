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
 * IntegrityAdvocate block per-instance configuration form definition.
 *
 * @package    block_integrityadvocate
 * @copyright  IntegrityAdvocate.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use block_quizonepagepaginate\Utility as qopp_u;
require_once(__DIR__ . '/lib.php');

\defined('MOODLE_INTERNAL') || die;

//require_once($CFG->dirroot . '/blocks/integrityadvocate/lib.php');

/**
 * IntegrityAdvocate per-instance block config form class.
 *
 * @copyright IntegrityAdvocate.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quizonepagepaginate_edit_form extends block_edit_form
{

    /**
     * Overridden to create any form fields specific to this type of block.
     * We can't add a type check here without causing a warning b/c the parent class does not have the type check.
     *
     * Note: Do not add a type declaration MoodleQuickForm $mform b/c it causes a...
     *       "Warning: Declaration of block_integrityadvocate_edit_form::specific_definition(MoodleQuickForm $mform) should be compatible with block_edit_form::specific_definition($mform)"
     *
     * @param \stdClass|MoodleQuickForm $mform the form being built.
     */
    protected function specific_definition($mform)
    {
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
    protected function specific_definition_custom(MoodleQuickForm $mform)
    {
        $parentcontext = context::instance_by_id($this->block->instance->parentcontextid);
        $mform->addElement('static', 'topnote', get_string('config_topnote', \QUIZONEPAGEPAGINATE_BLOCK_NAME), get_string('config_topnote_help', \QUIZONEPAGEPAGINATE_BLOCK_NAME));

        $elt = $mform->createElement('select', 'questionsperpage', get_string('newpage', 'quiz'), quiz_questions_per_page_options(), array('id' => 'id_questionsperpage'));
        if(isset($mform->questionsperpage)) {
            $mform->setDefault('questionsperpage', $mform->questionsperpage);
        }
        $mform->addElement($elt);

        $mform->addElement('static', 'blockversion', get_string('config_blockversion', \QUIZONEPAGEPAGINATE_BLOCK_NAME), get_config(\QUIZONEPAGEPAGINATE_BLOCK_NAME, 'version'));
    }
}
