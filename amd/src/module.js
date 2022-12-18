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
        const self = this;
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
        const self = this;
        const FXN = self.constructor.name + '.run';
        if (debug) { window.console.log(FXN + '::Started with self.firstQuestionToShow=; self.questionsperpage=', self.firstQuestionToShow, self.questionsperpage); }

        self.getAllQuestions();
        self.addNextPrevButtons();

        // Find the question index matching the question-* number.
        const requestedQuestionIndex = self.getAnchorQuestionIndex();
        if (debug) { window.console.log(FXN + '::Got requestedQuestionIndex=', requestedQuestionIndex); }
        if (requestedQuestionIndex >= 0) {
            self.firstQuestionToShow = requestedQuestionIndex;
        }
        self.hideShowQuestions(self.firstQuestionToShow, self.questionsperpage);
    }

    /**
     * If the URL anchor value matches /question-\d+-\d+/, get the index of the self.arrQuestions item that matches.
     *
     * @returns {number} The matchin index in self.arrQuestions; else -1.
     */
    getAnchorQuestionIndex() {
        let debug = false;
        const self = this;
        const FXN = self.constructor.name + '.getAnchorQuestionIndex';
        if (debug) { window.console.log(FXN + '::Started'); }

        let questionIndex = -1;

        const anchor = self.getAnchor();
        if (debug) { window.console.log(FXN + '::Got anchor=', anchor); }
        if (!anchor) {
            return questionIndex;
        }

        const questionNrRequested = self.getAnchorQuestionNr(anchor);
        if (!questionNrRequested) {
            return questionIndex;
        }

        questionIndex = self.findQuestionIndexFromQuestionNr(questionNrRequested);

        return questionIndex;
    }

    /**
     * Get the URL anchor value.
     *
     * @returns {string} The URL anchor value (e.g. "blah" in url=http://bloo#blah); else return empty string.
     */
    getAnchor() {
        return (document.URL.split('#').length > 1) ? document.URL.split('#')[1] : null;
    }

    /**
     * Extract the question sequence number from the URL anchor text.
     *
     * @param {string} anchor The URL anchor string (e.g. "blah" in url=http://bloo#blah).
     * @returns {string} The question number e.g. "question-23-9" from the URL anchor value (e.g. from http://bloo#question-23-9); else return empty string.
     */
    getAnchorQuestionNr(anchor = '') {
        let debug = false;
        const self = this;
        const FXN = self.constructor.name + '.getAnchorQuestionNr';
        if (debug) { window.console.log(FXN + '::Started'); }

        // This value is in the format mm-nn where mm=the quiz attempt number; nn=the question index.
        let questionNrRequested = '';

        if (anchor && anchor.length > 2) {
            const regexResults = anchor.match(/(question-\d+-\d+)/);
            if (debug) { window.console.log(FXN + '::Got regexResults=', regexResults); }
            if (regexResults) {
                questionNrRequested = regexResults[1];
            }
        }
        if (debug) { window.console.log(FXN + '::Got questionNrRequested=', questionNrRequested); }

        return questionNrRequested;
    }

    /**
     * Search self.arrQuestions for a question with number=questionNr.
     *
     * @param {str} questionNr The question number e.g. "question-23-9".
     * @returns {number} The index of self.arrQuestions that matches; else -1.
     */
    findQuestionIndexFromQuestionNr(questionNr = '') {
        let debug = false;
        const self = this;
        const FXN = self.constructor.name + '.findQuestionIndexFromQuestionNr';
        if (debug) { window.console.log(FXN + '::Started'); }

        let indexFound = -1;

        if (!questionNr) {
            window.console.log(FXN + '::Invalid value passed for param questionNr so return not found');
            return indexFound;
        }
        if (self.arrQuestions.length < 1) {
            window.console.log(FXN + '::arrQuestions is empty so return not found');
            return indexFound;
        }

        self.arrQuestions.forEach(function(elt, index) {
            if (debug) { window.console.log(FXN + '::Looking at index=; elt=', index, elt); }
            if (elt.id == questionNr) {
                if (debug) { window.console.log(FXN + '.forEach::Found matching index=', index); }
                indexFound = index;
                return;
            }
        });

        if (debug) { window.console.log(FXN + '::About to return indexFound=', indexFound); }
        return indexFound;
    }

    getAllQuestions() {
        let debug = false;
        const self = this;
        const FXN = self.constructor.name + '.getAllQuestions';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.arrQuestions = document.querySelectorAll(self.eltQuestionsSelector);
        if (debug) { window.console.log(FXN + '::Found ' + self.arrQuestions.length + ' questions on the page'); }
    }

    hideShowQuestions(first = 0, length) {
        let debug = false;
        const self = M.block_quizonepagepaginate;
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

    scrollToQuestion(anchor = '') {
        if (anchor.length < 1) {
            document.querySelector('#responseform').scrollIntoView({
                behavior: 'smooth'
            });
        }
    }

    setDisplayVal(elt, displayVal) {
        elt.style.display = displayVal;
    }

    addNextPrevButtons() {
        let debug = false;
        const self = this;
        const FXN = self.constructor.name + '.addNextPrevButtons';
        if (debug) { window.console.log(FXN + '::Started with self.eltQuizFinishAttemptButtonSelector=', self.eltQuizFinishAttemptButtonSelector); }

        const eltCloneSource = document.querySelector(self.eltQuizFinishAttemptButtonSelector);

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

                    const eltPrevInDom = self.addPrevNextButton(eltCloneSource, 'prev', stringsRetrieved);
                    eltPrevInDom.addEventListener('click', self.buttonClickedPrev);

                    const eltNextInDom = self.addPrevNextButton(eltCloneSource, 'next', stringsRetrieved);
                    eltNextInDom.addEventListener('click', self.buttonClickedNext);
                });
        });
    }

    /**
     * Add buttons to the page to JS-navigate through the quiz questions on the page.
     *
     * @param {DomElement} eltCloneSource An existing button in the form buttons area.
     * @param {string} nextorprev Which button to create; valid values=[prev, next]
     * @param {Array<string>} strings Moodle lang strings for the buttons in the order they are created.
     * @returns {DomElement} The DomElement we just inserted.
     */
    addPrevNextButton(eltCloneSource, nextorprev, strings) {
        let debug = false;
        const self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.addPrevNextButton';
        if (debug) { window.console.log(FXN + '::Started'); }

        const eltClone = eltCloneSource.cloneNode();
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
        const self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.buttonClickedPrev';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.updateVisibleQuestionRange(false);
        self.hideShowQuestions(self.firstQuestionToShow, self.questionsperpage);
        self.scrollToQuestion();
    }

    buttonClickedNext() {
        let debug = false;
        const self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.buttonClickedNext';
        if (debug) { window.console.log(FXN + '::Started'); }

        self.updateVisibleQuestionRange(true);
        self.hideShowQuestions(self.firstQuestionToShow, self.questionsperpage);
        self.scrollToQuestion();
    }

    updateVisibleQuestionRange(getNextSet = true) {
        let debug = false;
        const self = M.block_quizonepagepaginate;
        const FXN = self.constructor.name + '.updateVisibleQuestionRange';
        if (debug) {
            window.console.log(FXN + '::Started with getNextSet=', getNextSet);
        }

        const firstOfAllQs = 0;
        const lengthToShow = self.questionsperpage;
        const lastOfAllQs = self.arrQuestions.length;
        if (debug) { window.console.log(FXN + '::Start; firstOfAllQs=' + firstOfAllQs + '; lengthToShow=' + lengthToShow + '; lastOfAllQs=' + lastOfAllQs); }

        if (getNextSet) {
            // Propose to jump to the next set of questions.
            const proposedStart = self.firstQuestionToShow + lengthToShow;
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
            const proposedStart = self.firstQuestionToShow - lengthToShow;
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
        window.console.error(e);
    }
};