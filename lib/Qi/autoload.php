<?php

/**
 * Generic autoload to copy to a global lib dir
 */
spl_autoload_register (function ($class) {
	$file = str_replace ('\\', DIRECTORY_SEPARATOR, ltrim ($class, '\\')) . '.php';
    if (
	    stream_resolve_include_path(dirname(__DIR__) . DIRECTORY_SEPARATOR . $file) ||
        stream_resolve_include_path($file)
       ) {
		require_once $file;
		return true;
	}
	return false;
});
