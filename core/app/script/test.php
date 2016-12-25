<?php
passthru("vendor/bin/phpunit -c etc/phpunit.xml ".implode(" ", $argv));
