<?php

include_once('envioMailGeneral.php');
// Simple demonio escrito en PHP parte 2
 
// Primero creamos un proceso hijo

$pid = pcntl_fork();
if($pid == -1){
    die("Algo pasó con el forking del proceso!");
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

// Aquí digo que hacer si recibo la señal de finalización (kill -15)
pcntl_signal(SIGTERM, "exit_daemon");

// Si estamos aqui oficialmente somos un daemon

// revisamos la ejecución por cada línea de código
declare(ticks = 1);
global $db;
while(1) {
	$db = mysqli_connect("cronos","sistemas", "blam7326","madera");
	if (!$db) {
		echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
		echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
		echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}

    $date = date("h:i:s");
    echo "$date hola amigo, te saluda el daemon que envia mail!\n";
    $username = "sistemas@ospim.com.ar";
    $passw = "pepepascual";
    $fromRepli = "Sistemas";
    $subject = "Prueba de envio";
    $bodymail = "<body><br><br>Este es un mensaje de Aviso.<br><br>Prueba desde el demonio";
    $address = "mmzucchiatti@usimra.com.ar";
    envioMail($username, $passw, $fromRepli, $subject, $bodymail, $address);
	
	mysqli_close($db);
	
    sleep(30);
}

// Esta es mi función de salida
function exit_daemon($signo) {
    echo "Alguien quiere que me vaya!, recibo la señal $signo\n";
    exit("daemon terminado!\n");
}
?>
