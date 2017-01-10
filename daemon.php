<?php

include_once('funciones.php');
include_once('claves.php');

// Primero creamos un proceso hijo
/*$pid = pcntl_fork();
if($pid == -1){
    die("Algo paso con el forking del proceso!");
}

// Preguntamos si somos el proceso padre o el hijo recien construido
if($pid) {
    // Soy el padre por lo tanto necesito morir
    exit("Proceso padre terminado...\n");
}

// De aqui en adelante solo se ejecuta si soy el hijo y futuro daemon

// Lo siguiente que hacemos es soltarnos de la terminal de control
if (!posix_setsid()) {
    die ("No pude soltarme de la terminal");
}

// De este punto en adelante debemos cambiarnos de directorio y 
// hacemos las recomendaciones de Wikipedia para un daemon
chdir("/");
umask(0);

// Aqui digo que hacer si recibo la señal de finalizacion (kill -15)
pcntl_signal(SIGTERM, "exit_daemon");*/

// Si estamos aqui oficialmente somos un daemon
// revisamos la ejecucion por cada linnea de codigo
declare(ticks = 1);
while(1) {
	$db = mysqli_connect($hostLocal,$usuarioLocal,$claveLocal,$esquemaLocal);
	
	if (!$db) {
		echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
		echo "Error de depuracion: " . mysqli_connect_errno() . PHP_EOL;
		exit;
	}

	$emailsAEnviar = getEmail($db);
	if (sizeof($emailsAEnviar) != 0) {
		foreach ($emailsAEnviar as $email) {
			$from = $email['from'];
			$pass = getPass($db, $username);
			$fromRepli = getUsuario($db, $username);
			$subject = $email['subject'];
			$bodymail = $email['body'];
			$address = $email['address'];
			$attachments = getAttachment($db, $email['id']);
			envioMail($from, $pass, $fromRepli, $subject, $bodymail, $address, $attachments);
			if (updateEmailEnviado($db, $email['id']) == -1) {
				this.exit_daemon("exit_daemon");
			} else {
				echo "Se envio el mail id ".$email['id']."<br>";
			}
		}
	} else {
		echo "No hay mails para enviar<br>";
	}
	
	mysqli_close($db);
    sleep(900);
}

// Esta es mi funcion de salida
function exit_daemon($signo) {
    echo "Alguien quiere que me vaya!, recibo la señal $signo\n";
    exit("daemon terminado!\n");
}
?>
