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
	$mail->Username=$from;				// SMTP account username
	$mail->Password=$passw;				// SMTP account password
	$mail->SetFrom($from, $fromRepli);
	$mail->AddReplyTo($from, $fromRepli);
	$mail->Subject=$subject;
	$bodymail.=" Correo enviado el dia ".$fechamail." a las ".$horamail.".";
	$mail->MsgHTML($bodymail);
	
	$pos = strpos($address, ";");
	if ($pos === false) {
		$mail->AddAddress($address);
	} else {
		$arrayAddres = explode(";",$address);
		foreach ($arrayAddres as $emailTo) {
			$mail->AddBCC($emailTo);
		}
	}
	
	if ($attachments != null) {
		foreach($attachments as $attachment) {
			if (file_exists($attachment['adjunto'])) {
				$mail->AddAttachment($attachment['adjunto']);
			} else {
				return 2;
			}
		}
	}
	
	if(!$mail->Send()) {
		return 1;
	}
	return 0;
}

//obtengo los emails a enviar
function getEmail($db) {
	$arrayEmails = array();
	$sqlGetEmail = "SELECT * FROM bandejasalida";
	$resGetEmail = $db->query($sqlGetEmail);
	if ($resGetEmail) {
		while ($rowGetEmail = $resGetEmail->fetch_assoc()) {
			$arrayEmails[] = $rowGetEmail;
		}
		$resGetEmail->close();
	} else {
		 throw new Exception($db->error);
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
	} else {
		 throw new Exception($db->error);
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
	} else {
		 throw new Exception($db->error);
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
	} else {
		 throw new Exception($db->error);
	}
	return $rowGetNombre['nombre'];
}

//paso mail de salida a enviado.
function pasarBandejaEnviados($db, $email) {
	$fechaenvio = date ( "Y-m-d H:i:s" );
	$sqlInsertEnviados = "INSERT INTO bandejaenviados VALUES(".$email['id'].",'".$email['from']."','".$email['subject']."','".$email['body']."','".$email['address']."','".$fechaenvio."')";
	$sqlDeleteSalida = "DELETE FROM bandejasalida WHERE id = ".$email['id'];
	$resInsertEnviados = $db->query($sqlInsertEnviados);
	if (!$resInsertEnviados) {
		throw new Exception($db->error);
	}
	$resDeleteSalida = $db->query($sqlDeleteSalida);
	if (!$resDeleteSalida) {
		throw new Exception($db->error);
	}
}

function write_log($cadena,$tipo) {
	require('claves.php');
	$arch = fopen($logfile, "a+");
	fwrite($arch, "[".date("Y-m-d H:i:s")." - $tipo ] ".$cadena."\n");
	fclose($arch);
}

?>