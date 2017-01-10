<?php include("PHPMailer_5.2.2/class.phpmailer.php"); 

//realiza el envio real
function envioMail($from, $passw, $fromRepli, $subject, $bodymail, $address, $attachments) {
	$fechamail=date("d/m/Y");
	$horamail=date("H:i");
	$mail=new PHPMailer();
	$mail->IsSMTP();					// telling the class to use SMTP
	$mail->Host="smtp.ospim.com.ar"; 	// SMTP server
	$mail->SMTPAuth=true;				// enable SMTP authentication
	$mail->Host="smtp.ospim.com.ar";	// sets the SMTP server
	$mail->Port=25;						// set the SMTP port for the GMAIL server
	$mail->Username=$username;			// SMTP account username
	$mail->Password=$passw;				// SMTP account password
	$mail->SetFrom($username, $fromRepli);
	$mail->AddReplyTo($username, $fromRepli);
	$mail->Subject=$subject;
	$bodymail.=" El dia ".$fechamail." a las ".$horamail.".";
	$mail->MsgHTML($bodymail);
	$nameto = "";
	$mail->AddAddress($address, $nameto);
	/*foreach($attachments as $attachment) {
		$mail->AddAttachment($attachment);
	}*/
	$mail->Send();	
}

//obtengo los emails a enviar
function getEmail($db) {
	$arrayEmails = array();
	$sqlGetEmail = "SELECT * FROM bandejasalida WHERE enviado = 0";
	$resGetEmail = mysql_query($sqlGetEmail,$db);
	while ($rowGetEmail = mysql_fetch_assoc($resGetEmail)) {
		$arrayEmails[$rowGetEmail['id']] = $rowGetEmail;
	}
	return $arrayEmails;
}

//obtengo los adjuntos a enviar
function getAttachment($db, $idEmail) {
	$arrayAttachment = array();
	return $arrayAttachment;
}

//obetengo el password de email
function getPass($db, $email) {
	$sqlGetPass = "SELECT password FROM emails WHERE email = '$email'";
	$resGetPass = mysql_query($sqlGetPass,$db);
	$rowGetPass = mysql_fetch_assoc($resGetPass);
	return $rowGetPass['password'];
}

//obetengo el usuario de email
function getUsuario($db, $email) {
	$sqlGetNombre = "SELECT u.nombre FROM usuarios u, emails e WHERE e.email = '$email' and e.idusuario = u.id";
	$resGetNombre = mysql_query($sqlGetNombre,$db);
	$rowGetNombre = mysql_fetch_assoc($resGetNombre);
	return $rowGetNombre['nombre'];
}

//actualizao los emails enviados.
function updateEmailEnviado($db, $idEmail, $hostname, $dbname, $usuario, $clave) {
	$fechaenvio = date ( "Y-m-d H:i:s" );
	$sqlUpdateEnvio = "UPDATE FROM bandejasalida SET enviado = 1, fechaenvio = '$fechaenvio' WHERE id = $idEmail";
	try {
		$hostname = $_SESSION ['host'];
		$dbname = $_SESSION ['dbname'];
		$dbh = new PDO ( "mysql:host=$hostname;dbname=$dbname", $usuario, $clave );
		$dbh->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$dbh->beginTransaction();
		$dbh->exec ( $sqlUpdateEnvio );
		$dbh->commit ();
		return 0;
	} catch ( PDOException $e ) {
		echo $e->getMessage ();
		$dbh->rollback ();
		return -1;
	}
}

?>
