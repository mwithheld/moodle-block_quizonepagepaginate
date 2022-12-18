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
/* global window, M */
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
        self.hideShowQuestions();
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

    hideShowQuestions() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.hideShowQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        var first = 0;
        var length = self.questionsperpage;
        var last = first + length;

        self.arrQuestions.forEach(function(elt, index) {
            window.console.log(FXN + '::Looking at index=; elt=', index, elt);
            if (index >= first && index < last) {
                if (debug) { window.console.log(FXN + '::Show this elt'); }
                self.setDisplayVal(elt, 'block');
            } else {
                if (debug) { window.console.log(FXN + '::Hide this elt'); }
                self.setDisplayVal(elt, 'none');
            }
        });
    }

    setDisplayVal(elt, displayVal) {
        if (elt.style.display !== displayVal) {
            elt.style.display = displayVal;
        }
    }

    addNextPrevButtons() {
        let debug = true;
        let self = this;
        const FXN = self.constructor.name + '.addNextPrevButtons';
        if (debug) {
            window.console.log(FXN + '::Started with self.eltQuizFinishAttemptButtonSelector=',
                self.eltQuizFinishAttemptButtonSelector);
        }

        var eltCloneSource = document.querySelector(self.eltQuizFinishAttemptButtonSelector);

        // String are returned in a plain array in the same order specified here.
        // E.g. [0 => "Previous", 1 => "Next"].
        var stringsToRetrieve = [{
                key: 'previous',
                component: 'core'
            },
            {
                key: 'next',
                component: 'core',
            }
        ];

        // We need core/str bc we get column names via ajax get_string later.
        require(['core/str'], function(str) {
            if (debug) { window.console.log(FXN + '.require::Started with stringsToRetrieve=', stringsToRetrieve); }

            str.get_strings(stringsToRetrieve).then(
                function(stringsRetrieved) {
                    if (debug) {
                        window.console.log(FXN + '.require.get_strings.then::Started with stringsRetrieved=', stringsRetrieved);
                    }

                    self.addPrevNextButton(eltCloneSource, 'prev', stringsRetrieved);
                    self.addPrevNextButton(eltCloneSource, 'next', stringsRetrieved);
                });
        });
    }

    addPrevNextButton(eltCloneSource, nextorprev, strings) {
        var eltClone = eltCloneSource.cloneNode();
        var prevval = self.constructor.name + '-' + nextorprev;
        var prevdisplay = strings[(nextorprev == 'prev' ? 0 : 1)];
        eltClone.setAttribute('id', prevval);
        eltClone.setAttribute('class', eltClone.getAttribute('class').replace('btn-primary', 'btn-secondary'));
        eltClone.setAttribute('name', prevval);
        eltClone.setAttribute('type', prevval);
        eltClone.setAttribute('value', prevdisplay);
        eltClone.setAttribute('data-initial-value', prevdisplay);
        var eltInDom = (eltCloneSource.parentNode.insertBefore(eltClone, eltCloneSource));
        eltInDom.addEventListener('click',
            function() {
                window.console.log('Clicked the ' + nextorprev + ' button');
            });
        return eltInDom;
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