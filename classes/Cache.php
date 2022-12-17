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
 * Cache manager.
 *
 * @package     block_quizonepagepaginate
 * @author      Mark van Hoek <vhmark@gmail.com>
 * @copyright   2022 IntegrityAdvocate.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Ignore some Moodle codechecker PHPCS rules that I do not entirely agree with.
 * @tags
 * @phpcs:disable moodle.Files.LineLength.MaxExceeded
 * @phpcs:disable moodle.PHP.ForbiddenFunctions.FoundWithAlternative
 * @phpcs:disable moodle.PHP.ForbiddenFunctions.Found
 */
declare(strict_types=1);

namespace block_quizonepagepaginate;

defined('MOODLE_INTERNAL') || die();

use block_quizonepagepaginate\Cache as bqopp_cache;
use block_quizonepagepaginate\Utility as bqopp_u;

final class Cache {

    public const APPLICATION = 'application';
    public const PERREQUEST = 'perrequest';
    public const PERSESSION = 'persession';
    public const PERSESSIONSHORTEXPIRY = 'persessionshortexpiry';
    public const DEFINITIONS = [
        bqopp_cache::APPLICATION => [
            'mode' => \cache_store::MODE_APPLICATION,
            'simplekeys' => true,
            'staticacceleration' => true,
            'canuselocalstore' => true,
        ],
        bqopp_cache::PERREQUEST => [
            'mode' => \cache_store::MODE_REQUEST,
            'simplekeys' => true,
            'staticacceleration' => true,
            'canuselocalstore' => true,
        ],
        bqopp_cache::PERSESSION => [
            'mode' => \cache_store::MODE_SESSION,
            'simplekeys' => true,
            'staticacceleration' => true,
            'canuselocalstore' => true,
        ],
        bqopp_cache::PERSESSIONSHORTEXPIRY => [
            'mode' => \cache_store::MODE_SESSION,
            'simplekeys' => true,
            'staticacceleration' => true,
            'canuselocalstore' => true,
            // TTL with 5-minute timeout.
            'ttl' => 60 * 5,
        ],
    ];

    /** @var array<string,\cache> Cache instances we can re-use. */
    private static array $cacheinstance = [];

    /**
     * This is an augmented wrapper around Moodle cache->set().
     * Sends a key => value pair to the cache.
     *
     * @param string $cachename Name of the cache - must be one of the keys in bqopp_cache::DEFINITIONS.
     * @param string $cachekey The key for the data being requested.  E.g. $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, $user->id]));.
     * @param mixed $data The data to set against the key.
     * @return bool True on success, false if caching is disabled.  On failure throws an error.
     */
    public static function set(string $cachename, string $cachekey, $data): bool {
        if (!FeatureControl::CACHE) {
            return false;
        }

        if (empty($cachekey)) {
            throw new \Exception('Invalid cachekey specified=' . $cachekey);
        }

        if (!array_key_exists($cachename, bqopp_cache::DEFINITIONS)) {
            throw new \Exception('Invalid cachename specified=' . $cachename);
        }

        if (!isset(bqopp_cache::$cacheinstance[$cachename])) {
            bqopp_cache::$cacheinstance[$cachename] = \cache::make(__NAMESPACE__, $cachename);
        }

        if (is_bool($data)) {
            $data = json_encode($data);
        }

        if (!bqopp_cache::$cacheinstance[$cachename]->set($cachekey, $data)) {
            throw new \Exception('Failed to set $data in the cache');
        }

        return true;
    }

    /**
     * Retrieves the value for the given key from the cache.  An augmented wrapper around Moodle's cache->get() except it returns null if they key is not found (instead of false).
     * Note if you cache a JSON-ified object/array, you'll get the object/array back out and not the JSON.
     *
     * @param string $cachename Name of the cache - must be one of the keys in bqopp_cache::DEFINITIONS.
     * @param string $cachekey The key for the data being requested.  E.g. $cachekey = bqopp_mu::get_cache_key(implode('_', [$fxn, $user->id]));.
     * @return mixed|null The data from the cache or null if the key did not exist within the cache.
     */
    public static function get(string $cachename, string $cachekey) {
        if (!FeatureControl::CACHE) {
            return null;
        }

        if (empty($cachekey)) {
            throw new \Exception('Invalid cachekey specified=' . $cachekey);
        }

        if (!array_key_exists($cachename, bqopp_cache::DEFINITIONS)) {
            throw new \Exception('Invalid cachename specified=' . $cachename);
        }

        if (!isset(bqopp_cache::$cacheinstance[$cachename])) {
            // The cacheinstance does not exist yet.
            return null;
        }

        $cachedvalue = bqopp_cache::$cacheinstance[$cachename]->get($cachekey);
        if ($cachedvalue === false) {
            return null;
        }

        return bqopp_u::get_data_from_maybe_json($cachedvalue);
    }
}
