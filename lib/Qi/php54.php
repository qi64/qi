<?php

if ( ! function_exists('http_response_code') )
{
    function http_response_code($statusCode)
    {
        header(':', true, $statusCode);
    }
}

defined('JSON_PRETTY_PRINT') || define('JSON_PRETTY_PRINT', 0);
