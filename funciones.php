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
	
}

//obtengo los adjuntos a enviar
function getAttachment($db, $idEmail) {
	
}

//obetengo el password de email
function getPass($db, $email) {
	
}

//obetengo el usuario de email
function getUsuario($db, $email) {

}

//actualizao los emails enviados.
function updateEmailEnviado($db, $idEmail) {
	
}

?>
