<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Generic utility functions not specific to Moodle. Nothing in this class should use Moodle logic or Moodle's MUC.
 *
 * @package    block_quizonepagepaginate
 * @copyright   IntegrityAdvocate.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace block_quizonepagepaginate;

defined('MOODLE_INTERNAL') || die;

/**
 * For documentation, see the notes at the top of this file.
 */
final class Utility {
    /**
     * Wrapper around PHP empty() that also works for objects.
     * If the object has any properties it is considered not empty.
     * Unlike the language construct empty(), it will throw an error if the variable does not exist.
     *
     * @link https://stackoverflow.com/a/25320265
     * @param mixed $obj The variable to test for empty-ness.
     * @return bool true if empty; else false.
     */
    public static function is_empty($obj): bool {
        switch (true) {
            case !\is_object($obj):
                return empty($obj);
            case is_string($obj):
                return strlen($obj) < 1;
            default:
                $arr = (array) $obj;
                return empty($arr);
        }
    }

    /**
     * Just wraps print_r(), but defaults to returning as a string.  If $expression is an object that has implemented __toString() then this is used.
     *
     * @param mixed $expression <p>The expression to be printed.</p>
     * @param bool $return <p>If you would like to capture the output of <b>print_r()</b>, use the <code>return</code> parameter.
     * When this parameter is set to <b><code>TRUE</code></b>, <b>print_r()</b> will return the information rather than print it.</p>
     * @return mixed <p>If given a <code>string</code>, <code>integer</code> or <code>float</code>, the value itself will be printed.
     * If given an <code>array</code>, values will be presented in a format that shows keys and elements.
     * Similar notation is used for <code>object</code>s.</p><p>
     * When the <code>return</code> parameter is <b><code>TRUE</code></b>, this function will return a <code>string</code>.
     * Otherwise, the return value is <b><code>TRUE</code></b>.</p>
     */
    public static function var_dump($expression, bool $return = true) {
        if (self::is_empty($expression)) {
            // phpcs:ignore
            return \print_r('', $return);
        }

        // Avoid OOM errors.
        \raise_memory_limit(\MEMORY_HUGE);

        if (\is_object($expression)) {
            if (
                \property_exists($expression, 'page')
                && (\gettype($expression->page) == 'object')
                && \class_exists('moodle_page', false)
                && $expression->page instanceof \moodle_page
            ) {
                $expression->page = null;
            }
            if (\method_exists(\get_class($expression), '__toString')) {
                $expression = $expression->__toString();
            }
        }

        if (
            \is_array($expression)
            && isset($expression['page'])
            && (\gettype($expression['page']) == 'object')
            && \class_exists('moodle_page', false)
            && $expression['page'] instanceof \moodle_page
        ) {
            unset($expression['page']);
        }

        // Preg_replace prevents dying on base64-encoded images.
        // phpcs:ignore
        return \print_r(\print_r($expression, true), $return);
    }

    /**
     * Returns the count (including if zero), or -1 if not countable.
     *
     * @param mixed $var Variable to get the count for
     * @return int The count; -1 if not countable.
     */
    public static function count_if_countable($var): int {
        return \is_countable($var) ? \count($var) : -1;
    }

    /**
     * Tests if the given $value parameter is a JSON string.
     * When it is a valid JSON value, the decoded value is returned.
     * When the value is no JSON value (i.e. it was decoded already), then
     * the original value is returned.
     *
     * @link https://stackoverflow.com/a/45241792 .
     * @param mixed $value The value to evaluate for JSON-ness.
     * @param bool $asobject True to return the result as an object.
     * @return mixed The decoded result.
     */
    public static function get_data_from_maybe_json($value, $asobject = false) {
        if (is_numeric($value)) {
            return 0 + $value;
        }
        if (!is_string($value)) {
            return $value;
        }
        if (strlen($value) < 2) {
            return $value;
        }
        if ('null' === $value) {
            return null;
        }
        if ('true' === $value) {
            return true;
        }
        if ('false' === $value) {
            return false;
        }
        if ('{' != $value[0] && '[' != $value[0] && '"' != $value[0]) {
            return $value;
        }

        $jsondata = json_decode($value, $asobject);
        if (is_null($jsondata)) {
            return $value;
        }
        return $jsondata;
    }

    /**
     * Convert minutes to hh:mm format (or another you specify).
     *
     * @link https://stackoverflow.com/a/8563576 .
     * @param int $minutes How many minutes?
     * @param string $format sprintf format to convert to.
     * @return string Formatted time string.
     */
    public static function minutes_to_hours_mins(int $minutes, string $format = '%02d:%02d'): string {
        if ($minutes < 1) {
            return '';
        }

        $hours = floor($minutes / 60);
        $minutes = ($minutes % 60);
        return sprintf($format, $hours, $minutes);
    }
}
