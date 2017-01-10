<?php include("PHPMailer_5.2.2/class.phpmailer.php"); 

function envioMail($username, $passw, $fromRepli, $subject, $bodymail, $address) {
	$fechamail=date("d/m/Y");
	$horamail=date("H:i");
	$mail=new PHPMailer();
	$mail->IsSMTP();							// telling the class to use SMTP
	$mail->Host="smtp.ospim.com.ar"; 			// SMTP server
	$mail->SMTPAuth=true;						// enable SMTP authentication
	$mail->Host="smtp.ospim.com.ar";			// sets the SMTP server
	$mail->Port=25;								// set the SMTP port for the GMAIL server
	$mail->Username=$username;			// SMTP account username
	$mail->Password=$passw;				// SMTP account password
	$mail->SetFrom($username, $fromRepli);
	$mail->AddReplyTo($username, $fromRepli);
	$mail->Subject=$subject;
	$mail->AltBody="Para ver este mensaje, por favor use un lector de correo compatible con HTML!"; // optional, comment out and test
	$bodymail.=" El dia ".$fechamail." a las ".$horamail.".";
	$mail->MsgHTML($bodymail);
	$nameto = "";
	$mail->AddAddress($address, $nameto);
	$mail->Send();	
}



?>
