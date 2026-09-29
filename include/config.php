<?php
// ABADIES SOLUTIONS

defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "root");
defined('DB_PASS')   ? null : define("DB_PASS", "");
defined('DB_NAME')   ? null : define("DB_NAME", "abadies_db");

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

$this_file = str_replace('\\', '/', __FILE__);
$doc_root  = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
defined('LIB_PATH')  ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

/* Works both ways:
   - vhost   http://abadies-class.local/     -> WEB_ROOT = "/"
   - folder  http://localhost/abadies-class/ -> WEB_ROOT = "/abadies-class/"
   Nothing needs to change when you switch between the two. */
$webRoot = str_replace(array($doc_root, "include/config.php"), '', $this_file);
$srvRoot = str_replace('config/config.php', '', $this_file);

define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
?>
