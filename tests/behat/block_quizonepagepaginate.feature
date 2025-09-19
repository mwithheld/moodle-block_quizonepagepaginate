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
      | Question text | sky=blue |
      | Correct answer | True |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q2 |
      | Question text | grass=red |
      | Correct answer | False |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q3 |
      | Question text | water=wet |
      | Correct answer | True |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name | Q4 |
      | Question text | fire=cold |
      | Correct answer | False |
    And I log out
  # Setup this block
    # As student, attempt quiz (should see all questions)
    And I change window size to "1920x1200"
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz"
    # Q1
    Then I should see "sky=blue"
    # Q2
    And I should see "grass=red"
    # Q3
    And I should see "water=wet"
    # Q4
    And I should see "fire=cold"
    When I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out
    # As teacher, add the block to the quiz
    Given I log in as "teacher1"
    And I am on the "Quiz 1" "quiz activity" page
    When I turn editing mode on
    And I add the "One Page Paginate" block
    And "One Page Paginate" "block" should exist

  @javascript @block @block_quizonepagepaginate @block_quizonepagepaginate_hide_questionsperpage
  Scenario: This block hides the quiz setting questionsperpage
    And I am on the "Quiz 1" "quiz activity editing" page logged in as "teacher1"
    When I turn editing mode on
    And "One Page Paginate" "block" should be visible
    And I expand all fieldsets
    Then "#fgroup_id_questionsperpagegrpid_questionsperpage" "css_element" should not be visible
    And I click on "#id_display .moreless-toggler" "css_element"
    And the field "Show blocks during quiz attempts" matches value "1"
    And I log out

  # @javascript @block @block_quizonepagepaginate @block_quizonepagepaginate_hideshow
  # Scenario: Hide the block and check it is hidden on all quiz pages
  #   When I am on the "Quiz 1" "mod_quiz > View" page logged in as "teacher1"
  #   And I turn editing mode on
  #   Then "One Page Paginate" "block" should be visible
  #   And I open the "One Page Paginate" blocks action menu
  #   #
  #   # NONE OF THE BELOW WORKS TO CLICK THE ACTIONS MENU AND CHOOSE "HIDE".
  #   #     
  #   # When I click on ".block_quizonepagepaginate .dropdown-toggle i" "css_element"
  #   # And I choose "Hide One Page Paginate block" in the open action menu
  #   # And I follow "Hide One Page Paginate block"
  #   And I click on "Actions menu" "icon" in the "One Page Paginate" "block"
  #   And I follow "Hide One Page Paginate block"
  #   Then ".block_quizonepagepaginate.invisibleblock" "css_element" should exist
  #   #
  #   When I am on the "Quiz 1" "mod_quiz > Edit" page
  #   Then ".block_quizonepagepaginate.invisibleblock" "css_element" should exist
  #   #
  #   And I am on the "Test quiz name" "mod_quiz > Grades report" page
  #   Then ".block_quizonepagepaginate.invisibleblock" "css_element" should exist
  #   And I log out

  @javascript @block @block_quizonepagepaginate @block_quizonepagepaginate_student_onequestion
  Scenario: As student I should see one quiz question at a time
    Given I log in as "student1"
    And I am on the "Quiz 1" "quiz activity" page
    When I press "Re-attempt quiz"

    And "One Page Paginate" "block" should exist
    Then ".block_quizonepagepaginate.invisibleblock" "css_element" should exist

    # Page 1
    # Q1
    Then I should see "sky=blue"
    # Q2
    And I should not see "grass=red"
    # Q3
    And I should not see "water=wet"
    # Q4
    And I should not see "fire=cold"
    And the "Previous" "button" should be disabled
    When I press "Next"
    # Page 2
    # Q1
    Then I should not see "sky=blue"
    # Q2
    And I should see "grass=red"
    # Q3
    And I should not see "water=wet"
    # Q4
    And I should not see "fire=cold"
    And the "Previous" "button" should be enabled
    When I press "Next"
    # Page 3
    When I press "Next"
    # Page 4
    # Q1
    Then I should not see "sky=blue"
    # Q2
    And I should not see "grass=red"
    # Q3
    And I should not see "water=wet"
    # Q4
    And I should see "fire=cold"
    And the "Next" "button" should be disabled
    When I press "Previous"
    # Page 3
    # Q1
    Then I should not see "sky=blue"
    # Q2
    And I should not see "grass=red"
    # Q3
    And I should see "water=wet"
    # Q4
    And I should not see "fire=cold"
    And the "Previous" "button" should be enabled
    And the "Next" "button" should be enabled
    When I press "Finish attempt ..."

    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out

  # TODO: If I make the block visible again, it should appear on all quiz pages
  # TODO: If I hide the block, all questions should appear for students
