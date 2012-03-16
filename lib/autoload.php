<?php

/**
 * Generic autoload to copy to a global lib dir
 */
spl_autoload_register (function ($class) {
	$file = str_replace ('\\', DIRECTORY_SEPARATOR, ltrim ($class, '\\')) . '.php';
	if (file_exists (__DIR__ . DIRECTORY_SEPARATOR . $file)) {
		require_once $file;
		return true;
	}
	return false;
});
