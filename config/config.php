<?php

define('ENV_DEVELOP', 'development');
define('ENV_PRODUCT', 'production');
if (file_exists('./env')) {
	define('ENV', ENV_PRODUCT);
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
} else {
	define('ENV', ENV_DEVELOP);
}

