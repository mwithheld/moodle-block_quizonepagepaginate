Behat on Docker
----
# Ref https://github.com/moodlehq/moodle-docker#use-containers-for-running-behat-tests .
bin/moodle-docker-compose exec webserver php admin/tool/behat/cli/init.php
bin/moodle-docker-compose exec -u www-data webserver php admin/tool/behat/cli/run.php --tags=@block_quizonepagepaginate
# Some options for admin/cli/run.php:
# ... --format=pretty --colors --no-snippets
# ... --format=pretty --format-settings '{"formats": "html,image", "dir_permissions": "0777"}'

