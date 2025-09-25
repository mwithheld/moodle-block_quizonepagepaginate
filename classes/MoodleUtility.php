<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodl>e is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Utility functions not specific to this module that interact with Moodle core.
 *
 * @package    block_quizonepagepaginate
 * @copyright   IntegrityAdvocate.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace block_quizonepagepaginate;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/user/lib.php');

use block_quizonepagepaginate\Cache as bqopp_cache;
use block_quizonepagepaginate\MoodleUtility as bqopp_mu;
use block_quizonepagepaginate\Utility as bqopp_u;

/**
 * For documentation, see the notes at the top of this file.
 */
final class MoodleUtility {
    /**
     * Build a unique reproducible cache key from the given string.
     * By design the cache key becomes invalid on any upgrade in Moodle (core or plugins).
     *
     * @param string $key The string to use for the key.
     * @return string The cache key.
     */
    public static function get_cache_key(string $key): string {
        global $CFG;
        return \sha1($CFG->allversionshash . $key);
    }

    /**
     * Return whether a block is visible in the given context.
     *
     * @param int $parentcontextid The module context id
     * @param int $blockinstanceid The block instance id
     * @return bool true if the block is visible in the given context
     */
    public static function is_block_visibile(int $parentcontextid, int $blockinstanceid): bool {
        global $DB;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug = false;
        $debug && debugging($fxn . "::Started with \$parentcontextid={$parentcontextid}; \$blockinstanceid={$blockinstanceid}");

        $record = $DB->get_record('block_positions', ['blockinstanceid' => $blockinstanceid, 'contextid' => $parentcontextid], 'id,visible', \IGNORE_MULTIPLE);
        $debug && debugging($fxn . '::Got $bp_record=' . (bqopp_u::is_empty($record) ? '' : bqopp_u::var_dump($record, true)));
        if (bqopp_u::is_empty($record)) {
            // There is no block_positions record, and the default is visible.
            return true;
        }

        return (bool) $record->visible;
    }

    /**
     * Set visibility for a block instance on all its page types and positions in a given context.
     * E.g. if you hide a quiz block, you can use this to hide it on all quiz-* page types.
     *
     * @param int $blockinstanceid The block instance id, e.g. the value 60 from http://localhost:8000/mod/quiz/view.php?id=2&bui_editid=60.
     * @param int $contextid The context id to look in.
     * @param bool $newvisibility True to make the blocks visible; False to make them hidden.
     * @return void.
     */
    public static function blocks_set_visibility_all_for_context_pagetypes(int $blockinstanceid, int $contextid, bool $newvisibility): void {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . '::Started with $blockinstanceid=' . $blockinstanceid . '; $contextid=' . $contextid .
            '; $newvisibility=' . ($newvisibility ? 'true' : 'false'));

        global $DB;
        $blockinstances = $DB->get_records('block_instances', ['parentcontextid' => $contextid]);
        $debug && debugging($fxn . '::Got ' . bqopp_u::count_if_countable($blockinstances) . ' $blockinstances');

        foreach ($blockinstances as $blockinstance) {
            $debug && debugging($fxn . '::About to set block_positions visible for blockinstanceid=' . $blockinstance->id . ' in contextid=' . $contextid .
                ' to ' . ($newvisibility ? 'true' : 'false'));
            $DB->set_field('block_positions', 'visible', $newvisibility, [
                'blockinstanceid' => $blockinstance->id,
                'contextid' => $contextid,
            ]);
        }

        $debug && debugging($fxn . '::Done');
    }
}
