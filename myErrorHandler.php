<?php
set_error_handler('myErrorHandler');
register_shutdown_function('fatalErrorShutdownHandler');

function myErrorHandler($code, $message, $file, $line) {
	exit_daemon($message);
}

function fatalErrorShutdownHandler() {
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		write_log($last_error['message'],"ERROR");
		myErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}
?>