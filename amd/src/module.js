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
/* eslint-disable no-control-regex, no-alert, max-len */

class block_quizonepagepaginate {
    constructor(questionsperpage) {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.constructor';
        if (debug) { window.console.log(FXN + '::Started with questionsperpage=', questionsperpage); }

        if (isNaN(questionsperpage)) {
            throw FXN + '::Invalid value passed for param questionsperpage';
        }

        // How many quiz questions to show at one time.
        self.questionsperpage = parseInt(questionsperpage);
        // The index of the first quiz question to show.
        self.firstQuestionToShow = 0;

        // Used to locate the quiz questions on the page.
        self.eltQuestionsSelector = '#page-mod-quiz-attempt #responseform .que';
        // Used to place this plugin's JS-driven next/prev nav buttons.
        self.eltQuizFinishAttemptButtonSelector = '#responseform .submitbtns .mod_quiz-next-nav';
        // Button to show tne previous questions.
        self.eltBqoppButtonPrev = self.constructor.name + '-prev';
        // Button to show tne next questions.
        self.eltBqoppButtonNext = self.constructor.name + '-next';

        // Holds all the current page quiz questions, visible or not.
        self.arrQuestions = [];
    }

    run() {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.run';
        if (debug) { window.console.log(FXN + '::Started with self.firstQuestionToShow=; self.questionsperpage=', self.firstQuestionToShow, self.questionsperpage); }

        self.getAllQuestions();
        self.hideShowQuestions(self.firstQuestionToShow, self.questionsperpage);
        self.addNextPrevButtons();
    }

    getAllQuestions() {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.getAllQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.arrQuestions = document.querySelectorAll(self.eltQuestionsSelector);
        if (debug) { window.console.log(FXN + '::Found ' + self.arrQuestions.length + ' questions on the page'); }
    }

    hideShowQuestions(first = 0, length) {
        let debug = false;
        let self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.hideShowQuestions';
        if (debug) { window.console.log(FXN + '::Started with start=; length=', first, length); }

        const last = first + length;
        let countVisible = 0;

        self.arrQuestions.forEach(function(elt, index) {
            if (debug) { window.console.log(FXN + '::Looking at index=; elt=', index, elt); }
            if (index >= first && index < last && countVisible < self.questionsperpage) {
                if (debug) { window.console.log(FXN + '::Show this elt'); }
                self.setDisplayVal(elt, 'block');
                countVisible++;
            } else {
                if (debug) { window.console.log(FXN + '::Hide this elt'); }
                self.setDisplayVal(elt, 'none');
            }
        });
    }

    setDisplayVal(elt, displayVal) {
        elt.style.display = displayVal;
    }

    addNextPrevButtons() {
        let debug = false;
        let self = this;
        const FXN = self.constructor.name + '.addNextPrevButtons';
        if (debug) { window.console.log(FXN + '::Started with self.eltQuizFinishAttemptButtonSelector=', self.eltQuizFinishAttemptButtonSelector); }

        let eltCloneSource = document.querySelector(self.eltQuizFinishAttemptButtonSelector);

        // String are returned in a plain array in the same order specified here.
        // E.g. [0 => "Previous", 1 => "Next"].
        const stringsToRetrieve = [{
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
                    if (debug) { window.console.log(FXN + '.require.get_strings.then::Started with stringsRetrieved=', stringsRetrieved); }

                    let eltPrevInDom = self.addPrevNextButton(eltCloneSource, 'prev', stringsRetrieved);
                    eltPrevInDom.addEventListener('click', self.buttonClickedPrev);

                    let eltNextInDom = self.addPrevNextButton(eltCloneSource, 'next', stringsRetrieved);
                    eltNextInDom.addEventListener('click', self.buttonClickedNext);
                });
        });
    }

    /**
     *
     * @param {DomElement} eltCloneSource
     * @param {string} nextorprev
     * @param {Array<string>} strings
     * @returns The DomElement we just inserted
     */
    addPrevNextButton(eltCloneSource, nextorprev, strings) {
        let eltClone = eltCloneSource.cloneNode();
        const prevval = (nextorprev == 'prev' ? self.eltBqoppButtonPrev : self.eltBqoppButtonNext);
        const prevdisplay = strings[(nextorprev == 'prev' ? 0 : 1)];
        eltClone.setAttribute('id', prevval);
        eltClone.setAttribute('class', eltClone.getAttribute('class').replace('btn-primary', 'btn-secondary'));
        eltClone.setAttribute('name', prevval);
        eltClone.setAttribute('type', prevval);
        eltClone.setAttribute('value', prevdisplay);
        eltClone.setAttribute('data-initial-value', prevdisplay);

        return eltCloneSource.parentNode.insertBefore(eltClone, eltCloneSource);
    }

    buttonClickedPrev() {
        let debug = false;
        let self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.buttonClickedPrev';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.updateVisibleQuestionRange(false);
        self.hideShowQuestions(self.firstQuestionToShow, self.questionsperpage);
    }

    buttonClickedNext() {
        let debug = false;
        let self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.buttonClickedNext';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.updateVisibleQuestionRange(true);
        self.hideShowQuestions(self.firstQuestionToShow, self.questionsperpage);
    }

    updateVisibleQuestionRange(getNextSet = true) {
        let debug = false;
        let self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.updateVisibleQuestionRange';
        if (debug) {
            window.console.log(FXN + '::Started with getNextSet=', getNextSet);
        }

        let firstOfAllQs = 0;
        let lengthToShow = self.questionsperpage;
        let lastOfAllQs = self.arrQuestions.length;
        if (debug) { window.console.log(FXN + '::Start; firstOfAllQs=' + firstOfAllQs + '; lengthToShow=' + lengthToShow + '; lastOfAllQs=' + lastOfAllQs); }

        if (getNextSet) {
            // Propose to jump to the next set of questions.
            let proposedStart = self.firstQuestionToShow + lengthToShow;
            if (debug) { window.console.log(FXN + '::Proposed start of the next set of questions=', proposedStart); }

            // Check that the [proposed range of setLength questions] is within the [total range of questions].
            if (proposedStart + lengthToShow < lastOfAllQs) {
                self.firstQuestionToShow = proposedStart;
                if (debug) { window.console.log(FXN + '::The proposedStart + lengthToShow is below the max range, so set self.firstQuestionToShow=', self.firstQuestionToShow); }
            } else {
                self.firstQuestionToShow = lastOfAllQs - lengthToShow;
                if (debug) { window.console.log(FXN + '::The proposedStart + lengthToShow is above the max range, so set self.firstQuestionToShow=', self.firstQuestionToShow); }
            }
        } else {
            // Propose to jump to the next set of questions.
            let proposedStart = self.firstQuestionToShow - lengthToShow;
            window.console.log(FXN + '::Proposed start of the next set of questions=', proposedStart);

            // Check that the [proposed range of setLength questions] is within the [total range of questions].
            if (proposedStart < firstOfAllQs) {
                if (debug) { window.console.log(FXN + '::The proposedStart is below the min range, so set self.firstQuestionToShow=', self.firstQuestionToShow); }
                self.firstQuestionToShow = firstOfAllQs;
            } else {
                if (debug) { window.console.log(FXN + '::The proposedStart is within the min range, so set self.firstQuestionToShow=', self.firstQuestionToShow); }
                self.firstQuestionToShow = proposedStart;
            }
        }

        if (debug) { window.console.log(FXN + '::Done; firstOfAllQs=' + firstOfAllQs + '; lengthToShow=' + lengthToShow + '; lastOfAllQs=' + lastOfAllQs); }
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

    try {
        M.block_quizonepagepaginate = new block_quizonepagepaginate(questionsperpage);
        //if (debug) { window.console.log('M.block_quizonepagepaginate::Built class=', M.block_quizonepagepaginate); }
        M.block_quizonepagepaginate.run();
    } catch (e) {
        window.console.log.error(e);
    }
};