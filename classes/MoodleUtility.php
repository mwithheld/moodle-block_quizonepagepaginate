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
     * Return true if a course exists.
     *
     * @param int $id The course id.
     * @return bool False if no course found; else true.
     */
    public static function course_exists(int $id): bool {
        global $DB;

        $exists = false;
        try {
            $exists = $DB->record_exists('course', ['id' => $id]);
            // Ignore the empty catch.
            // phpcs:ignore
        } catch (\dml_missing_record_exception $e) {
            // Ignore these.
        }

        return $exists;
    }

    /**
     * Convert module context id to moodle module context object into if needed.
     *
     * @param int|\stdClass $modulecontextorid The context object or id to check.
     * @return \stdClass A Moodle module context object. Returns null if nothing found.
     */
    public static function get_modulecontext_as_obj($modulecontextorid): ?\context_module {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . '::Started with type(\$modulecontextorid)=' . \gettype($modulecontextorid));

        $returnthis = null;

        switch (true) {
            case \is_numeric($modulecontextorid) && intval($modulecontextorid) === clean_param($modulecontextorid, PARAM_INT):
                $cachename = bqopp_cache::PERSESSION;
                $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, \json_encode($modulecontextorid, \JSON_PARTIAL_OUTPUT_ON_ERROR)]));
                $cachedvalue = bqopp_cache::get($cachename, $cachekey);
                if (!is_null($cachedvalue)) {
                    $debug && debugging($fxn . '::Found a cached value, so return that');
                    return $cachedvalue;
                }

                // Note \context_module::instance() can return a context_module object or false.
                if ($modulecontext = \context_module::instance($modulecontextorid, \IGNORE_MISSING)) {
                    $returnthis = $modulecontext;
                } else {
                    break;
                }

                bqopp_cache::set($cachename, $cachekey, $returnthis);
                break;
            case bqopp_u::is_empty($modulecontextorid):
                // Do nothing - we will return an empty stdClass.
                break;
            case \is_object($modulecontextorid):
                // Sanity check.
                if (!$modulecontextorid instanceof \context_module) {
                    throw new \InvalidArgumentException('The input $modulecontextorid object is not of type context_module');
                }
                // It is already a Moodle module context class.
                $returnthis = $modulecontextorid;
                break;
            default:
                throw new \Exception('Expected to be passed in an int or a \context_module but got type=' . gettype($modulecontextorid));
        }

        return $returnthis;
    }

    /**
     * Convert course context id to moodle course context object into if needed.
     *
     * @param int|\stdClass $coursecontextorid The context object or id to check.
     * @return \stdClass A Moodle course context object. Returns null if nothing found.
     */
    public static function get_coursecontext_as_obj($coursecontextorid): ?\context_course {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . '::Started with type(\$coursecontextorid)=' . \gettype($coursecontextorid));

        $returnthis = null;

        switch (true) {
            case \is_numeric($coursecontextorid) && intval($coursecontextorid) === clean_param($coursecontextorid, PARAM_INT):
                $cachename = bqopp_cache::PERSESSION;
                $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, \json_encode($coursecontextorid, \JSON_PARTIAL_OUTPUT_ON_ERROR)]));
                $cachedvalue = bqopp_cache::get($cachename, $cachekey);
                if (!is_null($cachedvalue)) {
                    $debug && debugging($fxn . '::Found a cached value, so return that');
                    return $cachedvalue;
                }

                // Note \context_course::instance() can return a context_course object or false.
                if ($coursecontext = \context_course::instance($coursecontextorid, \IGNORE_MISSING)) {
                    $returnthis = $coursecontext;
                } else {
                    break;
                }

                bqopp_cache::set($cachename, $cachekey, $returnthis);
                break;
            case bqopp_u::is_empty($coursecontextorid):
                // Do nothing - we will return an empty stdClass.
                break;
            case \is_object($coursecontextorid):
                // Sanity check.
                if (!$coursecontextorid instanceof \context_course) {
                    throw new \InvalidArgumentException('The input $coursecontextorid object is not of type context_course');
                }
                // It is already a Moodle course context class.
                $returnthis = $coursecontextorid;
                break;
            default:
                throw new \Exception('Expected to be passed in an int or a \context_course but got type=' . gettype($coursecontextorid));
        }

        return $returnthis;
    }

    /**
     * Convert course id to moodle course object into if needed.
     *
     * @param int|\stdClass $course The course object or courseid to check
     * @return \stdClass A Moodle course object. Returns empty object if nothing found.
     */
    public static function get_course_as_obj($course): \stdClass {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . '::Started with type(\$course)=' . \gettype($course));

        $cachename = bqopp_cache::PERSESSION;
        $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, \json_encode($course, \JSON_PARTIAL_OUTPUT_ON_ERROR)]));
        $cachedvalue = bqopp_cache::get($cachename, $cachekey);
        if (!is_null($cachedvalue)) {
            $debug && debugging($fxn . '::Found a cached value, so return that');
            return $cachedvalue;
        }

        $returnthis = new \stdClass();
        global $DB, $SITE;

        switch (true) {
            case \is_numeric($course) && intval($course) === clean_param($course, PARAM_INT):
                try {
                    // This throws dml_exception if not found in database.
                    $returnthis = \get_course((int) $course);
                } catch (\dml_missing_record_exception $e) {
                    // We did not find the course.  Ignore it and return the 'not found' result.
                    $returnthis = new \stdClass();
                }

                break;
            case \is_string($course) && !is_numeric($course) && strlen($course) > 0:
                $courseshortname = clean_param($course, PARAM_TEXT);
                if ($courseshortname != $course) {
                    // Something is wrong - return the empty default.
                    return $returnthis;
                }
                $course = $DB->get_record('course', ['shortname' => $courseshortname], '*', IGNORE_MULTIPLE);
                if (!isset($course->id) || intval($course->id) === intval($SITE->id)) {
                    // Something is wrong - return the empty default.
                    return $returnthis;
                }

                $returnthis = $course;
                break;
            case \is_object($course) && isset($course->id) && isset($course->fullnamme) && isset($course->shortname) && isset($course->category):
                // Assume it is already a Moodle course class. No type check bc they are stdClass objects.
                $returnthis = $course;
                break;
            case bqopp_u::is_empty($course):
                // Invalid, so return the empty default.
                return $returnthis;
            default:
                throw new \Exception('Expected to be passed in an int, string, or a stdClass but got type=' . gettype($course));
        }

        bqopp_cache::set($cachename, $cachekey, $returnthis);
        return $returnthis;
    }

    /**
     * Convert userid to moodle user object into if needed.
     *
     * @param int|\stdClass $user The user object or userid to convert
     * @return \stdClass A Moodle user object. Returns empty object if nothing found or the user is marked deleted.
     */
    public static function get_user_as_obj($user): ?\stdClass {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . '::Started with type(\$user)=' . \gettype($user));

        $cachename = bqopp_cache::PERSESSION;
        $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, \json_encode($user, \JSON_PARTIAL_OUTPUT_ON_ERROR)]));
        $cachedvalue = bqopp_cache::get($cachename, $cachekey);
        if (false && !is_null($cachedvalue)) {
            $debug && debugging($fxn . '::Found a cached value, so return that');
            return $cachedvalue;
        }

        $returnthis = new \stdClass();

        switch (true) {
            case \is_numeric($user) && intval($user) === clean_param($user, PARAM_INT):
                $userarr = \user_get_users_by_id([(int) $user]);
                $debug && debugging($fxn . '::Got $userarr=' . bqopp_u::var_dump($userarr, true));
                if (empty($userarr)) {
                    return $returnthis;
                }
                $user = \array_pop($userarr);
                $debug && debugging($fxn . '::Got $user=' . bqopp_u::var_dump($user, true));

                if (isset($user->deleted) && $user->deleted) {
                    return $returnthis;
                }

                $returnthis = $user;
                break;
            case \is_object($user) && isset($user->id) && !(isset($user->deleted) && $user->deleted):
                // Assume it is already a Moodle user class. No type check bc they are stdClass objects.
                $returnthis = $user;
                break;
            case bqopp_u::is_empty($user):
                // Invalid, so return the empty default.
                return $returnthis;
            default:
                throw new \Exception('Expected to be passed in an int, string, or a stdClass but got type=' . gettype($user));
        }

        bqopp_cache::set($cachename, $cachekey, $returnthis);
        return $returnthis;
    }

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
     * Get course custom fields, optionally for only one custom field.
     *
     * @link https://docs.moodle.org/dev/Custom_fields_API .
     * @param int $courseid Courseid to get data for.
     * @param string $shortnametoreturn If non-empty, return data only for this course custom field shortname.
     * @return array <string, mixed> Custom field shortname => custom field value.
     */
    public static function get_course_metadata(int $courseid, string $shortnametoreturn = ''): array {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . "::Started with \$courseid={$courseid}; \$shortnameToReturn={$shortnametoreturn}");

        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        // This is equivalent to the line above: $handler = \core_course\customfield\course_handler::create();.
        $datas = $handler->get_instance_data($courseid);
        $debug && debugging($fxn . "::Got datas=" . bqopp_u::var_dump($datas, true));

        $returnthis = [];
        foreach ($datas as $data) {
            $debug && debugging($fxn . "::Looking at data=" . bqopp_u::var_dump($data, true));
            $shortname = $data->get_field()->get('shortname');
            if ($shortnametoreturn && $shortname != $shortnametoreturn) {
                continue;
            }

            if (empty($data->get_value())) {
                continue;
            }

            $cat = $data->get_field()->get_category()->get('name');
            $returnthis[$shortname] = $cat . ': ' . $data->get_value();

            if ($shortnametoreturn && $shortname === $shortnametoreturn) {
                break;
            }
        }

        $debug && debugging($fxn . "::About to return \$returnThis=" . bqopp_u::var_dump($returnthis, true));
        return $returnthis;
    }

    /**
     * Get the default role with the passed in name.
     * Adapted from lib/accesslib.php::get_guest_role().
     *
     * @param string $shortname The role short name.
     * @return \stdClass A Moodle role record.
     */
    public static function get_default_role(string $shortname): \stdClass {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . "::Started with \$shortname={$shortname}");

        global $CFG, $DB;

        $cachename = bqopp_cache::PERSESSION;
        $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, $shortname]));
        $cachedvalue = bqopp_cache::get($cachename, $cachekey);
        if (!is_null($cachedvalue)) {
            $debug && debugging($fxn . '::Found a cached value, so return that');
            return $cachedvalue;
        }

        $returnthis = new \stdClass();

        // Guest role is handled by lib/accesslib.php.
        if ($shortname === 'guest') {
            $debug && debugging($fxn . "::Use Moodle default handling for guest");
            // This can return false or stdClass.
            $role = get_guest_role();
            if (!empty($role)) {
                $returnthis = $role;
            }
        } else {
            // Caution: If we use shortnameroleid Moodle always returns the guest role id!  Thanks Moodle!
            $cfgvarname = "roleid_{$shortname}";
            $debug && debugging($fxn . "::Built \$cfgvarname={$cfgvarname}");

            $debug && debugging($fxn . '::About to check ' . $CFG->$cfgvarname . '=' . get_config('core', $cfgvarname));
            if (!isset($CFG->$cfgvarname)) {
                $debug && debugging($fxn . '::Found no existing $CFG var');
                if ($roles = $DB->get_records('role', ['archetype' => $shortname])) {
                    $role = array_shift($roles);   // Pick the first one.
                    $debug && debugging($fxn . "::Got role=" . bqopp_u::var_dump($role, true));
                    set_config($cfgvarname, $role->id);
                    $returnthis = $role;
                } else {
                    $debug && debugging($fxn . "::Can not find any {$shortname} role!");
                }
            } else {
                $debug && debugging($fxn . '::Found existing $CFG var with val=' . $CFG->$cfgvarname);
                if ($role = $DB->get_record('role', ['id' => $CFG->$cfgvarname])) {
                    $debug && debugging($fxn . "::Got role=" . bqopp_u::var_dump($role, true));
                    $returnthis = $role;
                } else {
                    $debug && debugging($fxn . "::Somebody is messing with the roles, remove incorrect setting and try to find a new one.");
                    set_config($cfgvarname, '');
                    $returnthis = bqopp_mu::get_default_role($shortname);
                }
            }
        }

        bqopp_cache::set($cachename, $cachekey, $returnthis);
        return $returnthis;
    }

    /**
     * Does this user have a role in some context?  False for guest and autheticated user role; True for admins.
     *
     * @param \stdClass $user The user to check.
     * @return bool True if the user has a role somewhere.
     */
    public static function user_has_nonbasic_role_somewhere(\stdClass $user): bool {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . "::Started with user->id={$user->id}");

        if (\isguestuser()) {
            return false;
        }

        $cachename = bqopp_cache::PERSESSIONSHORTEXPIRY;
        $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, $user->id]));
        $cachedvalue = bqopp_cache::get($cachename, $cachekey);
        if (!is_null($cachedvalue)) {
            $debug && debugging($fxn . '::Found a cached value, so return that');
            return $cachedvalue;
        }

        $returnthis = false;

        switch (true) {
            case \is_siteadmin():
                $debug && debugging($fxn . '::This user is siteadmin');
                $returnthis = true;
                break;
            default:
                $accessdata = get_user_accessdata($user->id);
                $debug && debugging($fxn . '::Got $accessdata=' . bqopp_u::var_dump($accessdata, true));

                foreach ($accessdata['ra'] as $contextpath => &$roles) {
                    $debug && debugging($fxn . '::Looking at $contextpath=' . $contextpath . ' with roles=' . bqopp_u::var_dump($roles, true));

                    foreach (['guest', 'user', 'frontpage'] as $discardthisrole) {
                        $debug && debugging($fxn . '::Looking for role=' . $discardthisrole);
                        $userrole = bqopp_mu::get_default_role($discardthisrole);
                        $debug && debugging($fxn . '::We should discard $userrole=' . bqopp_u::var_dump($userrole, true));

                        if (array_key_exists($userrole->id, $roles)) {
                            $debug && debugging($fxn . '::Removed role=' . $discardthisrole);
                            unset($roles[$userrole->id]);
                        }
                    }

                    $debug && debugging($fxn . '::After cleaning, roles=' . bqopp_u::var_dump($roles, true));
                    if (empty($roles)) {
                        unset($accessdata['ra'][$contextpath]);
                    }
                }

                $debug && debugging($fxn . '::After cleaning, $accessdata[\'ra\']=' . bqopp_u::var_dump($accessdata['ra'], true));
                $returnthis = !bqopp_u::is_empty($accessdata['ra']);
                $debug && debugging($fxn . '::Set $returnthis=' . ($returnthis ? 1 : 0));
                break;
        }

        // Do not cache if found no non-basic roles.
        $returnthis && bqopp_cache::set($cachename, $cachekey, $returnthis);
        $debug && debugging($fxn . '::About to return $returnthis=' . ($returnthis ? 1 : 0));
        return $returnthis;
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
        $debug && debugging($fxn . '::Started with $blockinstanceid=' . $blockinstanceid . '; $contextid=' . $contextid . '; $newvisibility=' . ($newvisibility ? 'true' : 'false'));

        global $DB;
        $blockinstances = $DB->get_records('block_instances', ['parentcontextid' => $contextid]);
        $debug && debugging($fxn . '::Got ' . bqopp_u::count_if_countable($blockinstances) . ' $blockinstances');

        foreach ($blockinstances as $blockinstance) {
            $debug && debugging($fxn . '::About to set block_positions visible for blockinstanceid=' . $blockinstance->id . ' in contextid=' . $contextid . ' to ' . ($newvisibility ? 'true' : 'false'));
            $DB->set_field('block_positions', 'visible', $newvisibility, [
                'blockinstanceid' => $blockinstance->id,
                'contextid' => $contextid
            ]);
        }

        $debug && debugging($fxn . '::Done');
    }

    /**
     * Returns true if a plugin is installed and enabled.
     *
     * @param string $component Plugin full name (frankenstyle) with the type prefix, e.g. local_satraining, mod_quiz, block_html.  Assumed to be a valid string.
     * @return bool True if a plugin is installed and enabled.
     */
    public static function is_plugin_installed_and_enabled(string $component): bool {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $debug && debugging($fxn . '::Started with $component=' . $component);

        // Sanity check.
        if (!strpos($component, '_')) {
            throw new \InvalidArgumentException($fxn . '::Expected a plugin full name (frankenstyle) including the type prefix e.g. local_satraining, mod_quiz, block_html');
        }

        $cachename = bqopp_cache::APPLICATION;
        $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, $component]));
        $cachedvalue = bqopp_cache::get($cachename, $cachekey);
        if (!is_null($cachedvalue)) {
            $debug && debugging($fxn . '::Found a cached value, so return that');
            return $cachedvalue;
        }

        $pluginmanager = \core_plugin_manager::instance();
        $isinstalled = !empty($pluginmanager->get_plugin_info($component));
        $debug && debugging($fxn . '::Got $isinstalled=' . bqopp_u::var_dump($isinstalled, true));

        [$type, $name] = \core_component::normalize_component($component);
        $enabledplugins = $pluginmanager->get_enabled_plugins($type);
        $debug && debugging($fxn . '::For $type=' . $type . ' got $enabledplugins=' . bqopp_u::var_dump($enabledplugins, true));
        $isenabled = array_key_exists($name, $enabledplugins);

        $returnthis = $isinstalled && $isenabled;
        bqopp_cache::set($cachename, $cachekey, $returnthis);
        return $returnthis;
    }

    /**
     * A wrapper around Moodle core moodlelib.php::fullname() but with per-session caching. Returns a persons full name
     *
     * Given an object containing *all of the users name values*, this function returns a string with the full name of the person.
     * The result may depend on system settings or language. 'override' will force the alternativefullnameformat to be used. In
     * English, fullname as well as alternativefullnameformat is set to 'firstname lastname' by default. But you could have
     * fullname set to 'firstname lastname' and alternativefullnameformat set to 'firstname middlename alternatename lastname'.
     *
     * @see \fullname()
     * @param mixed $userobjectorid A Moodle user object or userid to get full name of.
     * @param bool $override If true then the alternativefullnameformat format rather than fullnamedisplay format will be used.
     *
     * @return string
     */
    public static function get_user_fullname($userobjectorid, $override = false): string {
        $debug = false;
        $fxn = __CLASS__ . '::' . __FUNCTION__;
        $user = bqopp_mu::get_user_as_obj($userobjectorid);
        $debug && debugging($fxn . '::Started with userid=' . $user->id);

        // Cache so multiple calls don't repeat the same work.
        $cache = \cache::make(__NAMESPACE__, bqopp_cache::PERSESSION);
        $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, $user->id, intval($override)]));
        if (FeatureControl::CACHE && ($cachedvalue = $cache->get($cachekey))) {
            if ($cachedvalue !== false) {
                $debug && debugging($fxn . '::Found a cached value, so return that');
                return $cachedvalue;
            }
        }

        $returnthis = \fullname($user, $override);
        $debug && debugging($fxn . '::Got use fullname=' . $returnthis);

        if (FeatureControl::CACHE && !$cache->set($cachekey, $returnthis)) {
            throw new \Exception('Failed to set value in the cache');
        }
        return $returnthis;
    }

    /**
     * Strip out non-ASCII text and HTML tags.
     *
     * @param string $str The string to clean.
     * @return string The input string cleaned of ASCII text and HTML tags.
     */
    public static function clean_ascii_no_tags(string $str): string {
        return filter_var(\clean_param($str, PARAM_NOTAGS), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    }
}
