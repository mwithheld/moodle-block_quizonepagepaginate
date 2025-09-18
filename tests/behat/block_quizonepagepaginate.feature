@block @block_quizonepagepaginate @javascript
Feature: Basic functionality

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Test Course | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course     | role    |
      | teacher1 | C1        | editingteacher |
      | student1 | C1        | student |
    And the following "activities" exist:
      | activity | name      | intro           | course | idnumber |
      | quiz     | Quiz 1 | Quiz intro text | C1    | quiz1    |
    And I am on the "Quiz 1" "quiz activity" page
    And I log in as "teacher1"
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q1 |
      | Question text | The sky is blue. |
      | Correct answer | True |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q2 |
      | Question text | The grass is red. |
      | Correct answer | False |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q3 |
      | Question text | Water is wet. |
      | Correct answer | True |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q4 |
      | Question text | Fire is cold. |
      | Correct answer | False |
    And I log out

  Scenario: Quiz without block shows all questions, with block paginates questions
    # As student, attempt quiz (should see all questions)
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz"
    Then I should see "The sky is blue."
    And I should see "The grass is red."
    And I should see "Water is wet."
    And I should see "Fire is cold."
    When I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out

    # As teacher, add the block to the quiz
    Given I log in as "teacher1"
    And I am on the "Quiz 1" "quiz activity" page
    When I turn editing mode on
    And I add the "One Page Paginate" block
    And I log out

    # As student, attempt quiz again (should see one question at a time)
    Given I log in as "student1"
    And I am on the "Quiz 1" "quiz activity" page
    When I press "Re-attempt quiz"
    Then I should see "The sky is blue."
    And I should not see "The grass is red."
    And the "Previous" "button" should be disabled
    When I press "Next"
    Then I should see "The grass is red."
    And I should not see "The sky is blue."
    When I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out
    