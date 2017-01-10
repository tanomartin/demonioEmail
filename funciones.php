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
	$mail->Username=$from;			// SMTP account username
	$mail->Password=$passw;				// SMTP account password
	$mail->SetFrom($from, $fromRepli);
	$mail->AddReplyTo($from, $fromRepli);
	$mail->Subject=$subject;
	$bodymail.=" El dia ".$fechamail." a las ".$horamail.".";
	$mail->MsgHTML($bodymail);
	$nameto = "";
	$mail->AddAddress($address, $nameto);
	foreach($attachments as $attachment) {
		//CONTROLO EL ADJUNTO???
		$mail->AddAttachment($attachment['adjunto']);
	}
  	return $mail->Send();
}

//obtengo los emails a enviar
function getEmail($db) {
	$arrayEmails = array();
	$sqlGetEmail = "SELECT * FROM bandejasalida WHERE enviado = 0";
	$resGetEmail = $db->query($sqlGetEmail);
	if ($resGetEmail) {
		while ($rowGetEmail = $resGetEmail->fetch_assoc()) {
			$arrayEmails[] = $rowGetEmail;
		}
		$resGetEmail->close();
	}
	return $arrayEmails;
}

//obtengo los adjuntos a enviar
function getAttachment($db, $idEmail) {
	$arrayAttachment = array();
	$sqlGetAttachment = "SELECT * FROM bandejasalidaadjuntos WHERE idemail = $idEmail";
	$resGetAttachment = $db->query($sqlGetAttachment);
	if ($resGetAttachment) {
		while ($rowGetAttachment = $resGetAttachment->fetch_assoc()) {
			$arrayAttachment[] = $rowGetAttachment;
		}
		$resGetAttachment->close();
	}
	return $arrayAttachment;
}

//obetengo el password de email
function getPass($db, $email) {
	$sqlGetPass = "SELECT password FROM emails WHERE email like '$email'";
	$resGetPass = $db->query($sqlGetPass);
	if ($resGetPass) {
		$rowGetPass = $resGetPass->fetch_assoc();
		$resGetPass->close();
	}
	return $rowGetPass['password'];
}

//obetengo el usuario de email
function getUsuario($db, $email) {
	$sqlGetNombre = "SELECT u.nombre FROM usuarios u, emails e WHERE e.email = '$email' and e.idusuario = u.id";
	$resGetNombre = $db->query($sqlGetNombre);
	if ($resGetNombre) {
		$rowGetNombre = $resGetNombre->fetch_assoc();
		$resGetNombre->close();
	}
	return $rowGetNombre['nombre'];
}

//actualizao los emails enviados.
function updateEmailEnviado($db, $idEmail) {
	$fechaenvio = date ( "Y-m-d H:i:s" );
	$sqlUpdateEnvio = "UPDATE bandejasalida SET enviado = 1, fechaenvio = '$fechaenvio' WHERE id = $idEmail";
	$db->query($sqlUpdateEnvio);
}

?>