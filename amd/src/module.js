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
 * JS for this plugin.
 *
 * @author      Mark van Hoek <vhmark@gmail.com>
 * @copyright   2022 Service Alberta
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* global window, M */
/* Unused: $, M, alert, location, history, DOMParser */
/* eslint-env es6, node */
/* eslint-disable no-control-regex, no-alert */


class block_quizonepagepaginate {
    constructor() {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.constructor';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.eltQuestionsSelector = '#page-mod-quiz-attempt #responseform .que';
        self.arrQuestions = [];
    }

    run() {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.run';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.showQuestions();
    }

    showQuestions() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.showQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.arrQuestions = document.querySelectorAll(self.eltQuestionsSelector);
        if (debug) { window.console.log(FXN + '::Found ' + self.arrQuestions.length + ' questions'); }
        Array.from(self.arrQuestions).slice(0, 2).forEach(elt => (elt.style.display = 'block'));
    }

}

/**
 * Setup the module.
 */
export const init = () => {
    const FXN = 'block_quizonepagepaginate::init';
    window.console.log(FXN + '::Started');

    // Execution starts here.
    M.block_quizonepagepaginate = new block_quizonepagepaginate();
    //window.console.log('M.block_quizonepagepaginate::Built class=', M.block_quizonepagepaginate);
    M.block_quizonepagepaginate.run();
};