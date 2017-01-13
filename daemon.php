<?php
require('funciones.php');
require('claves.php');
require('myErrorHandler.php');

// Primero creamos un proceso hijo
$pid = pcntl_fork();
if($pid == -1){
	$log = "Algo paso con el forking del proceso!";
	write_log($log, "ERROR");
    die($log."\n");
}

// Preguntamos si somos el proceso padre o el hijo recien construido
if($pid) {
    // Soy el padre por lo tanto necesito morir
    $log = "Proceso padre terminado";
	write_log($log, "INFO");
    exit();
}

// De aqui en adelante solo se ejecuta si soy el hijo y futuro daemon
$log = "Demonio corriendo con pid ".getmypid();
echo $log."\n";
write_log($log, "INFO");

// Lo siguiente que hacemos es soltarnos de la terminal de control
if (!posix_setsid()) {
 	$log = "No pude soltarme de la terminal";
	write_log($log, "ERROR");
    exit_daemon ($log);
}


// Aqui digo que hacer si recibo la señal de finalizacion (kill -15)
pcntl_signal(SIGTERM, "exit_daemon");

// Si estamos aqui oficialmente somos un daemon
// revisamos la ejecucion por cada linnea de codigo
declare(ticks = 1);
while(1) {
	$db = new mysqli($hostLocal,$usuarioLocal,$claveLocal,$esquemaLocal);
	if (!$db) {
		$log = "Error: No se pudo conectar a MySQL." . PHP_EOL ." - Error de depuracion: " . mysqli_connect_errno() . PHP_EOL;
		write_log($log, "ERROR");
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
			
			if (envioMail($from, $pass, $fromRepli, $subject, $bodymail, $address, $attachments)) {
				updateEmailEnviado($db, $email['id']);
				$log = "ID: ".$email['id']." - Se Envió email desde $from a $address";
				write_log($log, "INFO");
			} else {
				$log = "ID: ".$email['id']." - No se pudo Enviar email desde $from a $address";
				write_log($log, "WARNING");
			}
			
		}
	} else {
		$log = "No hay mails para enviar";
		write_log($log, "INFO");
	}
	
	$db->close();
    sleep(900);
}

// Esta es mi funcion de salida
function exit_daemon($signo) {
	require('claves.php');
	$bodymail = "Alguien quiere que me vaya!, recibo la señal $signo";
	envioMail($emailErrorSalida, $claveEmailSalida, "Sistemas", "Demonio Finalizado", $bodymail, $emailErrorEntrada, null);
	write_log($bodymail,"FINALIZADO");
    exit();
}
?>
