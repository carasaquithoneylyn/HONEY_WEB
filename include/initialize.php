<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname(__DIR__));
defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT . DS . 'include');

require_once(LIB_PATH . DS . "config.php");
require_once(LIB_PATH . DS . "functions.php");
require_once(LIB_PATH . DS . "session.php");
require_once(LIB_PATH . DS . "user.php");
require_once(LIB_PATH . DS . "usertype.php");
require_once(LIB_PATH . DS . "database.php");
require_once(LIB_PATH . DS . "details.php");
require_once(LIB_PATH . DS . "student.php");
require_once(LIB_PATH . DS . "course.php");
require_once(LIB_PATH . DS . "subject.php");
require_once(LIB_PATH . DS . "generic.php");
require_once(__DIR__ . "/notifications.php");
?>