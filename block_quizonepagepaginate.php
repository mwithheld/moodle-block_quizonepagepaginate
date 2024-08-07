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
 * Definition of the block.
 *
 * @package    block_quizonepagepaginate
 * @copyright IntegrityAdvocate.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);
defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/lib.php');

use block_quizonepagepaginate\MoodleUtility as bqopp_mu;
use block_quizonepagepaginate\Utility as bqopp_u;

/**
 * The main class for this block.
 */
class block_quizonepagepaginate extends \block_base {

    /**
     * Sets the block title.
     */
    public function init(): void {
        $this->title = \get_string('pluginname', \QUIZONEPAGEPAGINATE_BLOCK_NAME);
    }

    /**
     * Defines where the block can be added.
     *
     * @return array
     */
    public function applicable_formats(): array {
        return [
            'mod-quiz' => true,
            // Unused: 'all' => false,
            // Unused: 'course-view-social' => false,
            // Unused: 'course-view-topics' => false,
            // Unused: 'course-view-weeks' => false,
            // Unused: 'course-view' => false,
            // Unused: 'course' => false,
            // Unused: 'mod' => false,
            // Unused: 'my' => false,
            // Unused: 'site-index' => false,
            // Unused: 'site' => false,
            // Unused: 'tag' => false,
            // Unused: 'user-profile' => false,
        ];
    }

    /**
     *  We have global config/settings data.
     *
     * @return bool True if we have global config/settings data.
     */
    public function has_config(): bool {
        return false;
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page.
     *
     * @return bool True if multiple instances of the block are allowed on a page.
     */
    public function instance_allow_multiple(): bool {
        return false;
    }

    /**
     * Do any additional initialization you may need at the time a new block instance is created.
     *
     * @return bool True if we have additional initializations.
     */
    public function instance_create() {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && debugging($fxn . '::Started with configdata=' . bqopp_u::var_dump($this->config, true));

        // If this is a quiz, auto-configure the quiz and this block.
        $debug && debugging($fxn . "::Looking at pagetype={$this->page->pagetype}");
        if (str_starts_with($this->page->pagetype, 'mod-quiz-')) {
            $this->autoupdate_quiz_config();
            $this->autoupdate_block_config();
        }

        return true;
    }

    /**
     * Change block config to show this block on all quiz pages.
     *
     * @return bool True if completes.
     */
    private function autoupdate_block_config(): bool {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && debugging($fxn . '::Started');

        // Show the block on all quiz pages.
        global $DB;
        $DB->set_field('block_instances', 'pagetypepattern', 'mod-quiz-*', ['id' => $this->instance->id]);
        $debug && debugging($fxn . '::Set DB [pagetypepattern] = mod-quiz-*');

        return true;
    }

    /**
     * Change quiz config to show blocks during quiz attempt; and show all quiz questions on one page.
     *
     * @return bool True if completes.
     */
    private function autoupdate_quiz_config(): bool {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && debugging($fxn . '::Started');

        global $COURSE, $DB;

        // Find the quiz attached to this block.
        $modulecontext = $this->context->get_parent_context();
        $debug && debugging($fxn . '::Got $modulecontext=' . bqopp_u::var_dump($modulecontext, true));
        $modinfo = \get_fast_modinfo($COURSE, -1);
        $cm = $modinfo->get_cm($modulecontext->instanceid);
        $debug && debugging($fxn . '::Got $cm->instance=' . bqopp_u::var_dump($cm->instance, true));

        // Get the quiz DB record.
        $record = $DB->get_record('quiz', ['id' => (int) ($cm->instance)], '*', \MUST_EXIST);
        $debug && debugging($fxn . '::Got record=' . bqopp_u::var_dump($record, true));

        // Update the quiz info.
        $changedquizconfig = true;
        if ($record->showblocks < 1) {
            $record->showblocks = 1;
            $changedquizconfig = true;
        }
        if ($record->questionsperpage !== 0) {
            $record->questionsperpage = 0;
            $changedquizconfig = true;
        }

        // If it has changed, save the updated quiz info.
        if ($changedquizconfig) {
            $DB->update_record('quiz', $record);
        }

        return true;
    }

    /**
     * This function is called on your subclass right after an instance is loaded
     * Use this function to act on instance data just after it's loaded and before anything else is done
     * For instance: if your block will have different title's depending on location (site, course, blog, etc)
     */
    public function specialization() {
        // Add a module-specific class to the body tag.  This enables the CSS that hides the quiz questions by default.
        $this->page->add_body_class('block_quizonepagepaginate');

        // If user shows/hides block in one pagetype of the quiz, also show/hide it in all the other pagetypes.
        $this->handle_block_visibility_change();
    }

    /**
     * When this block visibility changes, what other things should be done?
     *
     * @return void.
     */
    private function handle_block_visibility_change() {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && debugging($fxn . '::Started');

        if ($blockid = optional_param('bui_hideid', null, PARAM_INT)) {
            $newvisibility = 0;
        } else if ($blockid = optional_param('bui_showid', null, PARAM_INT)) {
            $newvisibility = 1;
        } else if ($blockid = optional_param('bui_editid', null, PARAM_INT)) {
            $newvisibility = optional_param('bui_visible', null, PARAM_INT);
        } else {
            $debug && debugging($fxn . '::No bui_* param found');
            return;
        }
        $debug && debugging($fxn . '::Got $blockid=' . $blockid . '; $newvisibility=' . $newvisibility);

        if (!in_array($newvisibility, [0, 1]) || $blockid !== intval($this->instance->id)) {
            $debug && debugging($fxn . '::Bad values found so skip out' . bqopp_u::var_dump($this->instance, true));
            return;
        }

        $modulecontext = $this->context->get_parent_context();
        $debug && debugging($fxn . '::Got $modulecontext=' . bqopp_u::var_dump($modulecontext, true));

        $contextid = $modulecontext->id;
        bqopp_mu::blocks_set_visibility_all_for_context_pagetypes($blockid, $contextid, (bool) $newvisibility);
    }

    /**
     * Return true if the block is configured to be visible.
     *
     * @return bool True if the block is configured to be visible.
     */
    public function is_visible(): bool {
        if (\property_exists($this, 'visible') && isset($this->visible) && \is_bool($this->visible)) {
            return $this->visible;
        }
        if (\property_exists($this->instance, 'visible') && isset($this->instance->visible) && \is_bool($this->instance->visible)) {
            return $this->instance->visible;
        }

        $parentcontext = $this->context->get_parent_context();
        return $this->visible = bqopp_mu::is_block_visibile($parentcontext->id, $this->context->id);
    }

    /**
     * Creates the block's main content
     *
     * @return string|stdClass
     */
    public function get_content() {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && debugging($fxn . '::Started with configdata=' . bqopp_u::var_dump($this->config, true));

        // If the block is configured to be Hidden, disable the functionality entirely.
        // This is just in case bc Moodle should not allow us to get here.
        if (!$this->is_visible()) {
            return '';
        }

        // Avoid Moodle generating the block content twice.
        if (isset($this->content)) {
            return $this->content;
        }

        // Run config autoupdates to force the settings.
        $this->autoupdate_quiz_config();
        $this->autoupdate_block_config();

        // Values to pass to JS.
        $paramsforjs = [];
        if (isset($this->config->questionsperpage) && is_numeric($this->config->questionsperpage)) {
            $questionsperpage = intval($this->config->questionsperpage);
        } else {
            $questionsperpage = 1;
        }
        $debug && debugging($fxn . '::Found questionsperpage=' . bqopp_u::var_dump($questionsperpage, true));
        $paramsforjs[] = $questionsperpage;

        // Add the block JS.
        $this->page->requires->js_call_amd('block_quizonepagepaginate/module', 'init', $paramsforjs);

        $this->content = new stdClass;
        $this->content->text = \get_string('defaultcontent', \QUIZONEPAGEPAGINATE_BLOCK_NAME);

        return $this->content;
    }
}
