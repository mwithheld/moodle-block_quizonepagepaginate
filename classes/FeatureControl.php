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
 * Feature control: Enable/disable features easily.
 *
 * @copyright   IntegrityAdvocate.com
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

defined('MOODLE_INTERNAL') || die;

final class FeatureControl {

    /** @var bool True to allow PHP-side caching using MUC. */
    public const CACHE = false;
}
