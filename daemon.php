<?php
require('funciones.php');
require('claves.php');
require('myErrorHandler.php');

//ini_set('log_errors',TRUE);
//ini_set('error_log',$logfile);

// Primero creamos un proceso hijo
$pid = pcntl_fork();
if($pid == -1){
	$log = "Algo paso con el forking del proceso!";
	write_log($log, "ERROR1");
    die($log);
}

// Preguntamos si somos el proceso padre o el hijo recien construido
if($pid) {
    // Soy el padre por lo tanto necesito morir
    $log = "Proceso padre terminado";
	write_log($log, "INFO");
    exit($log."\n");
}

// De aqui en adelante solo se ejecuta si soy el hijo y futuro daemon
$log = "Demonio corriendo con pid ".getmypid();
write_log($log, "INFO");

// Lo siguiente que hacemos es soltarnos de la terminal de control
if (!posix_setsid()) {
 	$log = "No pude soltarme de la terminal";
	write_log($log, "ERROR2");
    exit_daemon ($log);
}

// De este punto en adelante debemos cambiarnos de directorio y 
// hacemos las recomendaciones de Wikipedia para un daemon
chdir("/");
umask(0);

// Aqui digo que hacer si recibo la señal de finalizacion (kill -15)
pcntl_signal(SIGTERM, "exit_daemon");

// Si estamos aqui oficialmente somos un daemon
// revisamos la ejecucion por cada linnea de codigo
declare(ticks = 1);
while(1) {
	$db = new mysqli($hostLocal,$usuarioLocal,$claveLocal,$esquemaLocal);
	
	if (!$db) {
		$log = "Error: No se pudo conectar a MySQL." . PHP_EOL ." - Error de depuracion: " . mysqli_connect_errno() . PHP_EOL;
		write_log($log, "ERROR3");
		exit_daemon($log);
	}

	$emailsAEnviar = getEmail($db);
	if (sizeof($emailsAEnviar) != 0) {
		foreach ($emailsAEnviar as $email) {
			$from = $email['from'];
			$pass = getPass($db, $from);
			$fromRepli = getUsuario($db, $from);
			
			$subject = $email['subject'];
			$bodymail = $email['body'];
			$address = $email['address'];
			$attachments = getAttachment($db, $email['id']);
			
			$log = "Enviando emails desde $from a $address";
			write_log($log, "INFO");
			if (envioMail($from, $pass, $fromRepli, $subject, $bodymail, $address, $attachments)) {
				updateEmailEnviado($db, $email['id']);
			} else {
				$log = "No se pudo enviar\n";
				write_log($log, "WARNING");
			}
		}
	} else {
		$log = "No hay mails para enviar";
		write_log($log, "INFO");
	}
	
	$db->close();
    sleep(60);
}

// Esta es mi funcion de salida
function exit_daemon($signo) {
	require('claves.php');
	$bodymail = "Alguien quiere que me vaya!, recibo la señal $signo";
	//error_log($bodymail,3,$logfile);
	envioMail($emailErrorSalida, $claveEmailSalida, "Sistemas", "Demonio Finalizado", $bodymail, $emailErrorEntrada, null);
    exit();
}
?>
