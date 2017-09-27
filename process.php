#!/usr/bin/php -q
<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/database.php';

use PBX\Process\Inbound;

(new Inbound())->run();
