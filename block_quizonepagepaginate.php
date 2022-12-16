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

\defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/lib.php');

use block_quizonepagepaginate\Utility as qopp_u;
 
/**
 * Definition of the accessreview block.
 *
 * @package   block_accessreview
 * @copyright 2019 Karen Holland LTS.ie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quizonepagepaginate extends block_base {
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
    public function has_config(): bool
    {
        return false;
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page.
     *
     * @return bool True if multiple instances of the block are allowed on a page.
     */
    public function instance_allow_multiple(): bool
    {
        return false;
    }

    /**
     * Do any additional initialization you may need at the time a new block instance is created.
     *
     * @return bool True if we have additional initializations.
     */
    public function instance_create()
    {
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = true;
        $debug && error_log($fxn . '::Started with configdata=' . qopp_u::var_dump($this->config, true));

        global $COURSE;

        // If this is a quiz, auto-configure the quiz to...
        $debug && error_log($fxn . "::Looking at pagetype={$this->page->pagetype}");
        if (str_starts_with($this->page->pagetype, 'mod-quiz-')) {
            // A. Show blocks during quiz attempt; and...
            $modulecontext = $this->context->get_parent_context();
            $debug && error_log($fxn . '::Got $modulecontext=' . qopp_u::var_dump($modulecontext, true));
            $modinfo = \get_fast_modinfo($COURSE, -1);
            $cm = $modinfo->get_cm($modulecontext->instanceid);
            $debug && error_log($fxn . '::Got $cm->instance=' . qopp_u::var_dump($cm->instance, true));
            global $DB;
            $record = $DB->get_record('quiz', ['id' => (int) ($cm->instance)], '*', \MUST_EXIST);
            $debug && error_log($fxn . '::Got record=' . qopp_u::var_dump($record, true));
            if ($record->showblocks < 1) {
                $record->showblocks = 1;
                $DB->update_record('quiz', $record);
            }

            // B. By default show the block on all quiz pages.
            $DB->set_field('block_instances', 'pagetypepattern', 'mod-quiz-*', ['id' => $this->instance->id]);
            $debug && error_log($fxn . '::Set DB [pagetypepattern] = mod-quiz-*');
        }

        return true;
    }
     
    /**
     * This function is called on your subclass right after an instance is loaded
     * Use this function to act on instance data just after it's loaded and before anything else is done
     * For instance: if your block will have different title's depending on location (site, course, blog, etc)
     */
    function specialization() {
        // Add a module-specific class to the body tag.  This enables the CSS that hides the quiz questions by default.
        $this->page->add_body_class('block_quizonepagepaginate');
    }
    
    /**
     * Creates the block's main content
     *
     * @return string|stdClass
     */
    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }
        
        $this->page->requires->js_call_amd('block_quizonepagepaginate/module', 'init');

        $this->content = new stdClass;
        $this->content->text = __FUNCTION__.'::Some block content here';

        return $this->content;
    }

}
