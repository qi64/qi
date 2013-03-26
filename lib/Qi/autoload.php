<?php

spl_autoload_register (function ($class) {
	$file = str_replace ('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php';
    // try to load from include_path first
    if ( stream_resolve_include_path($file)
    // then try to load absolute from parent dir
      || stream_resolve_include_path($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . $file) ) {
        require_once $file;
        return true;
    }
    return false;
});
