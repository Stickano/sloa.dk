<?php

	@session_start();
	include("functions.php");
	include("connection.php");


	if(isset($_POST['okMail'])){

		#Klargør lidt blandet
		$error    = 0;
		$errorVal = array();
		$client   = mysqli_real_escape_string($conn,clientIP());
		$time     = mysqli_real_escape_string($conn,timeMe());

		$mail = htmlspecialchars(strip_tags(addslashes($_POST['mail'])));
		$subject = htmlspecialchars(strip_tags(addslashes($_POST['subject'])));
		$txt = htmlspecialchars(addslashes($_POST['txt']));

		#Ekstra check - må ikke være tom, skal være mail yada yada
		if(empty($mail) || empty($subject) || empty($txt)){
			$error++;
			$errorVal[] = "Udfyld venligst alle felter.";
		};
		
		if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
			$error++;
			$errorVal[] = "Ugyldig E-mail anvendt.";
		};


		#Hvis kriterierne ikke er godtaget
		if($error != 0){
			$_SESSION['mailError'] = $errorVal;
			header("location:../kontakt.php");

		#Hvis alt er som det skal være
		}else{

			#Hent hvilke mail der skal sendes til
			$sql = "SELECT mail_to,contacted FROM contact WHERE id=1";
			$query = mysqli_query($conn,$sql)or die(mysqli_error($conn));
			$result = mysqli_fetch_array($query);

			#Skaber mailen
			$to = $result['mail_to'];
			$subject = "Der er lyd fra sloa.dk";
			$headers = 'From: noreply@sloa.dk'."\r\n" .
						"Content-type: text/plain; charset=utf-8"."\r\n";
			$message =
"
Besked fra ".$mail."

".$subject."
".nl2br(stripslashes($txt));

			#Forsøg at send mail, og opret log alt an' på udfaldet
			if(mail($to, $subject, $message, $headers)){
				$_SESSION['mailSendt'] = true;

				#Opdater hvor mange gange mail funktionen er blevet anvendt
				$used = $result['contacted']+1;
				$sql = "UPDATE contact SET contacted='".$used."' WHERE id=1";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));

				$sql = "INSERT INTO events (ip,event,time,rel) VALUES ('".$client."','Kontakt forumlaen blev anvendt','".$time."','kontakt')";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			}else{
				$_SESSION['mailError'] = "<b>Der skete en fejl</b> kontakt mig direkte på info@sloa.dk mens jeg kigger på det";
				$sql = "INSERT INTO events (ip,event,time,rel,danger) VALUES ('".$client."','Kontakt funktion mislykkedes','".$time."','kontakt',1)";
				mysqli_query($conn,$sql)or die(mysqli_error($conn));
			};

			header("location:../kontakt.php");

		};

	};

?>