<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
	'driver' => 'mysql',
	'host'   => 'localhost',
	'database' => 'pbx',
	'username' => 'root',
	'password' => '',
	'charset'  => 'utf8',
	'collation' => 'utf8_general_ci',
	'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::enableQueryLog();


set_error_handler("errorHandler");

function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context){
	switch ($error_level) {
	    case E_ERROR:
	    case E_CORE_ERROR:
	    case E_COMPILE_ERROR:
	    case E_PARSE:
	        $errtype = "FATAL ERROR";
	        break;
	    case E_USER_ERROR:
	    case E_RECOVERABLE_ERROR:
	        $errtype = "ERROR";
	        break;
	    case E_WARNING:
	    case E_CORE_WARNING:
	    case E_COMPILE_WARNING:
	    case E_USER_WARNING:
			$errtype = "WARNING";
	        break;
	    case E_NOTICE:
	    case E_USER_NOTICE:
	        $errtype = "INFO";
	        break;
	    case E_STRICT:
	        $errtype = "DEBUG";
	        break;
	    default:
	        $errtype = "WARNING";
	}

	throw new \Exception($errtype . " in " . $error_file . " in line " . $error_line . ": " . $error_message);
}

register_shutdown_function( "fatal_handler" );

function fatal_handler() {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if( $error !== NULL) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        throw new \Exception($errno . " in " . $errfile . " in line " . $errline . ": " . $errstr);
    }
}