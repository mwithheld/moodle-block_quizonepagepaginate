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
 * @copyright   IntegrityAdvocate.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* global window, M, $ */
/* eslint-env es6, node */
/* eslint-disable no-control-regex, no-alert */

class block_quizonepagepaginate {
    constructor(questionsperpage) {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.constructor';
        if (debug) { window.console.log(FXN + '::Started'); }

        // Init class vars.
        // How many quiz questions to show at one time.
        self.questionsperpage = questionsperpage;

        // Used to locate the quiz questions on the page.
        self.eltQuestionsSelector = '#page-mod-quiz-attempt #responseform .que';
        // Used to place this plugin's JS-driven next/prev nav buttons.
        self.eltQuizFinishAttemptButtonSelector = '#responseform .submitbtns .mod_quiz-next-nav';

        // Holds all the current page quiz questions, visible or not.
        self.arrQuestions = [];
    }

    run() {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.run';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.getAllQuestions();
        self.hideAllQuestions();
        self.showQuestions();
        self.addNextPrevButtons();
    }

    getAllQuestions() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.getAllQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.arrQuestions = document.querySelectorAll(self.eltQuestionsSelector);
        if (debug) { window.console.log(FXN + '::Found ' + self.arrQuestions.length + ' questions on the page'); }
    }

    hideAllQuestions() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.hideAllQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        window.console.log(FXN + '::About to hide all ' + self.arrQuestions.length + ' quiz questions on the page');
        Array.from(self.arrQuestions).forEach(elt => (elt.style.display = 'none'));
    }

    showQuestions() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.showQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        window.console.log(FXN + '::About to unhide the first ' + self.questionsperpage + ' quiz questions');
        Array.from(self.arrQuestions).slice(0, self.questionsperpage).forEach(elt => (elt.style.display = 'block'));
    }

    addNextPrevButtons() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.showQuestions';
        if (debug) {
            window.console.log(FXN + '::Started with self.eltQuizFinishAttemptButtonSelector=',
                self.eltQuizFinishAttemptButtonSelector);
        }

        var elt = document.querySelector(self.eltQuizFinishAttemptButtonSelector);
        // We need core/str bc we get column names via ajax get_string later.
        require(['core/str'], function(str) {
            var eltPrev = elt.cloneNode();
            var prevval = self.constructor.name + '-prev';
            var stringispresent_prevdisplay = str.get_string('previous', 'core');
            eltPrev.setAttribute('id', prevval);
            eltPrev.setAttribute('class', eltPrev.getAttribute('class').replace('btn-primary', 'btn-secondary'));
            eltPrev.setAttribute('name', prevval);
            eltPrev.setAttribute('type', prevval);
            $.when(stringispresent_prevdisplay).done(function(prevdisplay) {
                eltPrev.setAttribute('value', prevdisplay);
                eltPrev.setAttribute('data-initial-value', prevdisplay);
            });
            elt.parentNode.insertBefore(eltPrev, elt);

            var eltNext = elt.cloneNode();
            var nextval = self.constructor.name + '-next';
            var stringispresent_nextdisplay = str.get_string('next', 'core');
            eltNext.setAttribute('id', nextval);
            eltNext.setAttribute('class', eltNext.getAttribute('class').replace('btn-primary', 'btn-secondary'));
            eltNext.setAttribute('name', nextval);
            eltNext.setAttribute('type', nextval);
            $.when(stringispresent_nextdisplay).done(function(nextdisplay) {
                eltNext.setAttribute('value', nextdisplay);
                eltNext.setAttribute('data-initial-value', nextdisplay);
            });
            elt.parentNode.insertBefore(eltNext, elt);
        });
    }

}

/**
 * Setup the module.
 *
 * @param {number} questionsperpage How many quiz questions to show at once.
 */
export const init = (questionsperpage) => {
    let debug = false;
    const FXN = 'block_quizonepagepaginate::init';
    if (debug) { window.console.log(FXN + '::Started with questionsperpage=' + questionsperpage); }

    M.block_quizonepagepaginate = new block_quizonepagepaginate(questionsperpage);
    //if (debug) { window.console.log('M.block_quizonepagepaginate::Built class=', M.block_quizonepagepaginate); }
    M.block_quizonepagepaginate.run();
};