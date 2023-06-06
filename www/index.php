<?php
/**
 * Kit.
 *
 * $Id: index.php 15 2010-06-03 10:41:32Z eprev $
 */

define('START_TIME', microtime(true));

date_default_timezone_set('America/Chicago');

define('CACHE_PATH', 'memcached://localhost:11211/?prefix=kit');

define('APPLICATION_PATH', realpath('../application') . '/');
define('MODULES_PATH',     realpath('../modules')     . '/');
define('SYSTEM_PATH',      realpath('../system')      . '/');
define('COOT_PATH',      realpath('../')    );

// DO NOT CHANGE ANYTHING BELOW THIS LINE

require SYSTEM_PATH . 'library/system.php';

Application::execute();