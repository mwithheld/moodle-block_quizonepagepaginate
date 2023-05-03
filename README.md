# Moodle QuizOnePagePaginate block

This is a Moodle block to deliver quiz questions questions one (or more) at a time on the same page without navigating to a new page for each question.


## Getting started

Install:
- [Download the block code here](https://bitbucket.org/mwebv/moodle-block_quizonepagepaginate/downloads/).
- Follow the [Moodle plugin install instructions](https://docs.moodle.org/402/en/Installing_plugins#Installing_via_uploaded_ZIP_file). The install folder is /path/to/moodle/blocks/quizonepagepaginate/

Add the block to a course quiz:
- Navigate into a course and then into a quiz.
- Turn "Edit mode" on, click "Add a block", scroll and click "One Page Paginate".
![(Screenshot: How to activate)](https://bitbucket.org/mwebv/moodle-block_quizonepagepaginate/raw/9933c4ba2d643f9785a0014d53f272ee1ccaf2b0/docs/block_quizonepagepaginate-activate.png)

Once the block is enabled on a quiz, you will see Previous and Next buttons at the bottom, and by default only one quiz question shows at a time.

![(Screenshot: How it looks when activated)](https://bitbucket.org/mwebv/moodle-block_quizonepagepaginate/raw/9933c4ba2d643f9785a0014d53f272ee1ccaf2b0/docs/block_quizonepagepaginate-activated.png)


To configure the block instance: Navigate into a course quiz, turn "Edit mode" on, in the block click the gear menu > "Configure One Page Paginate block".

![(Screenshot: How to configure)](https://bitbucket.org/mwebv/moodle-block_quizonepagepaginate/raw/9933c4ba2d643f9785a0014d53f272ee1ccaf2b0/docs/block_quizonepagepaginate-configure-1.png)


## Things to know

- All the quiz questions are loaded on page load - they're just hidden from the user.
- The "Quiz navigation" block will not reflect what is currently visible on the page.
- If you exit the quiz and return to it, you will be placed back at the first question and not where you left off.
- On activation, this block overwrites quiz config for:
-- "Layout > New Page" to "All questions on one page"
-- "Appearance > Show blocks during quiz attempts" to "Yes".
- This block will appear on "Any quiz module page" regardless of the "Display on page types" quiz setting.
- The block will not show to students ever, or to teachers unless editing mode is on. If you Hide the block, you disable its features.


## Privacy

This plugin does not store any data.

We take privacy and security seriously. Any security issues can most responsibly be disclosed to admin@integrityadvocate.com


## Requirements

- Moodle 3.5 and above - see [What version of Moodle am I using?](https://docs.moodle.org/en/Moodle_version#What_version_of_Moodle_am_I_using)
- You need administrator privileges in your Moodle instance to install this plugin. You need teacher privileges in your course quiz to enable it.
- JavaScript in the student's browser must be enabled.

## Reporting problems
Please give concrete info and background like:

- What error do you see, or what don't you see that you expect?
- How exactly can I reproduce the error?
- What browser?
- What version of Moodle are you running?
- Does the "quizonepagepaginate" plugin show up on the plugins overview page (/admin/plugins.php)?
- Anything useful in the Apache logs?
